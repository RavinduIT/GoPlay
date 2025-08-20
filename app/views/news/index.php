<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Sports News</h1>
        <p class="text-lg text-gray-600">Stay updated with the latest sports news and updates</p>
    </div>
    
    <!-- Featured Article -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <img src="/public/assets/images/news/featured.jpg" alt="Featured News" class="w-full h-64 lg:h-full object-cover">
            <div class="p-8">
                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">BREAKING</span>
                <h2 class="text-2xl font-bold text-gray-800 mt-4 mb-3">India Wins Cricket World Cup 2024</h2>
                <p class="text-gray-600 mb-4">
                    In a thrilling final match, Team India defeated Australia by 8 wickets to claim the Cricket World Cup 2024. 
                    The victory comes after a spectacular performance by the entire team...
                </p>
                <div class="flex items-center text-sm text-gray-500 mb-4">
                    <span>Sports Desk</span>
                    <span class="mx-2">•</span>
                    <span>2 hours ago</span>
                    <span class="mx-2">•</span>
                    <span>5 min read</span>
                </div>
                <a href="/news/cricket-world-cup-2024" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Read More
                </a>
            </div>
        </div>
    </div>
    
    <!-- News Categories -->
    <div class="flex flex-wrap justify-center mb-8">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg mx-2 mb-2">All</button>
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mx-2 mb-2 hover:bg-gray-300">Cricket</button>
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mx-2 mb-2 hover:bg-gray-300">Football</button>
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mx-2 mb-2 hover:bg-gray-300">Basketball</button>
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mx-2 mb-2 hover:bg-gray-300">Tennis</button>
    </div>
    
    <!-- News Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- News Article Card -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <img src="/public/assets/images/news/article1.jpg" alt="News" class="w-full h-48 object-cover">
            <div class="p-6">
                <span class="text-blue-600 text-sm font-semibold">CRICKET</span>
                <h3 class="text-xl font-bold text-gray-800 mt-2 mb-3">IPL 2024 Season to Begin Next Month</h3>
                <p class="text-gray-600 mb-4">
                    The Indian Premier League 2024 season is set to commence next month with exciting new additions...
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>John Doe</span>
                    <span>6 hours ago</span>
                </div>
                <a href="/news/ipl-2024-season" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
            </div>
        </article>
        
        <!-- More news articles would be generated dynamically -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <img src="/public/assets/images/news/article2.jpg" alt="News" class="w-full h-48 object-cover">
            <div class="p-6">
                <span class="text-green-600 text-sm font-semibold">FOOTBALL</span>
                <h3 class="text-xl font-bold text-gray-800 mt-2 mb-3">Champions League Finals Preview</h3>
                <p class="text-gray-600 mb-4">
                    A comprehensive preview of the upcoming Champions League finals featuring the top teams...
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>Jane Smith</span>
                    <span>1 day ago</span>
                </div>
                <a href="/news/champions-league-finals" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
            </div>
        </article>
        
        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <img src="/public/assets/images/news/article3.jpg" alt="News" class="w-full h-48 object-cover">
            <div class="p-6">
                <span class="text-purple-600 text-sm font-semibold">BASKETBALL</span>
                <h3 class="text-xl font-bold text-gray-800 mt-2 mb-3">NBA Playoffs Heat Up</h3>
                <p class="text-gray-600 mb-4">
                    The NBA playoffs are reaching their climax with thrilling matches and unexpected outcomes...
                </p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span>Mike Johnson</span>
                    <span>2 days ago</span>
                </div>
                <a href="/news/nba-playoffs" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
            </div>
        </article>
    </div>
    
    <!-- Load More Button -->
    <div class="text-center mt-12">
        <button class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
            Load More News
        </button>
    </div>
</div>