# CruzeWheelz - Car Rental System

CruzeWheelz is a web-based car rental management system that allows users to browse, book, and manage car rentals easily and efficiently.

## Features

- User Authentication System
  - Login/Signup
  - Password Recovery
  - Profile Management
  
- Car Rental Management
  - Browse Available Cars
  - Check Car Availability
  - Book Cars
  - View Booking History
  - Cancel Bookings
  
- Admin Panel
  - Manage Cars
  - View Bookings
  - User Management
  - Location Management

## Technology Stack

- Frontend:
  - HTML5
  - CSS3
  - JavaScript

- Backend:
  - PHP
  - MySQL Database

## Project Structure

```
cruzewheelz/
├── admin/              # Administrative interface
│   ├── php/           # Admin backend scripts
│   ├── html/          # Admin frontend pages
│   ├── admin.sql      # Admin database schema
│   └── index.html     # Admin dashboard entry
├── php/               # Backend PHP scripts
├── js/                # JavaScript files
├── img/               # Image assets
├── html/              # HTML template files
├── css/               # Stylesheet files
├── index.html         # Main entry point
├── database.sql       # Main database schema
└── cars.sql           # Car-related database schema
```

## Installation

1. Prerequisites:
   - XAMPP/WAMP/LAMP server
   - PHP 7.4 or higher
   - MySQL 5.7 or higher

2. Setup Steps:
   ```bash
   # Clone the repository to your htdocs folder
   git clone [repository-url] cruzewheelz

   # Import database schemas
   mysql -u root -p < database.sql
   mysql -u root -p < cars.sql
   mysql -u root -p < admin/admin.sql

   # Configure database connection
   # Edit php/config.php with your database credentials
   ```

3. Access the application:
   - Main application: `http://localhost/cruzewheelz`
   - Admin panel: `http://localhost/cruzewheelz/admin`

## Configuration

1. Database Configuration (php/config.php):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'cruzewheelz');
   ```

## Usage

### User Interface
1. Register a new account or login with existing credentials
2. Browse available cars
3. Check car availability for desired dates
4. Make a booking
5. View and manage your bookings
6. Update profile information

### Admin Interface
1. Login to admin panel using admin credentials
2. Manage car inventory
3. View and manage bookings
4. Handle user accounts
5. Manage location data

## Security Features

- Password hashing
- Session management
- SQL injection prevention
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please email: prembadiger9@gmail.com

## Authors

- CruzeWheelz Development Team 
