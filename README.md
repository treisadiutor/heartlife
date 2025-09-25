# HeartLife - Personal Wellness Tracker

HeartLife is a personal wellness tracking application built with PHP that helps users monitor and manage their physical and mental health through various tracking modules including mood, sleep, BMI, notes, and self-care activities.

## Features

### Core Modules
- 🎭 **Mood Tracker** - Log and monitor daily mood patterns
- 😴 **Sleep Tracker** - Track sleep patterns and get recommendations
- 📊 **BMI Tracker** - Monitor Body Mass Index over time
- 📝 **Notes** - Personal journaling and note-taking
- 🧘 **Self-Care** - Checklist for daily self-care activities
- 📈 **Reports** - Wellness reports and analytics
- 💡 **Daily Quotes** - Motivational quotes for daily inspiration

### Additional Features
- 👤 **User Profile Management** - Update personal information and profile picture
- 🔐 **User Authentication** - Secure login and registration system
- 📊 **Dashboard** - Overview of all wellness metrics

## Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML, CSS (Tailwind CSS), JavaScript
- **Server**: Apache with mod_rewrite
- **Development Environment**: XAMPP

## Prerequisites

Before installing HeartLife, ensure you have:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer (for dependency management)
- XAMPP/WAMP/LAMP (recommended for local development)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/treisadiutor/heartlife.git
cd heartlife
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Database Setup

1. Create a MySQL database named `heartlife_db`
2. Import the database schema (heartlife_db.sql):
   ```bash
   mysql -u your_username -p heartlife_db < database/heartlife_db.sql
   ```

### 4. Environment Configuration

1. Copy the environment template:
   ```bash
   cp .env.example .env
   ```

2. Edit the `.env` file with your database credentials:
   ```env
   DB_HOST=localhost
   DB_NAME=heartlife_db
   DB_USER=your_database_username
   DB_PASS=your_database_password
   ```

### 5. Web Server Configuration

#### For XAMPP (Windows/Mac/Linux):
1. Place the project folder in `xampp/htdocs/`
2. Start Apache and MySQL in XAMPP Control Panel
3. Access the application at `http://localhost/HeartLife`

#### For Apache Virtual Host:
1. Create a virtual host pointing to the project directory
2. Ensure mod_rewrite is enabled
3. The `.htaccess` file is already configured for URL rewriting

## Access URLs

Once installed, you can access different parts of the application:

### Authentication
- **Login**: `http://localhost/HeartLife/login`
- **Sign Up**: `http://localhost/HeartLife/signup`
- **Dashboard**: `http://localhost/HeartLife/dashboard`

### Main Features
- **Mood Tracker**: `http://localhost/HeartLife/myMood`
- **Sleep Tracker**: `http://localhost/HeartLife/sleepTracker`
- **BMI Tracker**: `http://localhost/HeartLife/bmiTracker`
- **Notes**: `http://localhost/HeartLife/notes`
- **Self Care**: `http://localhost/HeartLife/self-care`
- **Reports**: `http://localhost/HeartLife/reports`
- **Profile**: `http://localhost/HeartLife/profile`

## Usage

### Getting Started
1. **Register** a new account or **login** with existing credentials
2. Complete your **profile setup** with basic information
3. Start tracking your wellness data through various modules
4. View your **dashboard** for a comprehensive overview
5. Generate **reports** to analyze your wellness trends

### Key Workflows

#### Mood Tracking
- Log daily moods with emotional states
- View mood patterns and trends
- Get insights into emotional wellness

#### Sleep Monitoring
- Record sleep duration and quality
- Receive personalized sleep recommendations
- Track sleep patterns over time

#### BMI Management
- Log weight and height measurements
- Calculate and track BMI automatically
- Monitor weight trends and health metrics

## Configuration

### URL Rewriting
The application uses clean URLs through Apache's mod_rewrite. The `.htaccess` file is configured to:
- Enable URL rewriting
- Redirect all requests through `index.php`
- Set the base path to `/HeartLife/`

### Environment Variables
All sensitive configuration is stored in the `.env` file:
```env
DB_HOST=localhost
DB_NAME=heartlife_db
DB_USER=root
DB_PASS=
```

### File Uploads
Profile pictures are stored in `assets/images/profile/` directory. Ensure this directory has write permissions.

## Project Structure

```
HeartLife/
├── app/
│   ├── controllers/         # Application controllers
│   ├── models/              # Data models
│   └── views/               # View templates
├── assets/
│   ├── css/                 # Stylesheets
│   ├── images/              # Static images
│   └── fonts/               # Custom fonts
├── config/
│   ├── database.php         # Database configuration
│   └── constants.php        # Application constants
├── core/
│   ├── Controller.php       # Base controller class
│   └── Router.php           # URL routing system
├── routes/
│   └── web.php              # Route definitions
├── vendor/                  # Composer dependencies
├── .env                     # Environment configuration
├── .htaccess                # Apache rewrite rules
├── composer.json            # PHP dependencies
└── index.php                # Application entry point
```

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check your `.env` file configuration
   - Ensure MySQL service is running
   - Verify database credentials

2. **404 Errors on Routes**
   - Ensure Apache mod_rewrite is enabled
   - Check `.htaccess` file is present and readable
   - Verify the correct base path in `.htaccess`

3. **Blank Page/PHP Errors**
   - Enable PHP error reporting in development
   - Check PHP error logs
   - Ensure all required PHP extensions are installed

4. **File Upload Issues**
   - Check directory permissions for `assets/images/profile/`
   - Verify PHP upload settings in `php.ini`

### Development Mode
Enable error reporting for development by uncommenting these lines in `index.php`:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Developer

**treisadiutor**
- GitHub: [@treisadiutor](https://github.com/treisadiutor)

## Support

If you encounter any issues or have questions:

1. Check the troubleshooting section above
2. Create an issue on GitHub
3. Contact the developer through GitHub

---

### Happy Wellness Tracking with HeartLife! 

Made with ❤️ for personal wellness and mental health awareness.