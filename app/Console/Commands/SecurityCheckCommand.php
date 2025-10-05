<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class SecurityCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:check {--fix : Attempt to fix security issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive security checks on the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔒 Starting security check...');
        $this->newLine();

        $issues = [];
        $fixes = [];

        // Check environment file security
        $this->checkEnvironmentSecurity($issues, $fixes);

        // Check file permissions
        $this->checkFilePermissions($issues, $fixes);

        // Check configuration security
        $this->checkConfigurationSecurity($issues, $fixes);

        // Check directory security
        $this->checkDirectorySecurity($issues, $fixes);

        // Check for sensitive files
        $this->checkSensitiveFiles($issues, $fixes);

        // Check database security
        $this->checkDatabaseSecurity($issues, $fixes);

        // Display results
        $this->displayResults($issues, $fixes);

        // Apply fixes if requested
        if ($this->option('fix') && !empty($fixes)) {
            $this->applyFixes($fixes);
        }

        return empty($issues) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Check environment file security
     */
    private function checkEnvironmentSecurity(array &$issues, array &$fixes): void
    {
        $this->info('Checking environment security...');

        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        // Check if .env file exists
        if (!File::exists($envPath)) {
            $issues[] = 'Environment file (.env) not found';
            return;
        }

        // Check if .env.example exists
        if (!File::exists($envExamplePath)) {
            $issues[] = 'Environment example file (.env.example) not found';
        }

        // Check .env file permissions
        $envPermissions = substr(sprintf('%o', fileperms($envPath)), -4);
        if ($envPermissions !== '0600') {
            $issues[] = "Environment file has incorrect permissions: {$envPermissions} (should be 0600)";
            $fixes[] = "chmod 0600 {$envPath}";
        }

        // Check for sensitive data in .env
        $envContent = File::get($envPath);
        if (strpos($envContent, 'password') !== false && strpos($envContent, 'your_password_here') === false) {
            $this->warn('⚠️  Environment file contains password - ensure it\'s properly secured');
        }

        // Check for debug mode in production
        if (env('APP_ENV') === 'production' && env('APP_DEBUG') === 'true') {
            $issues[] = 'Debug mode is enabled in production environment';
        }
    }

    /**
     * Check file permissions
     */
    private function checkFilePermissions(array &$issues, array &$fixes): void
    {
        $this->info('Checking file permissions...');

        $directories = [
            'storage' => '0755',
            'storage/app' => '0755',
            'storage/logs' => '0755',
            'bootstrap/cache' => '0755',
        ];

        foreach ($directories as $dir => $expectedPerms) {
            $path = base_path($dir);
            if (File::exists($path)) {
                $permissions = substr(sprintf('%o', fileperms($path)), -4);
                if ($permissions !== $expectedPerms) {
                    $issues[] = "Directory {$dir} has incorrect permissions: {$permissions} (should be {$expectedPerms})";
                    $fixes[] = "chmod {$expectedPerms} {$path}";
                }
            }
        }
    }

    /**
     * Check configuration security
     */
    private function checkConfigurationSecurity(array &$issues, array &$fixes): void
    {
        $this->info('Checking configuration security...');

        // Check session configuration
        if (Config::get('session.secure') === false && env('APP_ENV') === 'production') {
            $issues[] = 'Session secure flag is disabled in production';
        }

        if (Config::get('session.http_only') === false) {
            $issues[] = 'Session HTTP only flag is disabled';
        }

        // Check CORS configuration
        $corsConfig = Config::get('cors');
        if (isset($corsConfig['allowed_origins']) && in_array('*', $corsConfig['allowed_origins'])) {
            $issues[] = 'CORS allows all origins (*) - this is insecure';
        }

        // Check cache configuration
        if (Config::get('cache.default') === 'file' && env('APP_ENV') === 'production') {
            $this->warn('⚠️  Using file cache in production - consider using Redis or Memcached');
        }
    }

    /**
     * Check directory security
     */
    private function checkDirectorySecurity(array &$issues, array &$fixes): void
    {
        $this->info('Checking directory security...');

        $sensitiveDirs = [
            'storage/app/private',
            'storage/logs',
            'config',
        ];

        foreach ($sensitiveDirs as $dir) {
            $path = base_path($dir);
            if (File::exists($path)) {
                $htaccessPath = $path . '/.htaccess';
                if (!File::exists($htaccessPath)) {
                    $issues[] = "Missing .htaccess file in {$dir}";
                    $fixes[] = "echo 'Deny from all' > {$htaccessPath}";
                }
            }
        }
    }

    /**
     * Check for sensitive files
     */
    private function checkSensitiveFiles(array &$issues, array &$fixes): void
    {
        $this->info('Checking for sensitive files...');

        $sensitiveFiles = [
            '.env',
            'composer.lock',
            'package-lock.json',
            'yarn.lock',
        ];

        foreach ($sensitiveFiles as $file) {
            $path = base_path($file);
            if (File::exists($path)) {
                $publicPath = public_path($file);
                if (File::exists($publicPath)) {
                    $issues[] = "Sensitive file {$file} is accessible in public directory";
                    $fixes[] = "rm {$publicPath}";
                }
            }
        }
    }

    /**
     * Check database security
     */
    private function checkDatabaseSecurity(array &$issues, array &$fixes): void
    {
        $this->info('Checking database security...');

        // Check database connection security
        if (Config::get('database.default') === 'mysql' && !Config::get('database.connections.mysql.options.ssl')) {
            $this->warn('⚠️  MySQL connection is not using SSL - consider enabling SSL in production');
        }

        // Check for default database credentials
        $dbPassword = env('DB_PASSWORD');
        if (empty($dbPassword) || $dbPassword === 'password' || $dbPassword === 'root') {
            $issues[] = 'Database password is weak or default';
        }
    }

    /**
     * Display security check results
     */
    private function displayResults(array $issues, array $fixes): void
    {
        $this->newLine();
        $this->info('🔍 Security Check Results:');
        $this->newLine();

        if (empty($issues)) {
            $this->info('✅ No security issues found!');
        } else {
            $this->error('❌ Found ' . count($issues) . ' security issues:');
            $this->newLine();

            foreach ($issues as $issue) {
                $this->line("  • {$issue}");
            }

            if (!empty($fixes)) {
                $this->newLine();
                $this->info('🔧 Available fixes:');
                foreach ($fixes as $fix) {
                    $this->line("  • {$fix}");
                }
                $this->newLine();
                $this->info('Run with --fix option to apply fixes automatically');
            }
        }
    }

    /**
     * Apply security fixes
     */
    private function applyFixes(array $fixes): void
    {
        $this->newLine();
        $this->info('🔧 Applying security fixes...');

        foreach ($fixes as $fix) {
            $this->line("Executing: {$fix}");
            exec($fix, $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info("  ✅ Success");
            } else {
                $this->error("  ❌ Failed");
            }
        }

        $this->newLine();
        $this->info('Security fixes applied. Please run the security check again to verify.');
    }
}