<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Secure Payment</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Payment Information</h2>
                
                <!-- Payment Methods -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="card" class="mr-3" checked>
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            Credit/Debit Card
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="upi" class="mr-3">
                            <i class="fab fa-google-pay text-green-600 mr-2"></i>
                            UPI Payment
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="wallet" class="mr-3">
                            <i class="fas fa-wallet text-purple-600 mr-2"></i>
                            Digital Wallet
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="netbanking" class="mr-3">
                            <i class="fas fa-university text-red-600 mr-2"></i>
                            Net Banking
                        </label>
                    </div>
                </div>
                
                <!-- Card Details Form -->
                <form id="payment-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Holder Name</label>
                            <input type="text" class="form-input w-full" placeholder="Enter full name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <input type="text" class="form-input w-full" placeholder="1234 5678 9012 3456" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" class="form-input w-full" placeholder="MM/YY" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <input type="text" class="form-input w-full" placeholder="123" required>
                        </div>
                    </div>
                    
                    <!-- Billing Address -->
                    <h3 class="text-lg font-semibold mb-4">Billing Address</h3>
                    <div class="space-y-4 mb-6">
                        <input type="text" class="form-input w-full" placeholder="Street Address" required>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" class="form-input w-full" placeholder="City" required>
                            <input type="text" class="form-input w-full" placeholder="Postal Code" required>
                        </div>
                        <select class="form-select w-full" required>
                            <option>Select State</option>
                            <option>Delhi</option>
                            <option>Mumbai</option>
                            <option>Bangalore</option>
                            <option>Chennai</option>
                        </select>
                    </div>
                    
                    <!-- Security Features -->
                    <div class="bg-green-50 p-4 rounded-lg mb-6">
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span class="text-sm">Your payment information is secure and encrypted</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                        <i class="fas fa-lock mr-2"></i>
                        Pay Securely
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Order Summary</h2>
                
                <!-- Order Items -->
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-medium">Ground Booking</h4>
                            <p class="text-sm text-gray-600">Football Ground - 2 hours</p>
                            <p class="text-sm text-gray-600">Date: March 25, 2024</p>
                        </div>
                        <span class="font-bold">₹1,200</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="font-medium">Equipment Rental</h4>
                            <p class="text-sm text-gray-600">Football set</p>
                        </div>
                        <span class="font-bold">₹300</span>
                    </div>
                </div>
                
                <!-- Price Breakdown -->
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>₹1,500</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Fee</span>
                        <span>₹50</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">GST (18%)</span>
                        <span>₹279</span>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600">₹1,829</span>
                    </div>
                </div>
                
                <!-- Accepted Payment Methods -->
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">We Accept</h3>
                    <div class="flex space-x-3">
                        <img src="/public/assets/images/payment/visa.png" alt="Visa" class="h-8">
                        <img src="/public/assets/images/payment/mastercard.png" alt="Mastercard" class="h-8">
                        <img src="/public/assets/images/payment/rupay.png" alt="RuPay" class="h-8">
                        <img src="/public/assets/images/payment/upi.png" alt="UPI" class="h-8">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>