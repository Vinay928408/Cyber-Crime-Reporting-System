# Cyber Crime Reporting System

A comprehensive online platform for reporting and tracking cyber crime complaints built with Core PHP and MySQL.

## Features

### User Panel
- User registration and login
- File new cyber crime complaints
- Track complaint status and progress
- View complaint history
- Receive officer updates
- Profile management

### Admin Panel
- Admin login and dashboard
- View and manage all complaints
- Assign complaints to officers
- Manage officers and users
- Track overall system statistics
- Bulk operations on complaints

### Officer Panel
- Officer login and dashboard
- View assigned complaints
- Update complaint progress
- Add status notes and remarks
- Mark cases as resolved
- Track personal statistics

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: Core PHP with PDO
- **Database**: MySQL
- **Server**: XAMPP (Apache + MySQL)

## Installation Guide

### Prerequisites
- XAMPP (or similar LAMP/WAMP stack)
- Web browser (Chrome, Firefox, Safari, Edge)
- Text editor (VS Code, Sublime Text, etc.)

### Step 1: Download and Extract
1. Download the project files
2. Extract to: `C:\xampp\htdocs\Cyber Crime Reporting System\`

### Step 2: Database Setup
1. Start XAMPP Control Panel
2. Start Apache and MySQL services
3. Open browser and go to: `http://localhost/phpmyadmin`
4. Create a new database named `cybercrime_db`
5. Import the `cybercrime_db.sql` file from the project root

### Step 3: Configure Database Connection
1. Open `db.php` in the project root
2. Verify database settings:
   ```php
   $host = 'localhost';
   $dbname = 'cybercrime_db';
   $username = 'root';
   $password = '';
   ```

### Step 4: Access the Application
Open your browser and go to:
- **Home Page**: `http://localhost/Cyber Crime Reporting System/`

## Default Login Credentials

### Admin Account
- **URL**: `http://localhost/Cyber Crime Reporting System/admin/login.php`
- **Email**: `admin@gmail.com`
- **Password**: `password`

### Officer Account
- **URL**: `http://localhost/Cyber Crime Reporting System/officer/login.php`
- **Email**: `officer1@gmail.com`
- **Password**: `password`

### User Account
- **URL**: `http://localhost/Cyber Crime Reporting System/user/login.php`
- **Email**: `ram@gmail.com`
- **Password**: `password`

## Project Structure

```
Cyber Crime Reporting System/
├── admin/                          # Admin Panel
│   ├── login.php                   # Admin login
│   ├── dashboard.php               # Admin dashboard
│   ├── complaints.php              # Manage complaints
│   ├── assign_complaint.php        # Assign complaints to officers
│   ├── officers.php               # Manage officers
│   ├── users.php                  # Manage users
│   └── logout.php                 # Admin logout
├── officer/                       # Officer Panel
│   ├── login.php                   # Officer login
│   ├── dashboard.php               # Officer dashboard
│   ├── my_complaints.php          # View assigned complaints
│   ├── view_complaint.php         # View complaint details
│   ├── update_complaint.php       # Update complaint progress
│   ├── profile.php                # Officer profile
│   └── logout.php                 # Officer logout
├── user/                          # User Panel
│   ├── login.php                   # User login
│   ├── register.php                # User registration
│   ├── dashboard.php               # User dashboard
│   ├── file_complaint.php         # File new complaint
│   ├── my_complaints.php          # View user complaints
│   ├── view_complaint.php         # View complaint details
│   ├── profile.php                # User profile
│   └── logout.php                 # User logout
├── assets/                        # Static assets
│   ├── css/
│   │   └── style.css             # Custom styles
│   └── js/
│       └── script.js             # Custom JavaScript
├── db.php                         # Database connection and helper functions
├── index.php                      # Home page
├── cybercrime_db.sql              # Database structure and sample data
└── README.md                      # This file
```

## Database Schema

### Tables
1. **users** - User accounts and information
2. **officers** - Officer accounts and details
3. **admin** - Administrator accounts
4. **complaints** - Complaint records
5. **complaint_updates** - Progress updates for complaints

### Key Relationships
- Users can file multiple complaints
- Officers can be assigned multiple complaints
- Each complaint can have multiple progress updates
- Admin manages all users, officers, and complaints

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention using PDO prepared statements
- XSS prevention using `htmlspecialchars()`
- Session-based authentication
- Role-based access control
- Input validation and sanitization

## Browser Compatibility

- Google Chrome (Recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari
- Opera

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure XAMPP MySQL service is running
   - Check database credentials in `db.php`
   - Verify database name and import

2. **404 Not Found Error**
   - Ensure files are in correct directory
   - Check Apache service is running
   - Verify URL path is correct

3. **Blank Pages**
   - Enable PHP error display in `php.ini`
   - Check file permissions
   - Verify PHP syntax

4. **Login Issues**
   - Check database import was successful
   - Verify default credentials
   - Clear browser cache and cookies

### Error Reporting
To enable detailed error reporting during development, add this to the top of your PHP files:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Support

For technical support and queries:
- Email: support@cybercrime.gov.in
- Phone: +91-1234567890

## License

This project is for educational purposes. Please ensure compliance with local laws and regulations when deploying in production environments.

## Version History

- **v1.0.0** - Initial release with all core features
  - User registration and complaint filing
  - Admin panel with complaint management
  - Officer panel with progress tracking
  - Responsive design with Bootstrap 5

## Future Enhancements

- Email notifications for complaint updates
- File attachment support for evidence
- Advanced reporting and analytics
- Multi-language support
- Mobile application
- SMS notifications
- Integration with law enforcement databases

---

**Note**: This system is designed for educational purposes and demonstration. For production use, additional security measures and testing are recommended.
