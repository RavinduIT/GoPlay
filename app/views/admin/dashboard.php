<div class="min-h-screen bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">GoPlay Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, Admin</span>
                    <img src="/public/assets/images/admin-avatar.jpg" alt="Admin" class="w-8 h-8 rounded-full">
                </div>
            </div>
        </div>
    </header>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Bookings</h3>
                        <p class="text-2xl font-bold text-gray-900">1,247</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">↗ 12% from last month</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-rupee-sign text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Revenue</h3>
                        <p class="text-2xl font-bold text-gray-900">₹2,34,567</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">↗ 8% from last month</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                        <p class="text-2xl font-bold text-gray-900">892</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">↗ 15% from last month</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-map-marker-alt text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Active Grounds</h3>
                        <p class="text-2xl font-bold text-gray-900">45</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">↗ 3 new this month</span>
                </div>
            </div>
        </div>
        
        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Revenue Trend</h3>
                <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-gray-500">Chart Placeholder - Revenue over time</span>
                </div>
            </div>
            
            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Bookings</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b">
                        <div>
                            <p class="font-medium">Football Ground A</p>
                            <p class="text-sm text-gray-600">John Doe - 2 hours</p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Confirmed</span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b">
                        <div>
                            <p class="font-medium">Cricket Ground B</p>
                            <p class="text-sm text-gray-600">Jane Smith - 3 hours</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm">Pending</span>
                    </div>
                    
                    <div class="flex items-center justify-between py-3 border-b">
                        <div>
                            <p class="font-medium">Tennis Court 1</p>
                            <p class="text-sm text-gray-600">Mike Johnson - 1 hour</p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Confirmed</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/admin/bookings" class="text-blue-600 hover:text-blue-800 font-medium">View All Bookings →</a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="/admin/grounds" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                    <i class="fas fa-plus-circle text-blue-600 text-3xl mb-3"></i>
                    <p class="font-medium">Add Ground</p>
                </a>
                
                <a href="/admin/coaches" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                    <i class="fas fa-user-plus text-green-600 text-3xl mb-3"></i>
                    <p class="font-medium">Add Coach</p>
                </a>
                
                <a href="/admin/news" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                    <i class="fas fa-newspaper text-purple-600 text-3xl mb-3"></i>
                    <p class="font-medium">Add News</p>
                </a>
                
                <a href="/admin/shop" class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                    <i class="fas fa-shopping-bag text-orange-600 text-3xl mb-3"></i>
                    <p class="font-medium">Manage Shop</p>
                </a>
            </div>
        </div>
    </div>
</div>