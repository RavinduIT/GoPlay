<?php
/**
 * Test Coach Booking API
 *
 * Open this in your browser to test the booking system
 */

require_once __DIR__ . '/bootstrap.php';

session_start();

echo "<h1>Coach Booking System Test</h1>";

// Test 1: Check if logged in
echo "<h2>1. Session Check</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User is logged in<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "User Type: " . ($_SESSION['user_type'] ?? 'Not set') . "<br>";
} else {
    echo "❌ <strong>NOT LOGGED IN</strong> - This is likely why booking fails!<br>";
    echo "<a href='/login'>Click here to login</a><br>";
}

// Test 2: Check if CoachBooking class exists
echo "<h2>2. Class Check</h2>";
if (class_exists('App\Models\CoachBooking')) {
    echo "✅ CoachBooking class exists<br>";
} else {
    echo "❌ CoachBooking class NOT found<br>";
}

// Test 3: Check database connection
echo "<h2>3. Database Check</h2>";
try {
    $db = Core\Database::getInstance();
    echo "✅ Database connection successful<br>";

    // Test query
    $stmt = $db->query("SELECT COUNT(*) as count FROM coaches");
    $result = $stmt->fetch();
    echo "Coaches in database: " . $result['count'] . "<br>";

    $stmt = $db->query("SELECT COUNT(*) as count FROM coach_bookings");
    $result = $stmt->fetch();
    echo "Existing bookings: " . $result['count'] . "<br>";

} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: Check if coaches exist
echo "<h2>4. Coaches Check</h2>";
try {
    $coachModel = new App\Models\Coach();
    $coaches = $coachModel->getAvailable();
    echo "✅ Available coaches: " . count($coaches) . "<br>";

    if (count($coaches) > 0) {
        echo "<ul>";
        foreach (array_slice($coaches, 0, 3) as $coach) {
            echo "<li>ID: {$coach['id']} - {$coach['first_name']} {$coach['last_name']} - {$coach['sport_name']}</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching coaches: " . $e->getMessage() . "<br>";
}

// Test 5: Manual booking test (if logged in)
if (isset($_SESSION['user_id'])) {
    echo "<h2>5. Test Booking Creation</h2>";

    try {
        $bookingModel = new App\Models\CoachBooking();

        $testData = [
            'user_id' => $_SESSION['user_id'],
            'coach_id' => 1, // Assuming coach ID 1 exists
            'booking_date' => date('Y-m-d', strtotime('+2 days')),
            'start_time' => '14:00',
            'end_time' => '15:00',
            'session_type' => 'individual',
            'total_amount' => 2500.00,
            'special_requests' => 'Test booking from diagnostic script'
        ];

        echo "<strong>Test Booking Data:</strong><pre>";
        print_r($testData);
        echo "</pre>";

        echo "<form method='post'>";
        echo "<button type='submit' name='create_test'>Create Test Booking</button>";
        echo "</form>";

        if (isset($_POST['create_test'])) {
            $bookingId = $bookingModel->createBooking($testData);
            if ($bookingId) {
                echo "✅ <strong>SUCCESS!</strong> Test booking created with ID: {$bookingId}<br>";
            } else {
                echo "❌ Failed to create booking<br>";
            }
        }

    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If NOT LOGGED IN: <a href='/login'>Login first</a></li>";
echo "<li>If logged in: Try the <a href='/book-session'>booking page</a></li>";
echo "<li>Check browser console (F12) for JavaScript errors</li>";
echo "</ol>";
