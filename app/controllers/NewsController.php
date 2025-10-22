<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\News;

/**
 * News Controller
 * 
 * Handles news and articles with full database integration
 */
class NewsController extends BaseController
{
    private News $newsModel;

    public function __construct()
    {
        $this->newsModel = new News();
    }

    /**
     * Display all news with pagination and filtering
     */
    public function index(Request $request): Response
    {
        try {
            // Get query parameters
            $page = max(1, (int)$request->getParam('page', 1));
            $category = $request->getParam('category', 'all');
            $search = $request->getParam('q', '');
            $limit = 9; // News per page
            
            // Get published news based on filters
            if (!empty($search)) {
                $news = $this->newsModel->search($search);
            } elseif ($category && $category !== 'all') {
                $news = $this->newsModel->getByCategory($category);
            } else {
                $news = $this->newsModel->getPublished();
            }

            // Sort by published_at desc
            usort($news, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            // Get featured news for hero section (exclude from category filter)
            $allNews = $this->newsModel->getPublished();
            $featuredNews = $this->newsModel->getFeatured(1);
            $featured = $featuredNews[0] ?? null;

            // Pagination
            $total = count($news);
            $totalPages = ceil($total / $limit);
            $offset = ($page - 1) * $limit;
            $paginatedNews = array_slice($news, $offset, $limit);

            // Get categories for filter buttons
            $categories = $this->getNewsCategories();

            // Prepare view data
            $data = [
                'news' => $paginatedNews,
                'featured' => $featured,
                'categories' => $categories,
                'currentCategory' => $category,
                'search' => $search,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'has_prev' => $page > 1,
                    'has_next' => $page < $totalPages,
                    'prev_page' => $page - 1,
                    'next_page' => $page + 1
                ],
                'total' => $total
            ];

            return $this->view('news.index', $data);

        } catch (\Exception $e) {
            error_log("News index error: " . $e->getMessage());
            return $this->view('errors.500', [
                'error' => 'Unable to load news. Please try again later.'
            ]);
        }
    }

