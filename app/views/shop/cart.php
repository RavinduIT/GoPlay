<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Cart Items</h2>
                
                <!-- Cart Item -->
                <div class="flex items-center border-b pb-4 mb-4">
                    <img src="/public/assets/images/products/cricket-bat.jpg" alt="Product" class="w-20 h-20 object-cover rounded-lg">
                    <div class="flex-grow ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Professional Cricket Bat</h3>
                        <p class="text-gray-600">High-quality willow cricket bat</p>
                        <div class="flex items-center mt-2">
                            <button class="bg-gray-200 px-2 py-1 rounded-lg hover:bg-gray-300">-</button>
                            <span class="mx-3 font-semibold">1</span>
                            <button class="bg-gray-200 px-2 py-1 rounded-lg hover:bg-gray-300">+</button>
                            <span class="ml-4 text-lg font-bold text-blue-600">₹2,999</span>
                        </div>
                    </div>
                    <button class="text-red-600 hover:text-red-800 ml-4">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <!-- More cart items would be dynamically generated -->
                
                <div class="flex justify-between items-center pt-4">
                    <button class="text-blue-600 hover:text-blue-800">Continue Shopping</button>
                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Clear Cart</button>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Order Summary</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">₹2,999</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-semibold">₹99</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-semibold">₹360</span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-blue-600">₹3,458</span>
                    </div>
                </div>
                
                <button class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    Proceed to Checkout
                </button>
                
                <!-- Coupon Code -->
                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Have a coupon code?</h3>
                    <div class="flex">
                        <input type="text" class="flex-grow form-input rounded-r-none" placeholder="Enter code">
                        <button class="bg-gray-600 text-white px-4 py-2 rounded-r-lg hover:bg-gray-700">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>