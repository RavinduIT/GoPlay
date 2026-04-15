<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Sports Equipment Shop</h1>
        <p class="text-lg text-gray-600">Find the best sports equipment for your needs</p>
    </div>
    
    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Filter Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select class="form-select">
                <option>All Categories</option>
                <option>Cricket</option>
                <option>Football</option>
                <option>Basketball</option>
                <option>Tennis</option>
            </select>
            <select class="form-select">
                <option>Price Range</option>
                <option>Under ₹500</option>
                <option>₹500 - ₹1500</option>
                <option>₹1500 - ₹5000</option>
                <option>Above ₹5000</option>
            </select>
            <input type="text" class="form-input" placeholder="Search products...">
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Search
            </button>
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Sample Product Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <img src="<?= $_base ?>/public/assets/images/products/cricket-bat.jpg" alt="Cricket Bat" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Professional Cricket Bat</h3>
                <p class="text-gray-600 text-sm mb-3">High-quality willow cricket bat for professional players</p>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-2xl font-bold text-blue-600">₹2,999</span>
                    <span class="text-sm text-gray-500 line-through">₹3,999</span>
                </div>
                <div class="flex items-center mb-3">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <span class="text-sm text-gray-600 ml-2">(124 reviews)</span>
                </div>
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add to Cart
                </button>
            </div>
        </div>
        
        <!-- More product cards would be generated dynamically -->
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-center mt-8">
        <nav class="flex space-x-2">
            <button class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Previous</button>
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">2</button>
            <button class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">3</button>
            <button class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Next</button>
        </nav>
    </div>
</div>