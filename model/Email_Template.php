<?php

class  Email_Template {
 
public function getWelcomeEmailTemplate($userName, $dashboardLink, $profileLink) {
    return '<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <body style="margin: 0; padding: 20px; font-family: \'Segoe UI\', system-ui, -apple-system, sans-serif; background-color: #f8f9ff;">
            
        <table style="max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); border: 1px solid #e8e9ff;">
        <!-- Header -->
        <tr>
            <td style="padding: 40px 30px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px 16px 0 0; text-align: center;">
                <img src="https://comeandsee.com.ng/kreativerock/assets/images/logo.svg" alt="KreativeRock Logo" style="height: 48px; margin-bottom: 25px;">
                <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                    Welcome to KreativeRock! 
                </h1>
                <p style="color: #d1fae5; margin: 15px 0 0; font-size: 18px; font-weight: 500;">Your account is now active</p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <div style="border-left: 4px solid #4f46e5; padding-left: 20px; margin-bottom: 35px;">
                    <p style="margin: 0; color: #64748b; font-size: 18px; line-height: 1.6;">
                        Hi <strong style="color: #10b981;">' . htmlspecialchars($userName) . '</strong>, welcome to our community!
                    </p>
                </div>

                <p style="color: #374151; font-size: 16px; line-height: 1.7; margin-bottom: 30px;">
                    Congratulations! Your email has been successfully verified and your account is now active. You\'re all set to explore everything KreativeRock has to offer.
                </p>

                <!-- Quick Start Guide -->
                <div style="background: #f8f9ff; padding: 25px; border-radius: 12px; margin: 30px 0;">
                    <h3 style="margin: 0 0 20px; color: #4f46e5; font-size: 20px; font-weight: 700;">Quick Start Guide</h3>
                    <div style="margin-bottom: 15px;">
                        <div style="display: inline-block; width: 24px; height: 24px; background: #10b981; border-radius: 50%; text-align: center; margin-right: 12px; vertical-align: middle;">
                            <span style="color: white; font-size: 14px; font-weight: bold; line-height: 24px;">1</span>
                        </div>
                        <span style="color: #374151; font-size: 16px; vertical-align: middle;">Complete your profile setup</span>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <div style="display: inline-block; width: 24px; height: 24px; background: #10b981; border-radius: 50%; text-align: center; margin-right: 12px; vertical-align: middle;">
                            <span style="color: white; font-size: 14px; font-weight: bold; line-height: 24px;">2</span>
                        </div>
                        <span style="color: #374151; font-size: 16px; vertical-align: middle;">Explore our dashboard features</span>
                    </div>
                    <div>
                        <div style="display: inline-block; width: 24px; height: 24px; background: #10b981; border-radius: 50%; text-align: center; margin-right: 12px; vertical-align: middle;">
                            <span style="color: white; font-size: 14px; font-weight: bold; line-height: 24px;">3</span>
                        </div>
                        <span style="color: #374151; font-size: 16px; vertical-align: middle;">Join our community discussions</span>
                    </div>
                </div>

                <!-- CTA Buttons -->

                <!-- Support Info -->
                <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-top: 30px;">
                    <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 1.6;">
                        <strong>Need Help?</strong> Our support team is here to help you get started. Don\'t hesitate to reach out if you have any questions!
                    </p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 30px; background-color: #f8f9ff; border-radius: 0 0 16px 16px; border-top: 2px solid #e8e9ff;">
                <div style="text-align: center;">
                    <p style="margin: 0 0 15px; color: #64748b; font-size: 14px;">
                        Questions? We\'re here to help!<br>
                        <a href="mailto:support@kreativerock.com" style="color: #4f46e5; text-decoration: none; font-weight: 600;">support@kreativerock.com</a> | 
                        <a href="tel:+1234567890" style="color: #4f46e5; text-decoration: none; font-weight: 600;">+123 456 7890</a>
                    </p>
                    <div style="margin: 20px 0;">
                        <a href="#" style="display: inline-block; margin: 0 10px; color: #64748b; text-decoration: none;"> Facebook</a>
                        <a href="#" style="display: inline-block; margin: 0 10px; color: #64748b; text-decoration: none;"> Twitter</a>
                        <a href="#" style="display: inline-block; margin: 0 10px; color: #64748b; text-decoration: none;"> LinkedIn</a>
                    </div>
                    <div style="border-top: 1px solid #e8e9ff; padding-top: 20px; margin-top: 20px;">
                        <p style="margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            Thank you for choosing KreativeRock!<br>
                            © 2024 KreativeRock. All rights reserved.
                        </p>
                    </div>
                </div>
            </td>
        </tr>
        </table>

        </body>
    </html>';
}

public  function getVerificationEmailTemplate($userName, $verificationCode, $verificationLink) {
    return '<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <body style="margin: 0; padding: 20px; font-family: \'Segoe UI\', system-ui, -apple-system, sans-serif; background-color: #f8f9ff;">
            
            <table style="max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);    border: 1px solid #e8e9ff;">
            <!-- Header -->
        <tr>
            <td style="padding: 40px 30px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border-radius: 16px 16px 0 0; text-align: center;">
                <img src="https://comeandsee.com.ng/kreativerock/assets/images/logo.svg" alt="KreativeRock Logo" style="height: 48px; margin-bottom: 25px;">
                <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                    Verify Your Email
                </h1>
                <p style="color: #e0e7ff; margin: 15px 0 0; font-size: 18px; font-weight: 500;">Almost there! Just one more step</p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px 30px;">
                <div style="border-left: 4px solid #10b981; padding-left: 20px; margin-bottom: 35px;">
                    <p style="margin: 0; color: #64748b; font-size: 18px; line-height: 1.6;">
                        Welcome to <strong style="color: #4f46e5;">KreativeRock</strong>! We\'re excited to have you on board.
                    </p>
                </div>

                <p style="color: #374151; font-size: 16px; line-height: 1.7; margin-bottom: 30px;">
                    Hi <strong>' . htmlspecialchars($userName) . '</strong>, to complete your registration and activate your account, please verify your email address by clicking the button below:
                </p>

                <!-- Verification Code Display -->
                <div style="background: #f0f9ff; padding: 25px; border-radius: 12px; margin: 30px 0; text-align: center; border: 2px solid #0ea5e9;">
                    <p style="margin: 0 0 10px; color: #0369a1; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Your Verification Code</p>
                    <p style="margin: 0; color: #0c4a6e; font-size: 32px; font-weight: 800; font-family: \'Courier New\', monospace; letter-spacing: 3px;">
                        ' . $verificationCode . '
                    </p>
                </div>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 35px 0;">
                    <a href="' . $verificationLink . '" style="display: inline-block; background: #10b981; color: #ffffff; padding: 16px 40px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 16px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        ✓ Verify Email Address
                    </a>
                </div>

                <div style="background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-top: 30px;">
                    <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                        <strong> Important:</strong> This verification link will expire in 24 hours. If you didn\'t create an account with us, please ignore this email.
                    </p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 30px; background-color: #f8f9ff; border-radius: 0 0 16px 16px; border-top: 2px solid #e8e9ff;">
                <div style="text-align: center;">
                    <p style="margin: 0 0 15px; color: #64748b; font-size: 14px;">
                        Need help? Contact our support team<br>
                        <a href="mailto:support@kreativerock.com" style="color: #4f46e5; text-decoration: none; font-weight: 600;">support@kreativerock.com</a>
                    </p>
                    <div style="border-top: 1px solid #e8e9ff; padding-top: 20px; margin-top: 20px;">
                        <p style="margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.6;">
                            This is an automated message - please do not reply directly to this email.<br>
                            © 2024 KreativeRock. All rights reserved.
                        </p>
                    </div>
                </div>
            </td>
        </tr>
        </table>
        </body>
    </html>';
}

}