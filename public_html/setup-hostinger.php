<?php
/**
 * Setup Script untuk Hostinger
 * 
 * Jalankan sekali via browser: https://your-domain.com/setup-hostinger.php
 * HAPUS FILE INI SETELAH SELESAI!
 */

// Security: Set password untuk akses script ini
define('SETUP_PASSWORD', 'change-this-password-123'); // GANTI PASSWORD INI!

// Check password
if (!isset($_GET['password']) || $_GET['password'] !== SETUP_PASSWORD) {
    die('<h1>Access Denied</h1><p>Add ?password=your-password to URL</p>');
}

// Bootstrap Laravel
define('LARAVEL_START', microtime(true));
require __DIR__.'/../laravel_app/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Setup - Hostinger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
            padding: 10px;
            background: #f0f0f0;
            border-left: 4px solid #2196F3;
        }
        pre {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .warning {
            color: #ff9800;
            font-weight: bold;
            background: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .btn-danger {
            background: #f44336;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Setup untuk Hostinger</h1>
        
        <div class="warning">
            <strong>⚠️ PENTING:</strong> Hapus file ini setelah setup selesai!
        </div>

        <?php
        $errors = [];
        $success = [];

        // Check Environment
        echo '<h2>📋 Environment Check</h2>';
        echo '<div class="step">';
        
        echo '<p><strong>PHP Version:</strong> ' . PHP_VERSION;
        if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
            echo ' <span class="success">✓ OK</span>';
        } else {
            echo ' <span class="error">✗ Minimum PHP 8.2 required</span>';
            $errors[] = 'PHP version too old';
        }
        echo '</p>';

        // Check .env
        $envPath = __DIR__.'/../laravel_app/.env';
        echo '<p><strong>.env File:</strong> ';
        if (file_exists($envPath)) {
            echo '<span class="success">✓ Found</span>';
        } else {
            echo '<span class="error">✗ Not Found</span>';
            $errors[] = '.env file missing';
        }
        echo '</p>';

        // Check writable directories
        $writableDirs = [
            'storage' => __DIR__.'/../laravel_app/storage',
            'bootstrap/cache' => __DIR__.'/../laravel_app/bootstrap/cache',
        ];

        foreach ($writableDirs as $name => $dir) {
            echo '<p><strong>' . $name . ':</strong> ';
            if (is_writable($dir)) {
                echo '<span class="success">✓ Writable</span>';
            } else {
                echo '<span class="error">✗ Not Writable</span>';
                $errors[] = $name . ' not writable';
            }
            echo '</p>';
        }

        echo '</div>';

        // If errors, stop here
        if (!empty($errors)) {
            echo '<div class="warning">';
            echo '<h3>❌ Setup Cannot Continue</h3>';
            echo '<p>Please fix these issues:</p><ul>';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul></div>';
            echo '</div></body></html>';
            exit;
        }

        // Run setup commands
        try {
            // 1. Generate Key
            echo '<h2>🔑 1. Generate APP_KEY</h2>';
            echo '<div class="step">';
            Artisan::call('key:generate', ['--force' => true]);
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ APP_KEY generated</span>';
            echo '</div>';

            // 2. Test Database Connection
            echo '<h2>🗄️ 2. Test Database Connection</h2>';
            echo '<div class="step">';
            try {
                DB::connection()->getPdo();
                echo '<span class="success">✓ Database connection successful</span>';
                echo '<p>Database: ' . config('database.connections.mysql.database') . '</p>';
            } catch (\Exception $e) {
                echo '<span class="error">✗ Database connection failed</span>';
                echo '<pre>' . $e->getMessage() . '</pre>';
                throw $e;
            }
            echo '</div>';

            // 3. Run Migrations
            echo '<h2>📊 3. Run Migrations</h2>';
            echo '<div class="step">';
            Artisan::call('migrate', ['--force' => true]);
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ Migrations completed</span>';
            echo '</div>';

            // 4. Seed Permissions
            echo '<h2>👥 4. Seed Permissions</h2>';
            echo '<div class="step">';
            try {
                Artisan::call('db:seed', [
                    '--class' => 'TravelPermissionSeeder',
                    '--force' => true
                ]);
                echo '<pre>' . Artisan::output() . '</pre>';
                echo '<span class="success">✓ Permissions seeded</span>';
            } catch (\Exception $e) {
                echo '<span class="warning">⚠ Seeder warning (might be OK if already seeded)</span>';
                echo '<pre>' . $e->getMessage() . '</pre>';
            }
            echo '</div>';

            // 5. Storage Link
            echo '<h2>🔗 5. Create Storage Link</h2>';
            echo '<div class="step">';
            try {
                Artisan::call('storage:link');
                echo '<pre>' . Artisan::output() . '</pre>';
                echo '<span class="success">✓ Storage link created</span>';
            } catch (\Exception $e) {
                echo '<span class="warning">⚠ Storage link warning (might already exist)</span>';
                echo '<pre>' . $e->getMessage() . '</pre>';
            }
            echo '</div>';

            // 6. Cache Configuration
            echo '<h2>⚡ 6. Cache Configuration</h2>';
            echo '<div class="step">';
            Artisan::call('config:cache');
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ Config cached</span>';
            echo '</div>';

            // 7. Cache Routes
            echo '<h2>🛣️ 7. Cache Routes</h2>';
            echo '<div class="step">';
            Artisan::call('route:cache');
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ Routes cached</span>';
            echo '</div>';

            // 8. Cache Views
            echo '<h2>👁️ 8. Cache Views</h2>';
            echo '<div class="step">';
            Artisan::call('view:cache');
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ Views cached</span>';
            echo '</div>';

            // 9. Optimize
            echo '<h2>🚀 9. Optimize Application</h2>';
            echo '<div class="step">';
            Artisan::call('optimize');
            echo '<pre>' . Artisan::output() . '</pre>';
            echo '<span class="success">✓ Application optimized</span>';
            echo '</div>';

            // Success message
            echo '<div class="info">';
            echo '<h2 class="success">✅ Setup Complete!</h2>';
            echo '<p>Your Laravel application is now ready to use.</p>';
            echo '</div>';

            // Next steps
            echo '<h2>📝 Next Steps</h2>';
            echo '<div class="step">';
            echo '<ol>';
            echo '<li><strong>DELETE THIS FILE:</strong> setup-hostinger.php</li>';
            echo '<li>Set <code>APP_DEBUG=false</code> in .env file</li>';
            echo '<li>Test your application</li>';
            echo '<li>Create admin user if needed</li>';
            echo '<li>Setup backup schedule</li>';
            echo '</ol>';
            echo '</div>';

            // Links
            echo '<div style="margin-top: 30px;">';
            echo '<a href="/" class="btn">Go to Homepage</a>';
            echo '<a href="javascript:alert(\'Please delete this file manually via File Manager or FTP\')" class="btn btn-danger">Delete This File</a>';
            echo '</div>';

        } catch (\Exception $e) {
            echo '<div class="warning">';
            echo '<h2 class="error">❌ Setup Failed</h2>';
            echo '<p><strong>Error:</strong></p>';
            echo '<pre>' . $e->getMessage() . '</pre>';
            echo '<p><strong>Stack Trace:</strong></p>';
            echo '<pre>' . $e->getTraceAsString() . '</pre>';
            echo '</div>';
        }
        ?>

        <div class="warning" style="margin-top: 30px;">
            <strong>🔒 Security Reminder:</strong>
            <ul>
                <li>Delete this setup file immediately</li>
                <li>Set APP_DEBUG=false in production</li>
                <li>Never commit .env file to git</li>
                <li>Use strong database passwords</li>
                <li>Enable HTTPS</li>
            </ul>
        </div>
    </div>
</body>
</html>
