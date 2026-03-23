# Friends of the Deaf Foundation - Laravel Application

A modern Laravel-based CMS for the Friends of the Deaf Foundation, built with accessibility and mobile responsiveness in mind.

## Features

- **Content Management System**: Full CMS for pages and blog posts
- **Events Management**: Comprehensive event system with registration
- **Mobile-First Design**: Responsive layout optimized for all devices
- **Accessibility**: WCAG 2.1 AA compliant with deaf community focus
- **SEO Optimized**: Meta tags, structured data, and clean URLs
- **Modern Tech Stack**: Laravel 12, Blade templates, MySQL/PostgreSQL

## Quick Start

### Prerequisites

- PHP 8.3+
- Composer
- MySQL 8.0+ or PostgreSQL 15+
- Node.js 18+ (for asset compilation)
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd fdf-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit your `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fdf_foundation
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seed data**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

### Local MySQL Helper (Windows + XAMPP)

If you want an isolated local MySQL/MariaDB instance for this project without using XAMPP's shared `C:\xampp\mysql\data` directory, use:

```bash
composer db:start-local
composer db:stop-local
```

The helper starts a project-local MariaDB instance on `127.0.0.1:3307` with data stored under `%LOCALAPPDATA%\Codex\fdf-laravel-mysql-v2`.

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Compile assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8001
   ```

   Visit `http://127.0.0.1:8001` in your browser.

## Content Management

### Pages

The application comes pre-loaded with essential pages:

- **Home**: Homepage with hero section and featured content
- **About**: Mission, vision, and organization information
- **Contact**: Contact details and form
- **Programs**: Overview of foundation programs
- **Donate**: Donation information and calls-to-action

### Managing Pages

Pages are managed through the database. To modify content:

1. **Via Database Seeder**: Edit `database/seeders/ContentSeeder.php`
2. **Via Database**: Directly modify the `pages` table
3. **Via Admin Panel**: Future admin interface (planned)

### Events

The events system includes:

- Event creation and management
- Registration tracking
- Virtual and in-person event support
- Featured events for homepage display

## Project Structure

```
./
├── docs/
├── app/
│   ├── Http/Controllers/
│   │   ├── PageController.php     # Page management
│   │   └── EventController.php    # Event management
│   ├── Models/
│   │   ├── Page.php              # Page model
│   │   └── Event.php             # Event model
│   └── Providers/
├── database/
│   ├── migrations/
│   │   ├── 2024_02_24_000001_create_pages_table.php
│   │   └── 2024_02_24_000002_create_events_table.php
│   └── seeders/
│       ├── ContentSeeder.php     # Import scraped content
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php     # Main layout
│       ├── pages/
│       │   ├── home.blade.php    # Homepage
│       │   └── show.blade.php    # Page template
│       └── events/
│           ├── index.blade.php   # Events listing
│           └── show.blade.php    # Event details
└── routes/
    └── web.php                   # Route definitions
```

## Development

### Adding New Pages

1. **Create Route**: Add to `routes/web.php`
   ```php
   Route::get('/your-page', [PageController::class, 'show'])->name('pages.your-page');
   ```

2. **Add Page to Database**: Insert into `pages` table or update seeder

### Adding New Events

Events are managed through the `Event` model and can be created via:

- Database seeder
- Admin interface (future)
- Direct database insertion

### Customizing Templates

- **Layout**: `resources/views/layouts/app.blade.php`
- **Pages**: `resources/views/pages/`
- **Events**: `resources/views/events/`

## Deployment

### Production Setup

1. **Server Requirements**
   - PHP 8.3+
   - MySQL 8.0+ or PostgreSQL 15+
   - Web server (Nginx/Apache)
   - SSL certificate

2. **Environment Configuration**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

3. **Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

4. **Scheduler Setup**
   Add to crontab:
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

### Security Considerations

- Regular security updates
- HTTPS enforcement
- Database encryption
- Input validation and sanitization
- Rate limiting for forms
- Regular backups

## Content Migration

The scraped content from the original WordPress site has been migrated and is available in:

- **Scraped Data**: `../scraped-content/` directory
- **Migration Guide**: `../scraped-content/LARAVEL_MIGRATION_GUIDE.md`
- **Scraping Summary**: `../scraped-content/SCRAPING_SUMMARY.md`

### Manual Content Updates

To update content from the scraped data:

1. Review scraped content in `../scraped-content/content/`
2. Update page content in the database or seeder
3. Update media files in `storage/app/public/`
4. Test changes in development environment

## Accessibility Features

This application is built with accessibility in mind:

- **Semantic HTML**: Proper heading structure and landmarks
- **ARIA Labels**: Screen reader support
- **Keyboard Navigation**: Full keyboard accessibility
- **Color Contrast**: WCAG compliant color ratios
- **Focus Indicators**: Clear focus states
- **Responsive Design**: Mobile-first approach
- **Alt Text**: Descriptive alternative text for images

## Future Enhancements

Planned features for future releases:

- **Admin Panel**: Full content management interface
- **User Authentication**: Member accounts and profiles
- **Blog System**: News and article management
- **Donation Integration**: Online payment processing
- **Event Registration**: Online event signup
- **Multi-language Support**: ASL video integration
- **Mobile App**: Native mobile application

## Support

For support and questions:

- **Email**: info@friendsofthedeaffoundation.org
- **Documentation**: Check the `/docs` directory
- **Issues**: Report via project issue tracker

## License

This project is proprietary to Friends of the Deaf Foundation.

---

**Note**: This application was built as part of a website migration from WordPress to Laravel, maintaining all existing content while improving performance, security, and accessibility.
