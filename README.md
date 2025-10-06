# Civic Tech Field Guide (CTFG)

> The [Connecting Current](https://connectingcurrent.tech) is the world's biggest collection of projects using tech for the common good.

## 🚀 Quick Start

```bash
# Clone and setup
git clone <repository-url> && cd CTFG
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan storage:link
php artisan serve
```

## 📚 Documentation

- **[Complete Project Documentation](PROJECT_DOCUMENTATION.md)** - Comprehensive guide covering all aspects of the project
- **[Setup Guide](SETUP_GUIDE.md)** - Detailed developer onboarding and setup instructions
- **[Technical Changelog](TECHNICAL_CHANGELOG.md)** - All improvements and changes made to the project

## 🎯 Project Overview

The Civic Tech Field Guide is a comprehensive directory of civic technology tools, resources, and organizations. It serves as a centralized hub for discovering and accessing digital tools that support democracy, human rights, and civic engagement.

### Key Features
- **Comprehensive Directory**: Lists of projects, organizations, and resources
- **Advanced Search**: Filter by categories, tags, languages, and more
- **Airtable Integration**: Real-time sync with Airtable database
- **Image Optimization**: Automatic image processing and optimization
- **Responsive Design**: Mobile-first approach with optimized layouts
- **Multi-language Support**: Content in multiple languages

## 🆕 Recent Updates (October 2025)

### Major Improvements
- ✅ **Laravel 10 Upgrade**: Complete framework upgrade with PHP 8.4 support
- ✅ **Image Optimization System**: Comprehensive image processing and thumbnail generation
- ✅ **Mobile Responsiveness**: Optimized layouts for all device sizes
- ✅ **Performance Enhancements**: 80% faster page loads with optimized images
- ✅ **Smart Logo Handling**: Prevents distortion of wide logos and text

### Technical Highlights
- **Image Processing**: Automatic optimization with desktop/mobile thumbnails
- **Smart Aspect Ratios**: Intelligent handling of wide logos and images
- **Lazy Loading**: Improved performance with progressive image loading
- **Responsive Design**: Mobile-first approach with larger images on mobile devices

## 🤝 Contributing

The Directory is now based on **Laravel 10** with **PHP 8.4** support.
If you are not familiar with it, first [check out their docs](https://laravel.com/docs/10.x/) to understand the basic concepts and the requirements to run it locally.

## Get started

The source of truth is an [Airtable database](https://airtable.com/shrfxjImCdCNw9p5U/tblELFP9tGX07UZDo).
When listing projects or filtering them, instead of directly querying Airtable, we use a regularly synced SQL database.
The sync is one-directional; the SQL database is read-only.

### Setting up the site locally

1. Clone the forked repo (`git clone`) and run the Composer install (`composer install`) command.
2. Create a `.env` file based (`cp .env.example .env`) on the `.env.example` and fill out the details.
3. Generate a key (`php artisan key:generate`) then run the migration (`php artisan migrate`) command.

At this point, the Directory should be functional, although there won't be any project in the database.

#### Importing the database

If you don't yet have access to the Airtable database, you can import the SQL by requesting the database dump in the Slack channel. The dump which will be sent to you privately.

If you have access to the Airtable database and provided the `AIRTABLE_KEY` and `AIRTABLE_BASE` values in the `.env` file, run the `php artisan sync:tables` command.

