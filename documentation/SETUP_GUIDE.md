# CTFG Setup Guide - Complete Developer Onboarding

## 🚀 Quick Start (5 Minutes)

### Prerequisites Check
```bash
# Check PHP version (8.4+ required)
php --version

# Check Composer
composer --version

# Check Node.js
node --version
npm --version

# Check Git
git --version
```

### One-Command Setup
```bash
# Clone and setup
git clone <repository-url> && cd CTFG
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan storage:link
php artisan serve
```

## 📋 Detailed Setup Instructions

### 1. Environment Setup

#### Clone Repository
```bash
git clone <repository-url>
cd CTFG
```

#### Install Dependencies
```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

#### Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Database Setup
```bash
# Create database (MySQL/PostgreSQL)
# Update .env with your database credentials

# Run migrations
php artisan migrate

# Create storage symlink
php artisan storage:link
```

### 2. Airtable Integration Setup

#### Get Airtable Credentials
1. Go to [Airtable.com](https://airtable.com)
2. Create account or login
3. Create a new base or use existing
4. Go to Account → Developer Hub
5. Generate API key
6. Get Base ID from your base URL

#### Configure Environment
```env
# Add to .env file
AIRTABLE_API_KEY=your_api_key_here
AIRTABLE_BASE_ID=your_base_id_here
```

#### Test Connection
```bash
# Test Airtable connection
php artisan tinker
>>> \Airtable::table('listings')->all();
```

#### Initial Data Sync
```bash
# Sync all data from Airtable
php artisan sync:tables
```

### 3. Image Optimization Setup

#### Verify Image Processing
```bash
# Check if Intervention Image is installed
composer show intervention/image

# Test image optimization
php artisan images:optimize --limit=5
```

#### Generate Thumbnails
```bash
# Generate thumbnails for all images
php artisan images:optimize --generate-thumbnails
```

### 4. Development Server

#### Start Laravel Server
```bash
php artisan serve
# Server runs on http://localhost:8000
```

#### Build Frontend Assets
```bash
# Development build
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

## 🐳 Docker Setup (Alternative)

### Using Docker Compose
```bash
# Create docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    environment:
      - APP_ENV=local
      - DB_HOST=mysql
      - DB_DATABASE=ctfg
      - DB_USERNAME=root
      - DB_PASSWORD=password
    depends_on:
      - mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: ctfg
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

### Run with Docker
```bash
# Build and start
docker-compose up -d

# Run commands in container
docker-compose exec app php artisan migrate
docker-compose exec app php artisan sync:tables
```

## 🔧 Configuration Files

### .env Configuration
```env
# Application
APP_NAME="Civic Tech Field Guide"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ctfg_local
DB_USERNAME=root
DB_PASSWORD=

# Airtable
AIRTABLE_API_KEY=your_api_key
AIRTABLE_BASE_ID=your_base_id

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Composer Configuration
```json
{
    "require": {
        "php": "^8.4",
        "laravel/framework": "^10.0",
        "intervention/image": "^3.11",
        "livewire/livewire": "^2.12"
    }
}
```

### Package.json Configuration
```json
{
    "devDependencies": {
        "laravel-mix": "^6.0",
        "sass": "^1.32",
        "sass-loader": "^12.0"
    }
}
```

## 🧪 Testing Setup

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=MediaTest

# Run with coverage
php artisan test --coverage
```

### Test Database
```bash
# Create test database
mysql -u root -p -e "CREATE DATABASE ctfg_test;"

# Update .env.testing
DB_DATABASE=ctfg_test
```

## 📱 Mobile Development

### Responsive Testing
```bash
# Test on different screen sizes
# Use browser dev tools or:
npm run dev
# Then test on mobile devices
```

### Image Optimization Testing
```bash
# Test image loading
php artisan tinker
>>> $media = App\Models\Media::first();
>>> $media->thumbnail_url;
>>> $media->mobile_thumbnail_url;
```

## 🚀 Production Deployment

### Kamal Deployment
```bash
# Install Kamal
gem install kamal

# Initialize
kamal init

# Configure config/deploy.yml
# Deploy
kamal deploy
```

### Environment Variables for Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DATABASE_URL=mysql://user:password@host:3306/database

# Airtable
AIRTABLE_API_KEY=production_api_key
AIRTABLE_BASE_ID=production_base_id

# Cache
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

## 🔍 Troubleshooting

### Common Issues & Solutions

#### 1. Composer Issues
```bash
# Clear composer cache
composer clear-cache

# Update dependencies
composer update

# Check for conflicts
composer check-platform-reqs
```

#### 2. Database Issues
```bash
# Check connection
php artisan tinker
>>> DB::connection()->getPdo();

# Reset database
php artisan migrate:fresh --seed

# Check migrations
php artisan migrate:status
```

#### 3. Storage Issues
```bash
# Check storage link
ls -la public/storage

# Recreate storage link
php artisan storage:link

# Check permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

#### 4. Airtable Issues
```bash
# Test API key
curl -H "Authorization: Bearer YOUR_API_KEY" \
     "https://api.airtable.com/v0/YOUR_BASE_ID/listings"

# Check sync logs
tail -f storage/logs/laravel.log
```

#### 5. Image Processing Issues
```bash
# Check GD extension
php -m | grep gd

# Test image processing
php artisan tinker
>>> $image = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
>>> $image->read('path/to/image.jpg');

# Regenerate thumbnails
php artisan images:optimize --generate-thumbnails
```

### Debug Mode
```bash
# Enable debug mode
# In .env file:
APP_DEBUG=true
LOG_LEVEL=debug

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 📊 Performance Optimization

### Image Optimization
```bash
# Optimize all images
php artisan images:optimize

# Generate thumbnails
php artisan images:optimize --generate-thumbnails

# Process in batches
php artisan images:optimize --limit=10
```

### Cache Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Database Optimization
```bash
# Check slow queries
# Enable query logging in .env:
DB_LOG_QUERIES=true

# Optimize database
php artisan optimize
```

## 🔐 Security Considerations

### Environment Security
```bash
# Never commit .env file
echo ".env" >> .gitignore

# Use strong API keys
# Rotate keys regularly
# Use environment-specific keys
```

### File Permissions
```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

### Database Security
```bash
# Use strong passwords
# Limit database user permissions
# Enable SSL connections in production
```

## 📚 Additional Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Airtable API Documentation](https://airtable.com/developers/web/api/introduction)
- [Intervention Image Documentation](https://image.intervention.io/)

### Tools
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- [Laravel Telescope](https://laravel.com/docs/telescope)
- [Kamal Documentation](https://kamal-deploy.org/)

### Community
- [Laravel Discord](https://discord.gg/laravel)
- [Laravel Reddit](https://reddit.com/r/laravel)
- [Laravel News](https://laravel-news.com/)

---

## ✅ Setup Checklist

- [ ] PHP 8.4+ installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] Database created and configured
- [ ] Environment file configured
- [ ] Dependencies installed
- [ ] Migrations run
- [ ] Storage link created
- [ ] Airtable credentials configured
- [ ] Initial sync completed
- [ ] Images optimized
- [ ] Development server running
- [ ] Frontend assets built
- [ ] Tests passing

---

*This guide covers everything needed to get the CTFG project running locally and in production. For additional help, refer to the main PROJECT_DOCUMENTATION.md file.*
