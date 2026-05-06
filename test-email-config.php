<?php
/**
 * Script Test Email Configuration
 * 
 * Jalankan: php test-email-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "=== TEST EMAIL CONFIGURATION ===\n\n";

// 1. Check .env configuration
echo "1. Checking .env Configuration:\n";
$mailConfig = [
    'MAIL_MAILER' => env('MAIL_MAILER'),
    'MAIL_HOST' => env('MAIL_HOST'),
    'MAIL_PORT' => env('MAIL_PORT'),
    'MAIL_USERNAME' => env('MAIL_USERNAME'),
    'MAIL_PASSWORD' => env('MAIL_PASSWORD') ? '***' . substr(env('MAIL_PASSWORD'), -4) : 'NOT SET',
    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
];

foreach ($mailConfig as $key => $value) {
    $status = '❌';
    if ($key === 'MAIL_PASSWORD') {
        $status = ($value !== 'NOT SET' && $value !== '***word') ? '✅' : '❌';
    } elseif ($key === 'MAIL_USERNAME') {
        $status = ($value && $value !== 'your-email@gmail.com') ? '✅' : '❌';
    } else {
        $status = $value ? '✅' : '❌';
    }
    
    echo "   $status $key: $value\n";
}

// 2. Check if email is properly configured
echo "\n2. Validating Configuration:\n";
$mailConfigured = config('mail.mailers.smtp.username') && 
                 config('mail.mailers.smtp.username') !== 'your-email@gmail.com' &&
                 config('mail.mailers.smtp.password') && 
                 config('mail.mailers.smtp.password') !== 'your-app-password';

if ($mailConfigured) {
    echo "   ✅ Email configuration looks good\n";
} else {
    echo "   ❌ Email not configured properly\n";
    echo "   Please update .env file with valid email credentials\n";
    echo "\n";
    echo "   For Gmail:\n";
    echo "   1. Enable 2-Step Verification\n";
    echo "   2. Generate App Password at https://myaccount.google.com/apppasswords\n";
    echo "   3. Update .env:\n";
    echo "      MAIL_USERNAME=your-email@gmail.com\n";
    echo "      MAIL_PASSWORD=your-16-char-app-password\n";
    exit(1);
}

// 3. Test email sending
echo "\n3. Testing Email Sending:\n";
echo "   Enter test email address (or press Enter to skip): ";
$testEmail = trim(fgets(STDIN));

if (empty($testEmail)) {
    echo "   ⚠️  Skipped email test\n";
} else {
    echo "   Sending test email to: $testEmail\n";
    
    try {
        Mail::raw('This is a test email from HM Tour Laravel Application.', function($message) use ($testEmail) {
            $message->to($testEmail)
                    ->subject('Test Email - HM Tour');
        });
        
        echo "   ✅ Email sent successfully!\n";
        echo "   Please check inbox (and spam folder) at: $testEmail\n";
    } catch (\Exception $e) {
        echo "   ❌ Failed to send email\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "\n";
        echo "   Common issues:\n";
        echo "   - Wrong username/password\n";
        echo "   - Port 587 blocked by firewall\n";
        echo "   - Need to enable 'Less secure app access' (Gmail)\n";
        echo "   - Need to generate App Password (Gmail with 2FA)\n";
    }
}

// 4. Test forgot password email template
echo "\n4. Checking Email Template:\n";
$templatePath = resource_path('views/emails/reset-password.blade.php');
if (file_exists($templatePath)) {
    echo "   ✅ Email template exists: $templatePath\n";
} else {
    echo "   ❌ Email template not found: $templatePath\n";
    echo "   Creating template...\n";
    
    $templateContent = <<<'BLADE'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #16a34a;">Reset Password - HM Tour</h2>
        
        <p>Halo <strong>{{ $affiliator->full_name }}</strong>,</p>
        
        <p>Anda telah meminta reset password untuk akun mitra Anda.</p>
        
        <p>Klik tombol di bawah ini untuk reset password:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetLink }}" 
               style="display: inline-block; padding: 12px 30px; background-color: #16a34a; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Reset Password
            </a>
        </div>
        
        <p>Atau copy link berikut ke browser Anda:</p>
        <p style="word-break: break-all; background: #f3f4f6; padding: 10px; border-radius: 5px;">
            {{ $resetLink }}
        </p>
        
        <p><strong>Link ini berlaku selama 1 jam.</strong></p>
        
        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
        
        <p style="font-size: 12px; color: #6b7280;">
            Email ini dikirim otomatis oleh sistem HM Tour. Mohon tidak membalas email ini.
        </p>
    </div>
</body>
</html>
BLADE;
    
    if (!is_dir(dirname($templatePath))) {
        mkdir(dirname($templatePath), 0755, true);
    }
    file_put_contents($templatePath, $templateContent);
    echo "   ✅ Email template created\n";
}

echo "\n=== TEST COMPLETE ===\n";

if ($mailConfigured) {
    echo "\n✅ Email configuration is ready!\n";
    echo "\nNext steps:\n";
    echo "1. Test forgot password flow in browser\n";
    echo "2. Check storage/logs/laravel.log for any errors\n";
    echo "3. Deploy to production with proper email credentials\n";
} else {
    echo "\n❌ Email configuration needed!\n";
    echo "\nPlease update .env file and run this script again.\n";
}
