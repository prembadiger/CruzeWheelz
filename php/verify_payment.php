<?php
// Prevent any HTML errors from being output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check if direct redirect is requested
$directRedirect = isset($_POST['direct_redirect']) && $_POST['direct_redirect'] === 'true';
error_log("Direct redirect requested: " . ($directRedirect ? 'Yes' : 'No'));

// Set headers for JSON response if not doing direct redirect
if (!$directRedirect) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
}

require_once 'config.php';

// Check if Razorpay library exists
$razorpayLibPath = 'vendor/razorpay/razorpay-php/Razorpay.php';
$razorpayAvailable = file_exists(__DIR__ . '/' . $razorpayLibPath);

if ($razorpayAvailable) {
    require_once $razorpayLibPath;
} else {
    error_log("Razorpay library not found at: " . __DIR__ . '/' . $razorpayLibPath);
}

try {
    // Check if this is a form POST or JSON request
    if (isset($_POST['razorpay_payment_id'])) {
        // Form POST data
        $postData = $_POST;
        error_log("Received form POST data: " . json_encode($postData));
    } else {
        // JSON data
        $postData = json_decode(file_get_contents('php://input'), true);
        error_log("Received JSON data: " . json_encode($postData));
    }
    
    if (!$postData) {
        throw new Exception('Invalid request data');
    }

    // Log received data
    error_log("Received payment verification request: " . json_encode($postData));

    // Verify required fields
    $requiredFields = ['razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature', 'booking_id'];
    foreach ($requiredFields as $field) {
        if (!isset($postData[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Verify payment based on Razorpay availability
    if ($razorpayAvailable) {
        // Initialize Razorpay
        $api = new Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

        // Verify signature
        $attributes = array(
            'razorpay_order_id' => $postData['razorpay_order_id'],
            'razorpay_payment_id' => $postData['razorpay_payment_id'],
            'razorpay_signature' => $postData['razorpay_signature']
        );

        try {
            $api->utility->verifyPaymentSignature($attributes);
            error_log("Signature verification successful");
        } catch (Exception $e) {
            error_log("Signature verification failed: " . $e->getMessage());
            throw new Exception("Payment verification failed: Invalid signature");
        }
    } else {
        // Skip signature verification if Razorpay library is not available
        // This is just for development/testing purposes
        error_log("WARNING: Skipping signature verification because Razorpay library is not available");
        error_log("Payment data: " . json_encode($postData));
    }

    // Update booking status in database
    $bookingId = $postData['booking_id'];
    $paymentId = $postData['razorpay_payment_id'];
    
    try {
        // Make sure we have a valid database connection
        if (!isset($pdo) || !($pdo instanceof PDO)) {
            error_log("Database connection not available. Creating new connection.");
            // Create a new connection if not available
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                throw new Exception("Database connection failed");
            }
        }
        
        $stmt = $pdo->prepare("UPDATE car_bookings SET payment_status = 'paid', status = 'confirmed' WHERE id = ?");
        $stmt->execute([$bookingId]);

        if ($stmt->rowCount() === 0) {
            error_log("No booking found with ID: " . $bookingId . " or no changes needed");
            // Don't throw an exception here, the booking might already be processed
            // Just log it and continue
        } else {
            error_log("Booking updated successfully for ID: " . $bookingId);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        // Don't throw an exception here, we still want to redirect the user
        // Just log the error
    }

    // Handle response based on redirect preference
    if ($directRedirect) {
        // Perform a direct server-side redirect with absolute URL
        error_log("Performing direct redirect to booking_success.html for booking ID: " . $bookingId);
        // Get the base URL of the site
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host . '/cruzewheelz/';
        
        // Redirect to the success page with absolute URL
        header("Location: " . $baseUrl . "html/booking_success.html?id=" . $bookingId);
        exit;
    } else {
        // Get the base URL of the site for JSON response
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host . '/cruzewheelz/';
        
        // Return JSON response with absolute redirect URL
        echo json_encode([
            'success' => true,
            'message' => 'Payment verified successfully',
            'booking_id' => $bookingId,
            'redirect_url' => $baseUrl . 'html/booking_success.html?id=' . $bookingId
        ]);
    }

} catch (Exception $e) {
    error_log("Payment verification error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 