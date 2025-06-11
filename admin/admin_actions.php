<?php
require_once 'php/config.php';
header('Content-Type: application/json');

// Get raw POST data
$raw_post = file_get_contents('php://input');
$data = json_decode($raw_post, true);

// If no JSON data, use $_POST
if ($data === null) {
    $data = $_POST;
}

error_log("Received request: " . print_r($data, true));
error_log("Current session: " . print_r($_SESSION, true));

// Handle all admin actions
$action = $data['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            handleLogin($data);
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        case 'get_stats':
            requireAdmin();
            getStats();
            break;
            
        case 'get_bookings':
            requireAdmin();
            getBookings();
            break;
            
        case 'update_booking':
            requireAdmin();
            updateBooking($data);
            break;
            
        case 'get_users':
            requireAdmin();
            getUsers();
            break;
            
        case 'get_locations':
            requireAdmin();
            getLocations();
            break;
            
        case 'get_location':
            requireAdmin();
            getLocation($data);
            break;
            
        case 'add_location':
            requireAdmin();
            addLocation($data);
            break;
            
        case 'update_location':
            requireAdmin();
            updateLocation($data);
            break;
            
        case 'delete_location':
            requireAdmin();
            deleteLocation($data);
            break;
            
        case 'get_vehicles':
            requireAdmin();
            getVehicles();
            break;
            
        case 'get_vehicle':
            requireAdmin();
            getVehicle($data);
            break;
            
        case 'add_vehicle':
            requireAdmin();
            addVehicle($data);
            break;
            
        case 'update_vehicle':
            requireAdmin();
            updateVehicle($data);
            break;
            
        case 'get_booking_details':
            if (!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
                exit;
            }
            getBookingDetails($_GET['id']);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Error in admin_actions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleLogin($data) {
    global $admin_db;
    error_log("=== Login Attempt Start ===");
    error_log("Login attempt for username: " . ($data['username'] ?? 'not provided'));
    error_log("Raw POST data: " . print_r($data, true));
    
    if (empty($data['username']) || empty($data['password'])) {
        error_log("Login failed: Username or password empty");
        throw new Exception('Username and password are required');
    }
    
    // Verify credentials
    $stmt = $admin_db->prepare("
        SELECT id, username, password, name, status
        FROM admin_users 
        WHERE username = ?
    ");
    $stmt->execute([$data['username']]);
    $admin = $stmt->fetch();
    
    error_log("Admin user found: " . ($admin ? 'Yes' : 'No'));
    if ($admin) {
        error_log("Admin user details: " . print_r($admin, true));
    }
    
    if (!$admin) {
        error_log("User not found: " . $data['username']);
        throw new Exception('Invalid username or password');
    }
    
    if ($admin['status'] !== 'active') {
        error_log("User account not active: " . $data['username']);
        throw new Exception('Account is not active');
    }
    
    // Special handling for known passwords
    $valid_password = false;
    if ($data['username'] === 'admin' && $data['password'] === 'admin123') {
        $valid_password = true;
    } else if ($data['username'] === 'prembadiger9@gmail.com' && $data['password'] === '@SnehaPrem1') {
        $valid_password = true;
    } else {
        $valid_password = password_verify($data['password'], $admin['password']);
    }
    
    if (!$valid_password) {
        error_log("Invalid password for user: " . $data['username']);
        logActivity('failed_login', 'Failed login attempt: ' . $data['username']);
        throw new Exception('Invalid username or password');
    }
    
    // Clear any existing session
    session_unset();
    session_destroy();
    session_start();
    
    // Set session data
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['last_activity'] = time();
    
    error_log("New session created with ID: " . session_id());
    error_log("Session data after login: " . print_r($_SESSION, true));
    
    // Update last login
    $stmt = $admin_db->prepare("UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$admin['id']]);
    
    logActivity('login', 'Admin logged in successfully');
    error_log("=== Login Attempt End - Success ===");
    
    echo json_encode([
        'success' => true,
        'admin' => [
            'id' => $admin['id'],
            'name' => $admin['name']
        ]
    ]);
}

function handleLogout() {
    logActivity('logout', 'Admin logged out');
    session_destroy();
    echo json_encode(['success' => true]);
}

function getStats() {
    global $main_db;
    error_log("Attempting to fetch stats...");
    
    try {
        // Get the period from request (default to month)
        $period = $_GET['period'] ?? 'month';
        
        // Get the last 6 months for revenue
        $months = [];
        $revenue_data = [];
        
        switch ($period) {
            case 'week':
                // Get last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $months[] = date('D', strtotime("-$i days"));
                    
                    $stmt = $main_db->prepare("
                        SELECT COALESCE(SUM(total_amount), 0) as revenue
                        FROM car_bookings 
                        WHERE (
                            (payment_method = 'razorpay' AND payment_status = 'paid') OR
                            (payment_method = 'cash' AND status = 'completed')
                        )
                        AND status != 'cancelled'
                        AND DATE(pickup_date) = ?
                    ");
                    $stmt->execute([$date]);
                    $revenue = (float)$stmt->fetchColumn();
                    $revenue_data[] = $revenue;
                }
                break;
                
            case 'year':
                // Get last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = date('Y-m', strtotime("-$i months"));
                    $months[] = date('M Y', strtotime("-$i months"));
                    
                    $stmt = $main_db->prepare("
                        SELECT COALESCE(SUM(total_amount), 0) as revenue
                        FROM car_bookings 
                        WHERE (
                            (payment_method = 'razorpay' AND payment_status = 'paid') OR
                            (payment_method = 'cash' AND status = 'completed')
                        )
                        AND status != 'cancelled'
                        AND DATE_FORMAT(pickup_date, '%Y-%m') = ?
                    ");
                    $stmt->execute([$date]);
                    $revenue = (float)$stmt->fetchColumn();
                    $revenue_data[] = $revenue;
                }
                break;
                
            default: // month
                // Get last 6 months
                for ($i = 5; $i >= 0; $i--) {
                    $date = date('Y-m', strtotime("-$i months"));
                    $months[] = date('M', strtotime("-$i months"));
                    
                    $stmt = $main_db->prepare("
                        SELECT COALESCE(SUM(total_amount), 0) as revenue
                        FROM car_bookings 
                        WHERE (
                            (payment_method = 'razorpay' AND payment_status = 'paid') OR
                            (payment_method = 'cash' AND status = 'completed')
                        )
                        AND status != 'cancelled'
                        AND DATE_FORMAT(pickup_date, '%Y-%m') = ?
                    ");
                    $stmt->execute([$date]);
                    $revenue = (float)$stmt->fetchColumn();
                    $revenue_data[] = $revenue;
                }
                break;
        }
        
        // Get total booking counts by status
        $stmt = $main_db->prepare("
            SELECT 
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed,
                COUNT(CASE WHEN status = 'ongoing' THEN 1 END) as ongoing,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled
            FROM car_bookings
        ");
        $stmt->execute();
        $booking_counts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Log the booking counts for debugging
        error_log("Booking counts: " . print_r($booking_counts, true));
        
        // Calculate total revenue based on payment method and status
        $stmt = $main_db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total_revenue
            FROM car_bookings 
            WHERE (
                (payment_method = 'razorpay' AND payment_status = 'paid') OR
                (payment_method = 'cash' AND status = 'completed')
            )
            AND status != 'cancelled'
        ");
        $stmt->execute();
        $total_revenue = $stmt->fetchColumn();
        
        $stats = [
            'total_bookings' => $main_db->query("SELECT COUNT(*) FROM car_bookings")->fetchColumn(),
            'active_bookings' => $main_db->query("SELECT COUNT(*) FROM car_bookings WHERE status IN ('confirmed', 'ongoing')")->fetchColumn(),
            'total_users' => $main_db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_revenue' => $total_revenue,
            
            // Graph data
            'revenue_labels' => $months,
            'revenue_data' => $revenue_data,
            
            // Booking status counts
            'pending_bookings' => (int)$booking_counts['pending'],
            'confirmed_bookings' => (int)$booking_counts['confirmed'],
            'ongoing_bookings' => (int)$booking_counts['ongoing'],
            'completed_bookings' => (int)$booking_counts['completed'],
            'cancelled_bookings' => (int)$booking_counts['cancelled']
        ];
        
        error_log("Stats fetched successfully: " . print_r($stats, true));
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
    } catch (Exception $e) {
        error_log("Error fetching stats: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching stats: " . $e->getMessage()
        ]);
    }
}

function getBookings() {
    global $main_db;
    
    error_log("Attempting to fetch bookings...");
    
    try {
        $stmt = $main_db->prepare("
            SELECT b.*, u.name as user_name, c.name as car_name,
                   l1.name as pickup_location, l2.name as return_location,
                   b.cancellation_reason
            FROM car_bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN cars c ON b.car_id = c.id
            LEFT JOIN locations l1 ON b.pickup_location_id = l1.id
            LEFT JOIN locations l2 ON b.return_location_id = l2.id
            ORDER BY b.created_at DESC
        ");
        
        error_log("Query prepared successfully");
        $stmt->execute();
        error_log("Query executed successfully");
        
        $results = $stmt->fetchAll();
        error_log("Number of bookings found: " . count($results));
        
        echo json_encode([
            'success' => true,
            'bookings' => $results
        ]);
    } catch (Exception $e) {
        error_log("Error fetching bookings: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching bookings: " . $e->getMessage()
        ]);
    }
}

function getBookingDetails($id) {
    global $main_db;
    
    try {
        $stmt = $main_db->prepare("
            SELECT b.*, u.name as user_name, c.name as car_name,
                   l1.name as pickup_location, l2.name as return_location,
                   b.cancellation_reason
            FROM car_bookings b
            LEFT JOIN users u ON b.user_id = u.id
            LEFT JOIN cars c ON b.car_id = c.id
            LEFT JOIN locations l1 ON b.pickup_location_id = l1.id
            LEFT JOIN locations l2 ON b.return_location_id = l2.id
            WHERE b.id = ?
        ");
        
        $stmt->execute([$id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            echo json_encode([
                'success' => true,
                'booking' => $booking
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Booking not found'
            ]);
        }
    } catch (Exception $e) {
        error_log("Error fetching booking details: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching booking details: " . $e->getMessage()
        ]);
    }
}

function updateBooking($data) {
    global $main_db;
    
    if (!isset($data['booking_id']) || !isset($data['status'])) {
        throw new Exception('Booking ID and status are required');
    }
    
    $main_db->beginTransaction();
    
    try {
        // First get the current booking details
        $stmt = $main_db->prepare("
            SELECT payment_method, payment_status, total_amount 
            FROM car_bookings 
            WHERE id = ?
        ");
        $stmt->execute([$data['booking_id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // If status is being changed to cancelled and it's a Razorpay payment
        if ($data['status'] === 'cancelled' && $booking['payment_method'] === 'razorpay' && $booking['payment_status'] === 'paid') {
            // Update both status and payment_status
            $stmt = $main_db->prepare("
                UPDATE car_bookings 
                SET status = ?, 
                    payment_status = 'refunded',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$data['status'], $data['booking_id']]);
        } else {
            // Normal status update
            $stmt = $main_db->prepare("
                UPDATE car_bookings 
                SET status = ?, 
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$data['status'], $data['booking_id']]);
        }
        
        logActivity('update_booking', "Updated booking #{$data['booking_id']} status to {$data['status']}");
        
        $main_db->commit();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $main_db->rollBack();
        throw $e;
    }
}

function getUsers() {
    global $main_db;
    error_log("Attempting to fetch users...");
    
    try {
        $stmt = $main_db->query("
            SELECT id, name, email, phone, created_at,
                   (SELECT COUNT(*) FROM car_bookings WHERE user_id = users.id) as total_bookings
            FROM users
            ORDER BY created_at ASC
        ");
        
        $users = $stmt->fetchAll();
        error_log("Number of users found: " . count($users));
        
        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    } catch (Exception $e) {
        error_log("Error fetching users: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching users: " . $e->getMessage()
        ]);
    }
}

function getLocations() {
    global $main_db;
    error_log("Attempting to fetch locations...");
    
    try {
        $stmt = $main_db->query("
            SELECT id, name, status
            FROM locations
            ORDER BY name ASC
        ");
        
        $locations = $stmt->fetchAll();
        error_log("Number of locations found: " . count($locations));
        
        echo json_encode([
            'success' => true,
            'locations' => $locations
        ]);
    } catch (Exception $e) {
        error_log("Error fetching locations: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching locations: " . $e->getMessage()
        ]);
    }
}

function getVehicles() {
    global $main_db;
    error_log("Attempting to fetch vehicles...");
    
    try {
        $stmt = $main_db->query("
            SELECT id, name, category, seats, transmission, price as daily_rate, 
                   rating, featured, image, description, features, specifications,
                   location_1, location_2, 
                   CASE WHEN featured = 1 THEN 'active' ELSE 'inactive' END as status
            FROM cars
            ORDER BY name ASC
        ");
        
        $vehicles = $stmt->fetchAll();
        error_log("Number of vehicles found: " . count($vehicles));
        
        echo json_encode([
            'success' => true,
            'vehicles' => $vehicles
        ]);
    } catch (Exception $e) {
        error_log("Error fetching vehicles: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => "Error fetching vehicles: " . $e->getMessage()
        ]);
    }
}

function getLocation($data) {
    global $main_db;
    
    if (!isset($data['id'])) {
        throw new Exception('Location ID is required');
    }
    
    $stmt = $main_db->prepare("
        SELECT id, name, status
        FROM locations
        WHERE id = ?
    ");
    $stmt->execute([$data['id']]);
    $location = $stmt->fetch();
    
    if (!$location) {
        throw new Exception('Location not found');
    }
    
    echo json_encode([
        'success' => true,
        'location' => $location
    ]);
}

function addLocation($data) {
    global $main_db;
    
    if (empty($data['name'])) {
        throw new Exception('Location name is required');
    }
    
    $stmt = $main_db->prepare("
        INSERT INTO locations (name, status)
        VALUES (?, 'active')
    ");
    
    $stmt->execute([$data['name']]);
    
    logActivity('add_location', "Added new location: {$data['name']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Location added successfully'
    ]);
}

function updateLocation($data) {
    global $main_db;
    
    if (empty($data['id']) || empty($data['name'])) {
        throw new Exception('Location ID and name are required');
    }
    
    $stmt = $main_db->prepare("
        UPDATE locations
        SET name = ?, status = ?
        WHERE id = ?
    ");
    
    $status = isset($data['status']) ? $data['status'] : 'active';
    
    $stmt->execute([
        $data['name'],
        $status,
        $data['id']
    ]);
    
    logActivity('update_location', "Updated location: {$data['name']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Location updated successfully'
    ]);
}
 
function deleteLocation($data) {
    global $main_db;
    
    if (!isset($data['id'])) {
        throw new Exception('Location ID is required');
    }
    
    // Check if the location is being used in any bookings
    $stmt = $main_db->prepare("
        SELECT COUNT(*) FROM car_bookings 
        WHERE pickup_location_id = ? OR return_location_id = ?
    ");
    $stmt->execute([$data['id'], $data['id']]);
    $bookingCount = $stmt->fetchColumn();
    
    if ($bookingCount > 0) {
        throw new Exception('Cannot delete location as it is being used in bookings');
    }
    
    $stmt = $main_db->prepare("DELETE FROM locations WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    logActivity('delete_location', "Deleted location ID: {$data['id']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Location deleted successfully'
    ]);
}

function getVehicle($data) {
    global $main_db;
    
    // Get ID from either GET or POST data
    $id = $data['id'] ?? $_GET['id'] ?? null;
    
    if (!$id) {
        error_log("Vehicle ID not provided in request. Data received: " . print_r($data, true));
        throw new Exception('Vehicle ID is required');
    }
    
    error_log("Fetching vehicle with ID: " . $id);
    
    $stmt = $main_db->prepare("
        SELECT id, name, category, seats, transmission, price, 
               rating, featured, image, description, features, specifications,
               location_1, location_2
        FROM cars
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$vehicle) {
        error_log("No vehicle found with ID: " . $id);
        throw new Exception('Vehicle not found');
    }
    
    error_log("Raw vehicle data from database: " . print_r($vehicle, true));
    
    // Parse features and specifications from newline-separated strings
    if (!empty($vehicle['features'])) {
        // First try to parse as JSON
        $features = json_decode($vehicle['features'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($features)) {
            $vehicle['features'] = $features;
        } else {
            // If not valid JSON, treat as newline-separated string
            $features = array_map('trim', explode("\n", $vehicle['features']));
            // Remove bullet points if present
            $features = array_map(function($feature) {
                return ltrim($feature, '• ');
            }, $features);
            $vehicle['features'] = array_filter($features); // Remove empty lines
        }
    } else {
        $vehicle['features'] = [];
    }
    
    if (!empty($vehicle['specifications'])) {
        // First try to parse as JSON
        $specs = json_decode($vehicle['specifications'], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($specs)) {
            $vehicle['specifications'] = $specs;
        } else {
            // If not valid JSON, treat as newline-separated string
            $specs = array_map('trim', explode("\n", $vehicle['specifications']));
            // Remove bullet points if present
            $specs = array_map(function($spec) {
                return ltrim($spec, '• ');
            }, $specs);
            $vehicle['specifications'] = array_filter($specs); // Remove empty lines
        }
    } else {
        $vehicle['specifications'] = [];
    }
    
    error_log("Processed vehicle data: " . print_r($vehicle, true));
    
    echo json_encode([
        'success' => true,
        'vehicle' => $vehicle
    ]);
}

function addVehicle($data) {
    global $main_db;
    
    if (empty($data['name']) || empty($data['category']) || empty($data['seats']) || 
        empty($data['transmission']) || empty($data['price']) || empty($data['location_1'])) {
        throw new Exception('Required fields are missing');
    }
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../img/cars/uploads/';
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = 'uploads/' . $fileName;
        }
    }
    
    // Format features and specifications
    $features = '';
    if (!empty($data['features'])) {
        $featuresArray = json_decode($data['features'], true);
        if (is_array($featuresArray)) {
            $features = implode("\n", array_map(function($feature) {
                return '• ' . trim($feature);
            }, $featuresArray));
        }
    }
    
    $specifications = '';
    if (!empty($data['specifications'])) {
        $specsArray = json_decode($data['specifications'], true);
        if (is_array($specsArray)) {
            $specifications = implode("\n", array_map(function($spec) {
                return '• ' . trim($spec);
            }, $specsArray));
        }
    }
    
    $stmt = $main_db->prepare("
        INSERT INTO cars (
            name, category, seats, transmission, price, rating, 
            featured, image, description, features, specifications,
            location_1, location_2
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");
    
    $stmt->execute([
        $data['name'],
        $data['category'],
        $data['seats'],
        $data['transmission'],
        $data['price'],
        $data['rating'] ?? null,
        $data['featured'] ?? 0,
        $image,
        $data['description'] ?? null,
        $features,
        $specifications,
        $data['location_1'],
        $data['location_2'] ?? null
    ]);
    
    logActivity('add_vehicle', "Added new vehicle: {$data['name']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Vehicle added successfully'
    ]);
}

function updateVehicle($data) {
    global $main_db;
    
    if (empty($data['id']) || empty($data['name']) || empty($data['category']) || 
        empty($data['seats']) || empty($data['transmission']) || empty($data['price']) || 
        empty($data['location_1'])) {
        throw new Exception('Required fields are missing');
    }
    
    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../img/cars/uploads/';
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $image = 'uploads/' . $fileName;
        }
    }
    
    // Format features and specifications
    $features = '';
    if (!empty($data['features'])) {
        $featuresArray = json_decode($data['features'], true);
        if (is_array($featuresArray)) {
            $features = implode("\n", array_map(function($feature) {
                return '• ' . trim($feature);
            }, $featuresArray));
        }
    }
    
    $specifications = '';
    if (!empty($data['specifications'])) {
        $specsArray = json_decode($data['specifications'], true);
        if (is_array($specsArray)) {
            $specifications = implode("\n", array_map(function($spec) {
                return '• ' . trim($spec);
            }, $specsArray));
        }
    }
    
    $sql = "
        UPDATE cars SET
            name = ?,
            category = ?,
            seats = ?,
            transmission = ?,
            price = ?,
            rating = ?,
            featured = ?,
            description = ?,
            features = ?,
            specifications = ?,
            location_1 = ?,
            location_2 = ?
    ";
    
    $params = [
        $data['name'],
        $data['category'],
        $data['seats'],
        $data['transmission'],
        $data['price'],
        $data['rating'] ?? null,
        $data['featured'] ?? 0,
        $data['description'] ?? null,
        $features,
        $specifications,
        $data['location_1'],
        $data['location_2'] ?? null
    ];
    
    // Add image update if new image was uploaded
    if ($image) {
        $sql .= ", image = ?";
        $params[] = $image;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $data['id'];
    
    $stmt = $main_db->prepare($sql);
    $stmt->execute($params);
    
    logActivity('update_vehicle', "Updated vehicle: {$data['name']}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Vehicle updated successfully'
    ]);
}
?> 