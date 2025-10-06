# Civic Tech Field Guide (CTFG) - Project Documentation

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Recent Improvements & Changes](#recent-improvements--changes)
3. [Local Development Setup](#local-development-setup)
4. [Production Deployment with Kamal](#production-deployment-with-kamal)
5. [Airtable Integration](#airtable-integration)
6. [Image Optimization System](#image-optimization-system)
7. [Database Schema](#database-schema)
8. [API Endpoints](#api-endpoints)
9. [Troubleshooting](#troubleshooting)
10. [Contributing](#contributing)

## 🎯 Project Overview

The Civic Tech Field Guide is a comprehensive directory of civic technology tools, resources, and organizations. It serves as a centralized hub for discovering and accessing digital tools that support democracy, human rights, and civic engagement.

### Key Features
- **Comprehensive Directory**: Lists of projects, organizations, and resources
- **Advanced Search**: Filter by categories, tags, languages, and more
- **Airtable Integration**: Real-time sync with Airtable database
- **Image Optimization**: Automatic image processing and optimization
- **Responsive Design**: Mobile-first approach with optimized layouts
- **Multi-language Support**: Content in multiple languages

## 🚀 Recent Improvements & Changes

### Laravel Framework Upgrade (October 2025)
- **Upgraded from Laravel 9 to Laravel 10**
- **Updated PHP compatibility** to support PHP 8.4
- **Resolved deprecation warnings** and compatibility issues
- **Updated dependencies**: PHPUnit, Livewire, CORS, and other packages

### Image Optimization System (October 2025)
- **Implemented comprehensive image optimization**
- **Added thumbnail generation** for faster loading
- **Mobile-responsive image sizing**
- **Automatic image compression** and format optimization
- **Smart aspect ratio handling** for logos and wide images

#### Image Optimization Features:
- **Desktop thumbnails**: 200x150px optimized containers
- **Mobile thumbnails**: 300x220px (tablet), 280x200px (mobile)
- **Smart logo handling**: Prevents distortion of wide logos
- **Lazy loading**: Improves page load performance
- **Fallback system**: Graceful degradation for missing images

### Performance Improvements
- **Faster page loading** with optimized thumbnails
- **Reduced bandwidth usage** with compressed images
- **Better mobile experience** with responsive image sizing
- **Improved SEO** with proper image optimization

### UI/UX Enhancements
- **Standardized image containers** for consistent layout
- **Better mobile responsiveness** with larger images on mobile
- **Improved logo readability** with smart aspect ratio handling
- **Enhanced visual hierarchy** with optimized image sizing

## 🛠 Local Development Setup

### Prerequisites
- **PHP 8.4+**
- **Composer**
- **Node.js & NPM**
- **MySQL/PostgreSQL**
- **Git**

### Installation Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd CTFG
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**
Update `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ctfg_local
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Seed the database (optional)**
```bash
php artisan db:seed
```

9. **Start the development server**
```bash
php artisan serve
```

10. **Build frontend assets**
```bash
npm run dev
# or for production
npm run build
```

### Airtable Configuration
1. **Get Airtable API credentials**
   - Create an Airtable account
   - Generate API key
   - Get base ID from your Airtable URL

2. **Configure environment variables**
```env
AIRTABLE_API_KEY=your_api_key
AIRTABLE_BASE_ID=your_base_id
```

3. **Sync data from Airtable**
```bash
php artisan sync:tables
```

## 🚀 Production Deployment with Kamal

### Prerequisites
- **Docker** installed on your server
- **Kamal** gem installed
- **SSH access** to your server
- **Domain name** configured

### Kamal Setup

1. **Install Kamal**
```bash
gem install kamal
```

2. **Initialize Kamal**
```bash
kamal init
```

3. **Configure `config/deploy.yml`**
```yaml
service: ctfg
image: your-registry/ctfg

servers:
  - 192.168.0.1

registry:
  server: your-registry.com
  username: your-username
  password: <%= ENV['REGISTRY_PASSWORD'] %>

env:
  secret: <%= ENV['RAILS_MASTER_KEY'] %>
  RAILS_ENV: production
  DATABASE_URL: <%= ENV['DATABASE_URL'] %>
  AIRTABLE_API_KEY: <%= ENV['AIRTABLE_API_KEY'] %>
  AIRTABLE_BASE_ID: <%= ENV['AIRTABLE_BASE_ID'] %>

builder:
  dockerfile: Dockerfile

accessories:
  mysql:
    image: mysql:8.0
    host: mysql
    port: 3306
    env:
      MYSQL_ROOT_PASSWORD: <%= ENV['MYSQL_ROOT_PASSWORD'] %>
      MYSQL_DATABASE: <%= ENV['MYSQL_DATABASE'] %>
      MYSQL_USER: <%= ENV['MYSQL_USER'] %>
      MYSQL_PASSWORD: <%= ENV['MYSQL_PASSWORD'] %>
    volumes:
      - mysql_data:/var/lib/mysql
```

4. **Deploy to production**
```bash
kamal deploy
```

### Environment Variables for Production
```env
# Database
DATABASE_URL=mysql://user:password@mysql:3306/ctfg_production

# Airtable
AIRTABLE_API_KEY=your_production_api_key
AIRTABLE_BASE_ID=your_production_base_id

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Registry
REGISTRY_PASSWORD=your_registry_password

# MySQL
MYSQL_ROOT_PASSWORD=secure_root_password
MYSQL_DATABASE=ctfg_production
MYSQL_USER=ctfg_user
MYSQL_PASSWORD=secure_user_password
```

## 🔗 Airtable Integration

### Overview
The project integrates with Airtable as the primary data source, providing real-time synchronization of projects, organizations, and resources.

### Airtable Tables Structure

#### Main Tables:
- **Listings**: Projects, organizations, and resources
- **Media**: Images and files associated with listings
- **Categories**: Classification system for listings
- **Tags**: Additional metadata for listings
- **Organizations**: Organization information
- **Locations**: Geographic data

### Sync Process

1. **Automatic Sync**
```bash
php artisan sync:tables
```

2. **Manual Sync (if needed)**
```bash
php artisan sync:listings
php artisan sync:media
php artisan sync:categories
php artisan sync:tags
```

### Media Handling
- **Automatic download** of Airtable images to local storage
- **Prevents URL expiration** by storing images locally
- **Optimization** of downloaded images
- **Thumbnail generation** for better performance

### API Integration
The system uses Airtable's REST API to:
- Fetch data from all tables
- Handle pagination for large datasets
- Process file attachments
- Maintain data consistency

## 🖼 Image Optimization System

### Architecture
The image optimization system provides automatic processing of images for optimal web performance.

### Components

#### 1. Media Model (`app/Models/Media.php`)
- **Local storage management**
- **Thumbnail generation**
- **Image optimization**
- **URL handling**

#### 2. Optimization Command (`app/Console/Commands/OptimizeImages.php`)
```bash
# Optimize all images
php artisan images:optimize

# Generate thumbnails
php artisan images:optimize --generate-thumbnails

# Process limited number
php artisan images:optimize --limit=10
```

#### 3. Thumbnail Generation
- **Desktop thumbnails**: 200x150px containers
- **Mobile thumbnails**: 300x220px (tablet), 280x200px (mobile)
- **Smart aspect ratio handling** for logos
- **Automatic compression** and optimization

### Image Processing Features
- **Aspect ratio preservation**
- **Smart logo handling** (prevents distortion)
- **Multiple size generation**
- **Format optimization** (JPEG with quality control)
- **Lazy loading** implementation
- **Fallback system** for missing images

### File Structure
```
storage/app/public/media/
├── [original_images].jpg
├── thumbnails/
│   └── [image_id]_thumb.jpg
└── mobile-thumbnails/
    └── [image_id]_mobile_thumb.jpg
```

## 🗄 Database Schema

### Key Tables

#### Listings
- `id`: Primary key
- `airtable_id`: Airtable record ID
- `name`: Project/organization name
- `description`: Detailed description
- `website_url`: Website URL
- `status`: Active/Inactive status
- `created_at`, `updated_at`: Timestamps

#### Media
- `id`: Primary key
- `airtable_id`: Airtable record ID
- `link`: Original image URL
- `local_path`: Local storage path
- `is_local`: Boolean flag for local storage
- `type`: Media type (image, document, etc.)

#### Categories & Tags
- Hierarchical classification system
- Many-to-many relationships with listings
- Support for multiple languages

### Migrations
```bash
# Run all migrations
php artisan migrate

# Create new migration
php artisan make:migration create_table_name

# Rollback migrations
php artisan migrate:rollback
```

## 🔌 API Endpoints

### Public Endpoints
- `GET /`: Home page with listings
- `GET /listing/{slug}`: Individual listing details
- `GET /search`: Search functionality
- `GET /categories`: List all categories
- `GET /tags`: List all tags

### Admin Endpoints
- `POST /sync`: Trigger Airtable sync
- `GET /admin`: Admin dashboard
- `POST /admin/listings`: Manage listings
- `POST /admin/media`: Manage media

### API Routes
```php
// Public routes
Route::get('/', [GuestController::class, 'index']);
Route::get('/listing/{slug}', [GuestController::class, 'show']);
Route::get('/search', [GuestController::class, 'search']);

// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::post('/sync', [SyncController::class, 'sync']);
});
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Image Loading Issues
```bash
# Check storage link
php artisan storage:link

# Regenerate thumbnails
php artisan images:optimize --generate-thumbnails

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### 2. Airtable Sync Issues
```bash
# Check API credentials
php artisan tinker
>>> config('airtable.api_key')

# Test connection
php artisan sync:test

# Manual sync
php artisan sync:tables
```

#### 3. Database Issues
```bash
# Check database connection
php artisan migrate:status

# Reset database
php artisan migrate:fresh --seed

# Check migrations
php artisan migrate:status
```

#### 4. Performance Issues
```bash
# Optimize images
php artisan images:optimize

# Clear caches
php artisan optimize

# Check file permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Log Files
- **Application logs**: `storage/logs/laravel.log`
- **Airtable sync logs**: Check for sync-specific errors
- **Image processing logs**: Check for optimization errors

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## 🤝 Contributing

### Development Workflow
1. **Fork the repository**
2. **Create a feature branch**
3. **Make your changes**
4. **Test thoroughly**
5. **Submit a pull request**

### Code Standards
- **PSR-12** coding standards
- **Laravel best practices**
- **Comprehensive testing**
- **Documentation updates**

### Testing
```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=TestName

# Generate test coverage
php artisan test --coverage
```

### Pull Request Guidelines
- **Clear description** of changes
- **Test coverage** for new features
- **Documentation updates**
- **Screenshots** for UI changes

## 📞 Support

### Getting Help
- **Documentation**: Check this file and inline code comments
- **Issues**: Create GitHub issues for bugs or feature requests
- **Discussions**: Use GitHub discussions for questions

### Contact
- **Project Maintainer**: [Your Name]
- **Email**: [your-email@example.com]
- **GitHub**: [your-github-username]

---

## 📝 Changelog

### Version 2.0.0 (October 2025)
- ✅ Upgraded Laravel 9 → 10
- ✅ Implemented comprehensive image optimization
- ✅ Added mobile-responsive image sizing
- ✅ Improved logo handling and aspect ratios
- ✅ Enhanced performance with optimized thumbnails
- ✅ Added lazy loading for better UX
- ✅ Resolved PHP 8.4 compatibility issues

### Version 1.0.0 (Initial Release)
- ✅ Basic Airtable integration
- ✅ Project directory functionality
- ✅ Search and filtering
- ✅ Responsive design
- ✅ Multi-language support

---

*Last updated: October 5, 2025*
