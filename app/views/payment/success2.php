<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Success Icon -->
        <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-green-600 text-4xl"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
        <p class="text-lg text-gray-600 mb-8">Your payment has been processed successfully. Thank you for your booking!</p>
        
        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-6">Booking Details</h2>
            
            <div class="space-y-4 text-left">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Transaction ID:</span>
                    <span class="font-semibold">#TXN123456789</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Booking ID:</span>
                    <span class="font-semibold">#BKG987654321</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Ground:</span>
                    <span class="font-semibold">Football Ground A</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Date & Time:</span>
                    <span class="font-semibold">March 25, 2024 - 6:00 PM to 8:00 PM</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold">2 hours</span>
                </div>
                
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-semibold text-green-600">₹1,829</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-semibold">Credit Card ending in 3456</span>
                </div>
            </div>
        </div>
        
        <!-- Confirmation Email -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-center text-blue-700 mb-3">
                <i class="fas fa-envelope mr-2"></i>
                <span class="font-semibold">Confirmation Email Sent</span>
            </div>
            <p class="text-blue-700 text-sm">
                A confirmation email with your booking details has been sent to your registered email address.
            </p>
        </div>
        
        <!-- Next Steps -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
            <h3 class="text-lg font-semibold mb-4">What's Next?</h3>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                    <span>Your booking is confirmed and the ground is reserved for you</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-clock text-blue-600 mr-3 mt-1"></i>
                    <span>Arrive 15 minutes before your booking time</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-id-card text-purple-600 mr-3 mt-1"></i>
                    <span>Bring a valid ID for verification at the facility</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-phone text-orange-600 mr-3 mt-1"></i>
                    <span>Contact support if you need to make any changes</span>
                </li>
            </ul>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/bookings" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                View My Bookings
            </a>
            <a href="/" class="bg-gray-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                Back to Home
            </a>
            <button onclick="window.print()" class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-2"></i>
                Download Receipt
            </button>
        </div>
        
        <!-- Support Contact -->
        <div class="mt-12 pt-8 border-t text-center">
            <p class="text-gray-600 mb-4">Need help with your booking?</p>
            <div class="flex justify-center space-x-6">
                <a href="tel:+919876543210" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-phone mr-2"></i>
                    +91 98765 43210
                </a>
                <a href="mailto:support@goplay.com" class="flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fas fa-envelope mr-2"></i>
                    support@goplay.com
                </a>
            </div>
        </div>
    </div>
</div>