<?php
/**
 * Transactional Email Service using Resend HTTP API
 */

class EmailService {

    /**
     * Send email using Resend HTTP API or simulate locally
     * 
     * @param string $toEmail
     * @param string $subject
     * @param string $htmlContent
     * @param string $altText
     * @param string $userName
     * @return bool
     */
    private static function sendEmail(string $toEmail, string $subject, string $htmlContent, string $altText, string $userName): bool {
        $apiKey = env('EMAIL_API_KEY');
        
        // Developer Simulation Mode
        if ($apiKey === 'simulate') {
            $logDir = ROOT_PATH . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            // Extract OTP from HTML if possible for cleaner simulation printing
            preg_match('/[0-9]{6}/', $altText, $matches);
            $otp = $matches[0] ?? 'N/A';
            
            $logContent = "[" . date('Y-m-d H:i:s') . "] SIMULATION: Email sent to {$userName} <{$toEmail}> | Subject: {$subject} | OTP Code: {$otp}\n";
            file_put_contents($logDir . '/otp.log', $logContent, FILE_APPEND);
            
            // Also log full payload for debugging
            file_put_contents($logDir . '/email_simulation.log', $logContent . "HTML:\n{$htmlContent}\n\n", FILE_APPEND);
            
            return true;
        }

        $smtpUser = env('SMTP_USER');
        $smtpPass = env('SMTP_PASS');

        // If SMTP credentials exist, prioritize PHPMailer SMTP
        if (!empty($smtpUser) && !empty($smtpPass)) {
            require_once ROOT_PATH . '/vendor/autoload.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = env('SMTP_HOST', 'smtp.gmail.com');
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUser;
                $mail->Password   = $smtpPass;
                $mail->SMTPSecure = env('SMTP_SECURE', 'tls') === 'ssl' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)env('SMTP_PORT', 587);

                // Recipients
                $fromName = env('SMTP_FROM_NAME', 'Secure Auth System');
                $mail->setFrom($smtpUser, $fromName);
                $mail->addAddress($toEmail, $userName);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlContent;
                $mail->AltBody = $altText;

                $mail->send();
                return true;
            } catch (\Exception $e) {
                error_log("PHPMailer SMTP Error: " . $mail->ErrorInfo);
                return false;
            }
        }

        // Fallback to Resend API
        if (!empty($apiKey)) {
            $fromEmail = env('EMAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $fromName = env('SMTP_FROM_NAME', 'Secure Auth System');

            $url = 'https://api.resend.com/emails';
            
            $payload = [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => [$toEmail],
                'subject' => $subject,
                'html' => $htmlContent,
                'text' => $altText
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$apiKey}",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, env('APP_ENV', 'development') !== 'development');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            if ($error) {
                error_log("Email API Curl error: " . $error);
                return false;
            }

            if ($httpCode !== 200 && $httpCode !== 201) {
                error_log("Email API request failed with HTTP code {$httpCode}. Response: " . $response);
                return false;
            }

            return true;
        }

        error_log("No email configuration found in .env (neither SMTP nor Resend API Key).");
        return false;
    }

    /**
     * Send Email OTP Verification Code
     */
    public static function sendOTP(string $toEmail, string $otp, string $userName = 'User'): bool {
        $subject = 'Verify Your Email Address - OTP Verification';
        
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #ffffff; color: #333333;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #4f46e5; margin: 0;'>Secure Auth System</h2>
            </div>
            <hr style='border: 0; border-top: 1px solid #e0e0e0; margin-bottom: 20px;' />
            <p>Hello <strong>" . htmlspecialchars($userName) . "</strong>,</p>
            <p>Thank you for registering. Please verify your email address using the secure 6-digit One-Time Password (OTP) below:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <span style='font-family: monospace; font-size: 32px; font-weight: bold; letter-spacing: 6px; padding: 12px 24px; background-color: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; color: #4f46e5; display: inline-block;'>" . htmlspecialchars($otp) . "</span>
            </div>
            <p style='color: #6b7280; font-size: 14px;'>This OTP code is valid for <strong>10 minutes</strong>. If you did not request this, you can ignore this message.</p>
            <hr style='border: 0; border-top: 1px solid #e0e0e0; margin-top: 30px; margin-bottom: 20px;' />
            <p style='font-size: 12px; color: #9ca3af; text-align: center;'>This is an automated security transmission. Please do not reply.</p>
        </div>
        ";
        
        $altText = "Hello {$userName},\n\nYour 6-digit email verification OTP is: {$otp}\n\nThis OTP is valid for 10 minutes.";
        
        return self::sendEmail($toEmail, $subject, $htmlContent, $altText, $userName);
    }

    /**
     * Send Password Reset OTP
     */
    public static function sendPasswordResetOTP(string $toEmail, string $otp, string $userName = 'User'): bool {
        $subject = 'Reset Your Password - OTP Security';
        
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #ffffff; color: #333333;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #b91c1c; margin: 0;'>Secure Auth System</h2>
            </div>
            <hr style='border: 0; border-top: 1px solid #e0e0e0; margin-bottom: 20px;' />
            <p>Hello <strong>" . htmlspecialchars($userName) . "</strong>,</p>
            <p>We received a request to reset your password. Please use the secure 6-digit password reset code below to authorize this request:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <span style='font-family: monospace; font-size: 32px; font-weight: bold; letter-spacing: 6px; padding: 12px 24px; background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: 6px; color: #b91c1c; display: inline-block;'>" . htmlspecialchars($otp) . "</span>
            </div>
            <p style='color: #6b7280; font-size: 14px;'>This OTP code is valid for <strong>10 minutes</strong>. If you did not initiate this, please secure your credentials immediately.</p>
            <hr style='border: 0; border-top: 1px solid #e0e0e0; margin-top: 30px; margin-bottom: 20px;' />
            <p style='font-size: 12px; color: #9ca3af; text-align: center;'>This is an automated security transmission. Please do not reply.</p>
        </div>
        ";
        
        $altText = "Hello {$userName},\n\nYour 6-digit password reset OTP is: {$otp}\n\nThis OTP is valid for 10 minutes.";
        
        return self::sendEmail($toEmail, $subject, $htmlContent, $altText, $userName);
    }
}
