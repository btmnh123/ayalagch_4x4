# АЯЛАГЧ 4X4 - Database Setup Guide

## Setup Instructions

### 1. Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Import or run the SQL file: `database_schema.sql`
   - This will create the database `ayalagch_db` and required tables

### 2. Database Configuration

The database configuration is in `config_db.php`:
- Server: localhost
- Username: root
- Password: (empty)
- Database: ayalagch_db

If your MySQL setup is different, update these values in `config_db.php`.

### 3. Files Structure

- **10.php** - Main admin interface (replaces admin_page.php)
- **config_db.php** - Database connection configuration
- **save_data.php** - API endpoint for all database operations
- **database_schema.sql** - SQL schema to create database and tables

### 4. Features Connected to Database

✅ **Dashboard Module**
- Total products count
- Daily sales calculation
- Total revenue
- Low stock alerts

✅ **Inventory Module**
- Add new products (saved to database)
- View all products (loaded from database)
- Delete products (removed from database)
- Stock management

✅ **Sales Module**
- Process sales (saved to database)
- Sales history (loaded from database)
- Automatic stock deduction

✅ **Analytics Module**
- Charts based on database data
- Stock level visualization

✅ **Gallery Module**
- Product gallery (loaded from database)

### 5. Login Integration

The login system has been updated to redirect to `10.php`:
- Admin users logging in from `login_register` will be redirected to `../aylagch_4x4/10.php`

### 6. Testing

1. Make sure XAMPP is running (Apache and MySQL)
2. Navigate to: http://localhost/aylagch_4x4/10.php
3. Add a test product
4. Process a test sale
5. Check phpMyAdmin to verify data is being saved

### 7. Troubleshooting

**Database connection error:**
- Check if MySQL is running in XAMPP
- Verify database name is `ayalagch_db`
- Check `config_db.php` credentials

**No data showing:**
- Make sure you've run `database_schema.sql` to create tables
- Check browser console for JavaScript errors
- Verify `save_data.php` is accessible

**Products/Sales not saving:**
- Check browser console for errors
- Verify database tables exist
- Check file permissions on PHP files

### 8. Database Tables

**products table:**
- id (auto increment)
- name
- price
- stock
- category
- image (base64 encoded)
- created_at
- updated_at

**sales table:**
- id (auto increment)
- sale_date
- product_name
- quantity
- total_price
- customer_name
- created_at
