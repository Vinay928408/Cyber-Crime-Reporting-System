# Installation Guide

## Cyber Crime Reporting System - Step by Step Installation

### System Requirements

- **Operating System**: Windows 10/11, Linux, or macOS
- **Web Server**: Apache (included in XAMPP)
- **Database**: MySQL 5.7+ or MariaDB 10.2+
- **PHP**: Version 7.4 or higher
- **Browser**: Modern web browser (Chrome, Firefox, Safari, Edge)

---

## Step 1: Install XAMPP

### For Windows:
1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer as Administrator
3. Choose installation location (default: `C:\xampp`)
4. Select Apache and MySQL components
5. Complete the installation

### For Linux/macOS:
1. Download XAMPP for your platform
2. Make the installer executable: `chmod + xampp-linux-x64-*.run`
3. Run with sudo: `sudo ./xampp-linux-x64-*.run`
4. Follow the installation wizard

---

## Step 2: Start XAMPP Services

1. Open XAMPP Control Panel
2. Click "Start" button for Apache
3. Click "Start" button for MySQL
4. Ensure both services show "Running" in green

**Note**: If you encounter port conflicts, change the Apache port from 80 to 8080.

---

## Step 3: Extract Project Files

1. Download the project ZIP file
2. Extract to: `C:\xampp\htdocs\Cyber Crime Reporting System\`
3. Verify the folder structure matches the project layout

**Alternative Method**:
1. Copy the entire project folder to `htdocs`
2. Rename it to `Cyber Crime Reporting System`

---

## Step 4: Database Setup

### Method 1: Using phpMyAdmin (Recommended)

1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. Click "New" in the left sidebar
4. Enter database name: `cybercrime_db`
5. Select "utf8mb4_general_ci" collation
6. Click "Create"
7. Select the newly created database
8. Click "Import" tab
9. Choose file: `cybercrime_db.sql` from project root
10. Click "Go" to import

### Method 2: Using MySQL Command Line

1. Open Command Prompt as Administrator
2. Navigate to XAMPP MySQL bin: `cd C:\xampp\mysql\bin`
3. Login to MySQL: `mysql -u root -p`
4. Create database: `CREATE DATABASE cybercrime_db;`
5. Use database: `USE cybercrime_db;`
6. Import SQL file: `SOURCE "C:\xampp\htdocs\Cyber Crime Reporting System\cybercrime_db.sql";`

---

## Step 5: Configure Database Connection

1. Open file: `C:\xampp\htdocs\Cyber Crime Reporting System\db.php`
2. Verify the following settings:
   ```php
   $host = 'localhost';
   $dbname = 'cybercrime_db';
   $username = 'root';
   $password = '';
   ```
3. Save the file (no changes needed for default XAMPP setup)

---

## Step 6: Test the Installation

1. Open your web browser
2. Go to: `http://localhost/Cyber Crime Reporting System/`
3. You should see the home page

If you see the home page successfully, the installation is complete!

---

## Step 7: Login to Test All Panels

### Test Admin Panel:
1. Go to: `http://localhost/Cyber Crime Reporting System/admin/login.php`
2. Login with:
   - Email: `admin@gmail.com`
   - Password: `password`

### Test Officer Panel:
1. Go to: `http://localhost/Cyber Crime Reporting System/officer/login.php`
2. Login with:
   - Email: `officer1@gmail.com`
   - Password: `password`

### Test User Panel:
1. Go to: `http://localhost/Cyber Crime Reporting System/user/login.php`
2. Login with:
   - Email: `ram@gmail.com`
   - Password: `password`

---

## Troubleshooting

### Common Issues and Solutions

#### Issue 1: Apache Won't Start
**Solution:**
- Check if port 80 is occupied by another application
- Change Apache port to 8080 in XAMPP config
- Stop Skype or other applications using port 80

#### Issue 2: MySQL Won't Start
**Solution:**
- Check if another MySQL instance is running
- Delete `ibdata1` file from `C:\xampp\mysql\data`
- Restart XAMPP services

#### Issue 3: Database Connection Error
**Solution:**
- Verify MySQL service is running
- Check database name in `db.php`
- Ensure database was imported correctly
- Test connection with phpMyAdmin

#### Issue 4: 404 Not Found Error
**Solution:**
- Verify files are in correct directory
- Check Apache service status
- Ensure URL path is correct
- Check `.htaccess` file if present

#### Issue 5: Blank White Pages
**Solution:**
- Enable PHP error display:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```
- Check PHP error logs
- Verify file permissions

#### Issue 6: Login Not Working
**Solution:**
- Verify database import was successful
- Check default credentials in database
- Clear browser cache and cookies
- Test with different browser

---

## Advanced Configuration

### Changing Default Passwords

1. Login to phpMyAdmin
2. Select `cybercrime_db` database
3. Update passwords in respective tables:
   ```sql
   UPDATE admin SET password = '$2y$10$...' WHERE email = 'admin@gmail.com';
   UPDATE officers SET password = '$2y$10$...' WHERE email = 'officer1@gmail.com';
   UPDATE users SET password = '$2y$10$...' WHERE email = 'ram@gmail.com';
   ```

### Customizing Application

1. **Change Site Name**: Edit `index.php` and all panel headers
2. **Modify Colors**: Edit `assets/css/style.css`
3. **Add Features**: Extend database schema and PHP files
4. **Change Logo**: Replace in header sections

---

## Security Recommendations for Production

1. **Change Default Passwords**: Immediately change all default credentials
2. **Database Security**: 
   - Create dedicated database user
   - Restrict database privileges
   - Use strong passwords
3. **PHP Configuration**:
   - Disable error display in production
   - Enable error logging
   - Set appropriate file permissions
4. **Web Server Security**:
   - Use HTTPS (SSL certificate)
   - Implement firewall rules
   - Regular security updates
5. **Application Security**:
   - Regular backups
   - Input validation
   - Session security
   - CSRF protection

---

## Performance Optimization

1. **Database Optimization**:
   - Add indexes to frequently queried columns
   - Optimize SQL queries
   - Regular database maintenance

2. **PHP Optimization**:
   - Enable OPcache
   - Use efficient coding practices
   - Minimize database calls

3. **Frontend Optimization**:
   - Minify CSS and JavaScript
   - Optimize images
   - Enable browser caching

---

## Backup and Recovery

### Database Backup
```bash
mysqldump -u root -p cybercrime_db > backup.sql
```

### Database Restore
```bash
mysql -u root -p cybercrime_db < backup.sql
```

### File Backup
Regularly backup the entire project directory:
```
C:\xampp\htdocs\Cyber Crime Reporting System\
```

---

## Support Resources

- **Official Documentation**: Check `README.md`
- **Video Tutorial**: [Installation Video Link]
- **Community Forum**: [Support Forum Link]
- **Email Support**: support@cybercrime.gov.in

---

## Final Checklist

Before going live, ensure:

- [ ] XAMPP services are running properly
- [ ] Database is created and imported
- [ ] All default login credentials work
- [ ] All panels load without errors
- [ ] File upload functionality works
- [ ] Email configuration (if needed)
- [ ] Security measures are implemented
- [ ] Backup strategy is in place
- [ ] Performance is optimized
- [ ] Documentation is complete

---

**Congratulations!** Your Cyber Crime Reporting System is now installed and ready to use.

For any issues during installation, please refer to the troubleshooting section or contact support.