    /**
     * AJAX Real-time search endpoint
     */
    public function liveSearch(Request $request): Response
    {
        try {
            $query = trim($request->getParam('q', ''));
            $category = $request->getParam('category', 'all');
            $limit = min(20, max(1, (int)$request->getParam('limit', 10)));

            if (empty($query)) {
                // If no query, return by category or all
                if ($category && $category !== 'all') {
                    $news = $this->newsModel->getByCategory($category);
                } else {
                    $news = $this->newsModel->getPublished();
                }
            } else {
                // Search with query
                $news = $this->newsModel->search($query);
                
                // Further filter by category if specified
                if ($category && $category !== 'all') {
                    $news = array_filter($news, function($article) use ($category) {
                        return strcasecmp($article['category'], $category) === 0;
                    });
                }
            }

            // Sort by date
            usort($news, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            // Limit results
            $news = array_slice($news, 0, $limit);

            // Format for JSON response
            $formattedNews = array_map(function($article) {
                return [
                    'id' => $article['id'],
                    'title' => $article['title'],
                    'slug' => $article['slug'],
                    'excerpt' => $article['excerpt'] ?? '',
                    'category' => $article['category'],
                    'featured_image' => $article['featured_image'] ?? '/public/assets/images/default-news.jpg',
                    'published_at' => $article['published_at'],
                    'views' => $article['views'] ?? 0,
                    'formatted_date' => $this->formatPublishedDate($article['published_at']),
                    'url' => '/news/' . $article['slug']
                ];
            }, $news);

            return $this->json([
                'success' => true,
                'news' => $formattedNews,
                'count' => count($formattedNews),
                'query' => $query,
                'category' => $category
            ]);

        } catch (\Exception $e) {
            error_log("Live search error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display specific news article
     */
    public function show(Request $request): Response
    {
        try {
            $slug = $request->getParam('slug');
            
            if (!$slug) {
                return $this->view('errors.404', [
                    'message' => 'News article not found'
                ]);
            }

            // Get news by slug
            $news = $this->newsModel->getBySlug($slug);
            
            if (!$news || $news['status'] !== 'published') {
                return $this->view('errors.404', [
                    'message' => 'News article not found or not available'
                ]);
            }

            // Increment view count
            $this->newsModel->incrementViews($news['id']);
            $news['views'] = ($news['views'] ?? 0) + 1;

            // Get related news
            $relatedNews = $this->newsModel->getRelated($news['id'], 4);

            // Get author information
            $author = null;
            if (!empty($news['author_id'])) {
                $author = $this->getUserById($news['author_id']);
            }

            // Format published date and calculate reading time
            $news['formatted_date'] = $this->formatPublishedDate($news['published_at']);
            $news['reading_time'] = $this->calculateReadingTime($news['content']);

            $data = [
                'news' => $news,
                'author' => $author,
                'relatedNews' => $relatedNews
            ];

            return $this->view('news.detail', $data);

        } catch (\Exception $e) {
            error_log("News detail error: " . $e->getMessage());
            return $this->view('errors.500', [
                'error' => 'Unable to load news article. Please try again later.'
            ]);
        }
    }

    /**
     * Get user information by ID
     */
    private function getUserById(?int $userId): ?array
    {
        if (!$userId) {
            return null;
        }
        
        try {
            $sql = "SELECT id, first_name, last_name, profile_picture FROM users WHERE id = ?";
            $db = \Core\Database::getInstance();
            $result = $db->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
            return $result[0] ?? null;
        } catch (\Exception $e) {
            error_log("Failed to get user: " . $e->getMessage());
            return null;
        }
    }

    /**
     * AJAX endpoint for loading more news
     */
    public function loadMore(Request $request): Response
    {
        try {
            $page = max(1, (int)$request->getParam('page', 1));
            $category = $request->getParam('category', 'all');
            $limit = 6;

            if ($category && $category !== 'all') {
                $news = $this->newsModel->getByCategory($category);
            } else {
                $news = $this->newsModel->getPublished();
            }

            // Sort and paginate
            usort($news, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            $offset = ($page - 1) * $limit;
            $paginatedNews = array_slice($news, $offset, $limit);

            // Format news for response
            foreach ($paginatedNews as &$article) {
                $article['formatted_date'] = $this->formatPublishedDate($article['published_at']);
                $article['reading_time'] = $this->calculateReadingTime($article['content']);
                $article['url'] = '/news/' . $article['slug'];
            }

            return $this->json([
                'success' => true,
                'news' => $paginatedNews,
                'hasMore' => (count($news) > $offset + $limit)
            ]);

        } catch (\Exception $e) {
            error_log("Load more news error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => 'Unable to load more news'
            ], 500);
        }
    }

    /**
     * Search news articles
     */
    public function search(Request $request): Response
    {
        try {
            $query = trim($request->getParam('q', ''));
            
            if (empty($query)) {
                return $this->redirect('/news');
            }

            $news = $this->newsModel->search($query);
            
            // Sort by relevance and date
            usort($news, function($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            $data = [
                'news' => $news,
                'query' => $query,
                'count' => count($news),
                'categories' => $this->getNewsCategories()
            ];

            return $this->view('news.search', $data);

        } catch (\Exception $e) {
            error_log("News search error: " . $e->getMessage());
            return $this->view('errors.500', [
                'error' => 'Search temporarily unavailable. Please try again later.'
            ]);
        }
    }

    /**
     * Get trending news endpoint
     */
    public function trending(Request $request): Response
    {
        try {
            $limit = min(20, max(1, (int)$request->getParam('limit', 10)));
            $trending = $this->newsModel->getTrending($limit);

            return $this->json([
                'success' => true,
                'trending' => $trending
            ]);

        } catch (\Exception $e) {
            error_log("Trending news error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => 'Unable to load trending news'
            ], 500);
        }
    }

    /**
     * Get news categories for filtering
     */
    private function getNewsCategories(): array
    {
        return [
            'all' => 'All Sports',
            'Cricket' => 'Cricket',
            'Football' => 'Football', 
            'Basketball' => 'Basketball',
            'Tennis' => 'Tennis',
            'Swimming' => 'Swimming',
            'General Sports' => 'General Sports'
        ];
    }

    /**
     * Format published date for display
     */
    private function formatPublishedDate(string $date): string
    {
        $publishedTime = strtotime($date);
        $now = time();
        $diff = $now - $publishedTime;

        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes <= 1 ? '1 minute ago' : $minutes . ' minutes ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours == 1 ? '1 hour ago' : $hours . ' hours ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days == 1 ? '1 day ago' : $days . ' days ago';
        } else {
            return date('M j, Y', $publishedTime);
        }
    }
    
    /**
     * Calculate estimated reading time
     */
    private function calculateReadingTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return $minutes . ' min read';
    }
}