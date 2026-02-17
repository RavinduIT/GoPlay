<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service
 *
 * Handles all email functionality for the GoPlay platform
 * Uses PHPMailer for sending emails via SMTP
 */
class EmailService
{
    private $mailer;
    private $fromAddress;
    private $fromName;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@goplay.lk';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'GoPlay Sports Platform';

        $this->configureSMTP();
    }

    /**
     * Configure SMTP settings from environment variables
     */
    private function configureSMTP(): void
    {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = (int)($_ENV['MAIL_PORT'] ?? 587);

            // Sender info
            $this->mailer->setFrom($this->fromAddress, $this->fromName);

            // Character set
            $this->mailer->CharSet = 'UTF-8';

            // HTML format
            $this->mailer->isHTML(true);

        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
            throw new \Exception("Failed to configure email service");
        }
    }

    /**
     * Send registration confirmation email
     *
     * @param string $recipientEmail User's email address
     * @param string $recipientName User's full name
     * @param string $userType Type of user account
     * @return bool Success status
     */
    public function sendRegistrationConfirmation(string $recipientEmail, string $recipientName, string $userType): bool
    {
        try {
            // Reset recipients for reuse
            $this->mailer->clearAddresses();

            // Add recipient
            $this->mailer->addAddress($recipientEmail, $recipientName);

            // Email subject
            $this->mailer->Subject = 'Welcome to GoPlay Sports Platform!';

            // Email body
            $body = $this->getRegistrationTemplate($recipientName, $userType);
            $this->mailer->Body = $body;

            // Plain text alternative
            $this->mailer->AltBody = strip_tags($body);

            // Send email
            $result = $this->mailer->send();

            // Log success
            error_log("Registration email sent successfully to: {$recipientEmail}");

            return $result;

        } catch (Exception $e) {
            error_log("Failed to send registration email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Send service provider approval email
     *
     * @param string $recipientEmail User's email address
     * @param string $recipientName User's full name
     * @param string $providerType Type of service provider (ground_owner, coach, shop_owner)
     * @return bool Success status
     */
    public function sendProviderApprovalEmail(string $recipientEmail, string $recipientName, string $providerType): bool
    {
        try {
            // Reset recipients for reuse
            $this->mailer->clearAddresses();

            // Add recipient
            $this->mailer->addAddress($recipientEmail, $recipientName);

            // Email subject
            $providerTypeName = $this->getProviderTypeName($providerType);
            $this->mailer->Subject = "Congratulations! You're now a {$providerTypeName}";

            // Email body
            $body = $this->getProviderApprovalTemplate($recipientName, $providerType);
            $this->mailer->Body = $body;

            // Plain text alternative
            $this->mailer->AltBody = strip_tags($body);

            // Send email
            $result = $this->mailer->send();

            // Log success
            error_log("Provider approval email sent successfully to: {$recipientEmail}");

            return $result;

        } catch (Exception $e) {
            error_log("Failed to send provider approval email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Get human-readable provider type name
     *
     * @param string $providerType Provider type code
     * @return string Human-readable name
     */
    private function getProviderTypeName(string $providerType): string
    {
        $names = [
            'ground_owner' => 'Ground Owner',
            'coach' => 'Coach',
            'shop_owner' => 'Shop Owner'
        ];

        return $names[$providerType] ?? 'Service Provider';
    }

    /**
     * Get registration email template
     *
     * @param string $userName User's name
     * @param string $userType User type
     * @return string HTML email template
     */
    private function getRegistrationTemplate(string $userName, string $userType): string
    {
        $dashboardUrl = $this->getDashboardUrl($userType);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 18px;
            color: #2196F3;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .features {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .feature-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .feature-icon {
            color: #2196F3;
            margin-right: 10px;
            font-size: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to GoPlay!</h1>
        </div>
        <div class="content">
            <p class="welcome-text">Hello {$userName}!</p>
            <div class="message">
                <p>Thank you for registering with GoPlay Sports Platform! We're excited to have you join our community of sports enthusiasts.</p>
                <p>Your account has been successfully created and is now active. You can start exploring our platform right away!</p>
            </div>

            <div class="features">
                <h3 style="margin-top: 0; color: #2196F3;">What you can do on GoPlay:</h3>
                <div class="feature-item">
                    <span class="feature-icon">&bull;</span>
                    <span>Book sports facilities and grounds</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">&bull;</span>
                    <span>Find and hire professional coaches</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">&bull;</span>
                    <span>Shop for sports equipment and gear</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">&bull;</span>
                    <span>Read and write reviews</span>
                </div>
            </div>

            <center>
                <a href="{$dashboardUrl}" class="button">Get Started</a>
            </center>

            <div class="message" style="margin-top: 30px;">
                <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
                <p>Best regards,<br><strong>The GoPlay Team</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; 2024 GoPlay Sports Platform. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get provider approval email template
     *
     * @param string $userName User's name
     * @param string $providerType Provider type
     * @return string HTML email template
     */
    private function getProviderApprovalTemplate(string $userName, string $providerType): string
    {
        $providerTypeName = $this->getProviderTypeName($providerType);
        $dashboardUrl = $this->getDashboardUrl($providerType);
        $features = $this->getProviderFeatures($providerType);

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .congratulations {
            font-size: 22px;
            color: #2196F3;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        .message {
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin: 20px 0;
        }
        .features {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .feature-item {
            margin: 12px 0;
            display: flex;
            align-items: flex-start;
        }
        .feature-icon {
            color: #2196F3;
            margin-right: 10px;
            font-size: 20px;
            min-width: 25px;
        }
        .next-steps {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Congratulations!</h1>
            <div class="badge">{$providerTypeName} Account Approved</div>
        </div>
        <div class="content">
            <p class="congratulations">Welcome to the GoPlay Provider Community, {$userName}!</p>

            <div class="message">
                <p>Great news! Your account has been upgraded to <strong>{$providerTypeName}</strong> status by our admin team.</p>
                <p>You now have access to exclusive provider features and can start offering your services on the GoPlay platform.</p>
            </div>

            <div class="features">
                <h3 style="margin-top: 0; color: #2196F3;">Your New Provider Features:</h3>
                {$features}
            </div>

            <div class="next-steps">
                <strong>Next Steps:</strong>
                <ol style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Complete your provider profile</li>
                    <li>Set up your services and pricing</li>
                    <li>Start receiving bookings from customers</li>
                </ol>
            </div>

            <center>
                <a href="{$dashboardUrl}" class="button">Go to Your Dashboard</a>
            </center>

            <div class="message" style="margin-top: 30px;">
                <p>If you have any questions about your new provider account, our support team is here to help!</p>
                <p>Best of luck with your services,<br><strong>The GoPlay Team</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; 2024 GoPlay Sports Platform. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get provider-specific features list
     *
     * @param string $providerType Provider type
     * @return string HTML features list
     */
    private function getProviderFeatures(string $providerType): string
    {
        $features = [
            'ground_owner' => [
                'List and manage your sports facilities',
                'Set availability and pricing schedules',
                'Track bookings and earnings in real-time',
                'View detailed analytics and reports',
                'Manage facility amenities and features',
                'Upload photos of your grounds'
            ],
            'coach' => [
                'Create your professional coach profile',
                'Set your availability and hourly rates',
                'Manage individual and group sessions',
                'Track your coaching sessions and earnings',
                'Receive and respond to reviews',
                'Showcase your certifications and experience'
            ],
            'shop_owner' => [
                'List and sell sports equipment',
                'Manage your product inventory',
                'Process orders and track sales',
                'View sales analytics and reports',
                'Manage shipping and delivery',
                'Create promotions and discounts'
            ]
        ];

        $featureList = $features[$providerType] ?? $features['ground_owner'];
        $html = '';

        foreach ($featureList as $feature) {
            $html .= '<div class="feature-item">';
            $html .= '<span class="feature-icon">&bull;</span>';
            $html .= '<span>' . $feature . '</span>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get dashboard URL based on user type
     *
     * @param string $userType User type
     * @return string Dashboard URL
     */
    private function getDashboardUrl(string $userType): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:3000';

        $dashboardMap = [
            'admin' => '/admin/dashboard',
            'ground_owner' => '/ground-owner/dashboard',
            'coach' => '/coach/dashboard',
            'shop_owner' => '/shop-owner/dashboard',
            'user' => '/'
        ];

        $path = $dashboardMap[$userType] ?? '/';
        return $baseUrl . $path;
    }

    /**
     * Send a generic notification email
     *
     * @param string $recipientEmail Recipient's email
     * @param string $recipientName Recipient's name
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @return bool Success status
     */
    public function sendNotification(string $recipientEmail, string $recipientName, string $subject, string $message): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($recipientEmail, $recipientName);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;
            $this->mailer->AltBody = strip_tags($message);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send notification email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
