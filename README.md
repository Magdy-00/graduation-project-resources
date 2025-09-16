# RefCollect - Collaborative Reference Management

A collaborative reference management system for graduation projects.

## Features

- User authentication and registration
- Add and categorize references (documents, links, papers, tools, people, etc.)
- Comment system for collaborative discussions
- Search and filter functionality
- Responsive web interface

## Database Schema

The application uses the following MySQL tables:

- `users` - User accounts with authentication
- `references_entity` - Reference entries with categories and metadata
- `comments` - Comments on references for collaboration

## Setup Instructions

1. **Database Setup**

   - Ensure you have MySQL/MariaDB running
   - Update database credentials in `db.php` if needed
   - Run `setup_database.php` to create the database and tables
   - This will also create a default admin account (username: admin, password: admin123)

2. **Web Server**

   - Place files in your web server directory (e.g., XAMPP htdocs)
   - Ensure PHP is enabled
   - Access the application through your web browser

3. **First Use**
   - Navigate to `setup_database.php` to initialize the database
   - Visit `login.php` to log in with the default admin account
   - Or create a new account via `register.php`

## File Structure

- `index.php` - Main dashboard showing all references
- `login.php` - User login page
- `register.php` - User registration page
- `add_reference.php` - Handle adding new references
- `add_comment.php` - Handle adding comments
- `auth.php` - Authentication helper functions
- `db.php` - Database connection
- `logout.php` - User logout
- `setup_database.php` - Database initialization script
- `css/style.css` - Styling
- `js/app.js` - JavaScript functionality

## Usage

1. **Adding References**: Click "+ Add Reference" to add new items
2. **Categorizing**: Choose from document, link, paper, tool, person, or other
3. **Commenting**: Add comments to any reference for collaboration
4. **Searching**: Use the search box to find specific references
5. **Filtering**: Click category buttons to filter by type

## Requirements

- PHP 7.0+
- MySQL 5.7+ or MariaDB
- Web server (Apache, Nginx, etc.)
