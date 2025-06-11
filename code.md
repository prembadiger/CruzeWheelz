# CruzeWheelz Code Documentation

## Table of Contents
1. [Main Pages](#main-pages)
2. [Authentication Pages](#authentication-pages)
3. [User Dashboard](#user-dashboard)
4. [Admin Dashboard](#admin-dashboard)
5. [Booking System](#booking-system)
6. [Database Structure](#database-structure)
7. [JavaScript Components](#javascript-components)
8. [PHP Backend](#php-backend)
9. [CSS Styling](#css-styling)
10. [Reports and Documentation](#reports-and-documentation)

## Main Pages

### index.html
The main landing page of CruzeWheelz containing:
- Header with navigation
- Home section with car search
- How it works section
- Featured vehicles section
- Why choose us section
- Customer reviews
- Newsletter subscription

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <title>cruzewheelz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>
</head>
<body>
  <!-- Header Section -->
  <header>
    <div class="logo">
      <img src="img/logo/car logo.png" alt="Logo" />
      <span class="logo-text">CruzeWheelz <p>Drive Your Dream</p></span>
    </div>
    <!-- Navigation -->
    <ul class="navbar">
      <li><a href="#home">Home</a></li>
      <li><a href="#ride">Ride</a></li>
      <li><a href="#feature">Cars</a></li>
      <li><a href="html/about.html">About</a></li> 
      <li><a href="#reviews">Reviews</a></li>
    </ul>
    <!-- Authentication Buttons -->
    <div class="header-button">
      <a href="html/signup.html" class="sign-up button">Sign Up</a>
      <a href="html/login.html" class="sign-in button">Sign In</a>
    </div>
  </header>
  <!-- Main Content Sections -->
  <!-- ... existing code ... -->
</body>
</html>
```

## Authentication Pages

### login.html
The login page for existing users:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CruzeWheelz</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container">
        <form id="login-form">
            <h2>Login to Your Account</h2>
            <div class="form-group">
                <input type="email" id="email" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" id="password" required placeholder="Password">
            </div>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="signup.html">Sign Up</a></p>
            <a href="forgot_password.html">Forgot Password?</a>
        </form>
    </div>
</body>
</html>
```

### signup.html
The registration page for new users:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CruzeWheelz</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="signup-container">
        <form id="signup-form">
            <h2>Create Your Account</h2>
            <div class="form-group">
                <input type="text" id="fullname" required placeholder="Full Name">
            </div>
            <div class="form-group">
                <input type="email" id="email" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" id="password" required placeholder="Password">
            </div>
            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.html">Login</a></p>
        </form>
    </div>
</body>
</html>
```

## User Dashboard

### user_dashboard.html
The main dashboard for logged-in users:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CruzeWheelz</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="user-info">
                <img src="../img/default-avatar.png" alt="User Avatar">
                <h3 id="user-name">User Name</h3>
            </div>
            <nav>
                <ul>
                    <li><a href="#profile">Profile</a></li>
                    <li><a href="my_bookings.html">My Bookings</a></li>
                    <li><a href="#settings">Settings</a></li>
                </ul>
            </nav>
        </div>
        <div class="main-content">
            <!-- Dashboard content -->
        </div>
    </div>
</body>
</html>
```

## Admin Dashboard

### admin/dashboard.html
The main admin dashboard interface with statistics and management features:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CruzeWheelz</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h1><i class="fas fa-car"></i> CruzeWheelz Admin</h1>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#dashboard" class="nav-link active">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#cars" class="nav-link">
                    <i class="fas fa-car"></i> Manage Cars
                </a>
            </li>
            <li class="nav-item">
                <a href="#bookings" class="nav-link">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
            </li>
            <li class="nav-item">
                <a href="#users" class="nav-link">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a href="#reports" class="nav-link">
                    <i class="fas fa-file-alt"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="#settings" class="nav-link">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
        <button id="logoutBtn" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <p>Welcome back, Admin! Here's what's happening today.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalBookings">0</h3>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <h3 id="activeCars">0</h3>
                <p>Active Cars</p>
            </div>
            <div class="stat-card">
                <h3 id="totalUsers">0</h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3 id="revenue">$0</h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h3>Booking Statistics</h3>
                <canvas id="bookingChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Revenue Overview</h3>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</body>
</html>
```

### admin/admin_actions.php
The backend handler for admin operations:
```php
<?php
// Admin authentication check
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.html');
    exit();
}

// Database connection
require_once 'php/config.php';

// Handle different admin actions
switch ($_POST['action']) {
    case 'get_stats':
        // Get dashboard statistics
        $stats = [
            'total_bookings' => getTotalBookings(),
            'active_cars' => getActiveCars(),
            'total_users' => getTotalUsers(),
            'total_revenue' => getTotalRevenue()
        ];
        echo json_encode($stats);
        break;

    case 'manage_car':
        // Handle car management operations
        handleCarManagement($_POST);
        break;

    case 'manage_booking':
        // Handle booking management operations
        handleBookingManagement($_POST);
        break;

    case 'manage_user':
        // Handle user management operations
        handleUserManagement($_POST);
        break;
}

// Helper functions for database operations
function getTotalBookings() {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM bookings";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// ... other helper functions ...
?>
```

## Booking System

### car_details.html
The page showing detailed information about a specific car:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Details - CruzeWheelz</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="car-details-container">
        <div class="car-gallery">
            <!-- Car images -->
        </div>
        <div class="car-info">
            <h1 id="car-name">Car Name</h1>
            <div class="car-specs">
                <!-- Car specifications -->
            </div>
            <div class="booking-form">
                <!-- Booking form -->
            </div>
        </div>
    </div>
</body>
</html>
```

## Database Structure

### database.sql
The main database structure:
```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars table
CREATE TABLE cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    description TEXT,
    features TEXT,
    status ENUM('available', 'booked') DEFAULT 'available'
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    car_id INT,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (car_id) REFERENCES cars(id)
);
```

## JavaScript Components

### js/main.js
The main JavaScript file handling core functionality:
```javascript
// Car filtering and sorting
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const sortDropdown = document.querySelector('.sortdropdown');
    const carContainer = document.getElementById('carContainer');

    // Filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            filterCars(category);
            updateActiveButton(this);
        });
    });

    // Sort functionality
    sortDropdown.addEventListener('change', function() {
        const sortValue = this.value;
        sortCars(sortValue);
    });

    // Car filtering function
    function filterCars(category) {
        const cars = document.querySelectorAll('.car-card');
        cars.forEach(car => {
            if (category === 'All' || car.dataset.category === category) {
                car.style.display = 'block';
            } else {
                car.style.display = 'none';
            }
        });
    }

    // Car sorting function
    function sortCars(sortValue) {
        const cars = Array.from(document.querySelectorAll('.car-card'));
        cars.sort((a, b) => {
            switch(sortValue) {
                case 'price_asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price_desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'rating_desc':
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                default:
                    return 0;
            }
        });
        carContainer.innerHTML = '';
        cars.forEach(car => carContainer.appendChild(car));
    }
});

// Date picker initialization
flatpickr("#pickup-date", {
    minDate: "today",
    onChange: function(selectedDates) {
        returnDatePicker.set("minDate", selectedDates[0]);
    }
});

flatpickr("#return-date", {
    minDate: "today"
});
```

### js/login.js
Authentication handling:
```javascript
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('../php/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();
        
        if (data.success) {
            window.location.href = 'user_dashboard.html';
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('An error occurred. Please try again.');
    }
});
```

## PHP Backend

### php/config.php
Database configuration:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cruzewheelz');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

### php/create_booking.php
Booking creation handler:
```php
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'];
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $total_price = $_POST['total_price'];

    $query = "INSERT INTO bookings (user_id, car_id, pickup_date, return_date, total_price, status) 
              VALUES (?, ?, ?, ?, ?, 'pending')";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iissd", $user_id, $car_id, $pickup_date, $return_date, $total_price);
    
    if (mysqli_stmt_execute($stmt)) {
        $booking_id = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'booking_id' => $booking_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking creation failed']);
    }
}
?>
```

### php/verify_payment.php
Payment verification handler:
```php
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $payment_id = $_POST['payment_id'];
    $amount = $_POST['amount'];

    // Verify payment with payment gateway
    $payment_verified = verifyPaymentWithGateway($payment_id, $amount);

    if ($payment_verified) {
        // Update booking status
        $query = "UPDATE bookings SET status = 'confirmed', payment_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $payment_id, $booking_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    }
}
?>
```

## CSS Styling

### css/style.css
Main stylesheet for the entire application:
```css
/* Global Styles */
:root {
    --primary-color: #ffd700;
    --secondary-color: #1a1a1a;
    --text-color: #333;
    --light-bg: #f5f5f5;
    --dark-bg: #242424;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
}

/* Header Styles */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 5%;
    background: var(--light-bg);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.navbar {
    display: flex;
    gap: 2rem;
    list-style: none;
}

/* Car Card Styles */
.car-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.car-card:hover {
    transform: translateY(-5px);
}

/* Form Styles */
.form-container {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        display: none;
    }
    
    .car-grid {
        grid-template-columns: 1fr;
    }
}
```

### css/dashboard.css
Dashboard-specific styles:
```css
/* Dashboard Layout */
.dashboard-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    min-height: 100vh;
}

.sidebar {
    background: var(--dark-bg);
    color: white;
    padding: 2rem;
}

.main-content {
    padding: 2rem;
    background: var(--light-bg);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
```

### css/login.css
Authentication pages styles:
```css
/* Login/Signup Container */
.auth-container {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.auth-button {
    width: 100%;
    padding: 1rem;
    background: var(--primary-color);
    color: var(--secondary-color);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}
```


## Screenshots Section

### Home Page
[Add screenshot of home page here]
- Landing page with hero section
- Search form for car rental
- How it works section
- Why choose us section
- Customer reviews
- Newsletter subscription

### Cars Section
[Add screenshot of cars section here]
- Featured vehicles display
- Category filters (Luxury, SUV, Sports, Sedan, Coupe, Supercar)
- Sorting options (Price: Low to High, Price: High to Low, Rating: High to Low, Featured)
- Car cards with images and details

### Login Page
[Add screenshot of login page here]

### Sign Up Page
[Add screenshot of signup page here]

### User Dashboard
[Add screenshot of user dashboard here]

### Admin Dashboard
[Add screenshot of admin dashboard here]

### Car Details Page
[Add screenshot of car details page here]

### Booking Confirmation
[Add screenshot of booking confirmation page here]

### My Bookings
[Add screenshot of my bookings page here] 