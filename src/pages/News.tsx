import { useState } from 'react';
import { Clock, User, Eye, Heart, Share, Search, Filter } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';

const News = () => {
  const [selectedCategory, setSelectedCategory] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  const categories = ['Football', 'Basketball', 'Tennis', 'Swimming', 'General', 'Tournaments'];

  const newsArticles = [
    {
      id: 1,
      title: 'Champions League Final Set for Next Month',
      excerpt: 'The most anticipated football match of the year is approaching. Two powerhouse teams will clash in an epic showdown that promises to deliver spectacular football.',
      content: 'Full article content here...',
      author: 'John Smith',
      date: '2024-01-15',
      time: '2 hours ago',
      category: 'Football',
      image: '⚽',
      views: 1250,
      likes: 89,
      featured: true
    },
    {
      id: 2,
      title: 'Tennis Tournament Registration Now Open',
      excerpt: 'Local tennis championship accepting registrations. Prize pool of $50,000 awaits the winners in this prestigious tournament.',
      content: 'Full article content here...',
      author: 'Sarah Johnson',
      date: '2024-01-14',
      time: '4 hours ago',
      category: 'Tennis',
      image: '🎾',
      views: 892,
      likes: 67,
      featured: false
    },
    {
      id: 3,
      title: 'New Basketball Complex Opens Downtown',
      excerpt: 'State-of-the-art facility with 6 courts now available for booking. Professional training equipment included.',
      content: 'Full article content here...',
      author: 'Mike Wilson',
      date: '2024-01-13',
      time: '6 hours ago',
      category: 'Basketball',
      image: '🏀',
      views: 743,
      likes: 54,
      featured: true
    },
    {
      id: 4,
      title: 'Swimming Pool Maintenance Complete',
      excerpt: 'Olympic-sized pool reopens with improved filtration systems and new timing equipment for competitive training.',
      content: 'Full article content here...',
      author: 'Lisa Brown',
      date: '2024-01-12',
      time: '8 hours ago',
      category: 'Swimming',
      image: '🏊',
      views: 567,
      likes: 43,
      featured: false
    },
    {
      id: 5,
      title: 'Youth Sports Program Launches',
      excerpt: 'New initiative to encourage young athletes with free coaching sessions and equipment loans.',
      content: 'Full article content here...',
      author: 'David Chen',
      date: '2024-01-11',
      time: '1 day ago',
      category: 'General',
      image: '🏆',
      views: 923,
      likes: 78,
      featured: false
    },
    {
      id: 6,
      title: 'Annual Sports Awards Ceremony',
      excerpt: 'Celebrating the best athletes and coaches of the year. Awards ceremony scheduled for next weekend.',
      content: 'Full article content here...',
      author: 'Emma Davis',
      date: '2024-01-10',
      time: '2 days ago',
      category: 'General',
      image: '🏅',
      views: 654,
      likes: 32,
      featured: false
    }
  ];

  const filteredNews = newsArticles.filter(article => {
    if (selectedCategory && selectedCategory !== 'all-categories' && article.category !== selectedCategory) return false;
    if (searchQuery && !article.title.toLowerCase().includes(searchQuery.toLowerCase()) && 
        !article.excerpt.toLowerCase().includes(searchQuery.toLowerCase())) return false;
    return true;
  });

  const featuredNews = newsArticles.filter(article => article.featured);
  const regularNews = filteredNews.filter(article => !article.featured);

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div className="bg-gradient-hero py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
            Sports News & Updates
          </h1>
          <p className="text-xl text-primary-foreground/90 max-w-2xl mx-auto">
            Stay informed with the latest news, events, and updates from the sports world
          </p>
        </div>
      </div>

      {/* Search & Filter Section */}
      <div className="container mx-auto px-4 -mt-8 relative z-10">
        <Card className="shadow-strong bg-card/95 backdrop-blur-sm">
          <CardContent className="p-6">
            <div className="grid md:grid-cols-3 gap-4">
              <div>
                <Input
                  placeholder="Search news..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="w-full"
                />
              </div>
              <div>
                <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                  <SelectTrigger>
                    <SelectValue placeholder="All categories" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all-categories">All Categories</SelectItem>
                    {categories.map((category) => (
                      <SelectItem key={category} value={category}>
                        {category}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Button className="w-full bg-gradient-primary">
                  <Search className="mr-2 h-4 w-4" />
                  Search
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="container mx-auto px-4 py-12">
        {/* Featured News */}
        {featuredNews.length > 0 && (
          <div className="mb-12">
            <h2 className="text-2xl font-bold text-foreground mb-6">Featured Stories</h2>
            <div className="grid md:grid-cols-2 gap-8">
              {featuredNews.map((article) => (
                <Card key={article.id} className="group hover:shadow-medium transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                  <CardHeader className="pb-4">
                    <div className="flex items-start justify-between mb-4">
                      <Badge className="bg-primary text-primary-foreground">Featured</Badge>
                      <Badge variant="secondary" className="bg-primary/10 text-primary">
                        {article.category}
                      </Badge>
                    </div>
                    <div className="text-center mb-4">
                      <div className="text-6xl mb-2">{article.image}</div>
                    </div>
                    <CardTitle className="text-xl text-card-foreground group-hover:text-primary transition-colors">
                      {article.title}
                    </CardTitle>
                  </CardHeader>
                  <CardContent>
                    <p className="text-muted-foreground mb-4 line-clamp-3">
                      {article.excerpt}
                    </p>
                    
                    <div className="flex items-center justify-between text-sm text-muted-foreground mb-4">
                      <div className="flex items-center gap-4">
                        <div className="flex items-center gap-1">
                          <User className="h-4 w-4" />
                          <span>{article.author}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Clock className="h-4 w-4" />
                          <span>{article.time}</span>
                        </div>
                      </div>
                    </div>

                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-4 text-sm text-muted-foreground">
                        <div className="flex items-center gap-1">
                          <Eye className="h-4 w-4" />
                          <span>{article.views}</span>
                        </div>
                        <div className="flex items-center gap-1">
                          <Heart className="h-4 w-4" />
                          <span>{article.likes}</span>
                        </div>
                      </div>
                      <div className="flex gap-2">
                        <Button size="icon" variant="outline" className="h-8 w-8">
                          <Heart className="h-4 w-4" />
                        </Button>
                        <Button size="icon" variant="outline" className="h-8 w-8">
                          <Share className="h-4 w-4" />
                        </Button>
                        <Button size="sm" className="bg-gradient-primary">
                          Read More
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}

        {/* Regular News */}
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-2xl font-bold text-foreground">
            Latest News ({filteredNews.length})
          </h2>
          <Button variant="outline" size="sm">
            <Filter className="mr-2 h-4 w-4" />
            Sort By
          </Button>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {regularNews.map((article) => (
            <Card key={article.id} className="group hover:shadow-medium transition-all duration-300 hover:-translate-y-1">
              <CardHeader className="pb-4">
                <div className="flex items-center justify-between mb-4">
                  <Badge variant="secondary" className="bg-primary/10 text-primary text-xs">
                    {article.category}
                  </Badge>
                  <span className="text-xs text-muted-foreground">{article.time}</span>
                </div>
                <div className="text-center mb-4">
                  <div className="text-4xl mb-2">{article.image}</div>
                </div>
                <CardTitle className="text-lg text-card-foreground group-hover:text-primary transition-colors line-clamp-2">
                  {article.title}
                </CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground mb-4 text-sm line-clamp-3">
                  {article.excerpt}
                </p>
                
                <div className="flex items-center justify-between text-xs text-muted-foreground mb-4">
                  <div className="flex items-center gap-1">
                    <User className="h-3 w-3" />
                    <span>{article.author}</span>
                  </div>
                  <div className="flex items-center gap-3">
                    <div className="flex items-center gap-1">
                      <Eye className="h-3 w-3" />
                      <span>{article.views}</span>
                    </div>
                    <div className="flex items-center gap-1">
                      <Heart className="h-3 w-3" />
                      <span>{article.likes}</span>
                    </div>
                  </div>
                </div>

                <div className="flex gap-2">
                  <Button size="icon" variant="outline" className="h-7 w-7 flex-shrink-0">
                    <Heart className="h-3 w-3" />
                  </Button>
                  <Button size="icon" variant="outline" className="h-7 w-7 flex-shrink-0">
                    <Share className="h-3 w-3" />
                  </Button>
                  <Button size="sm" className="flex-1 bg-gradient-primary text-xs">
                    Read More
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {filteredNews.length === 0 && (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">📰</div>
            <h3 className="text-xl font-bold text-card-foreground mb-2">No news found</h3>
            <p className="text-muted-foreground">Try adjusting your search or filters</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default News;