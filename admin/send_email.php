<?php
/**
 * Email Sending Module
 * Currently uses PHP's native mail() function
 * For production, consider using PHPMailer or SwiftMailer
 */

require_once 'config.php';

class EmailSender {
    
    /**
     * Send email response to contact
     */
    public static function sendResponseEmail($email, $name, $subject, $message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $_SERVER['HTTP_HOST'] . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        $headers .= "Reply-To: " . $_SERVER['HTTP_HOST'] . "\r\n";
        
        $to = $email;
        $subject_line = "Re: " . $subject . " - Home of Hope";
        
        $emailBody = self::getEmailTemplate($name, $message);
        
        if (mail($to, $subject_line, $emailBody, $headers)) {
            return true;
        }
        return false;
    }
    
    /**
     * Send email to donor
     */
    public static function sendDonorThankYou($email, $name, $amount) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $_SERVER['HTTP_HOST'] . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        
        $to = $email;
        $subject = "Thank You for Your Donation - Home of Hope";
        
        $message = "
            <html>
                <body style='font-family: Arial, sans-serif;'>
                    <h2>Thank You, " . htmlspecialchars($name) . "!</h2>
                    <p>We truly appreciate your generous donation of <strong>" . formatCurrency($amount) . "</strong> to Home of Hope.</p>
                    <p>Your contribution makes a real difference in our community. We are committed to using your donation to help those in need.</p>
                    <p>Warm regards,<br>The Home of Hope Team</p>
                </body>
            </html>
        ";
        
        if (mail($to, $subject, $message, $headers)) {
            return true;
        }
        return false;
    }
    
    /**
     * Send email to volunteer
     */
    public static function sendVolunteerConfirmation($email, $name) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $_SERVER['HTTP_HOST'] . " <noreply@" . $_SERVER['HTTP_HOST'] . ">\r\n";
        
        $to = $email;
        $subject = "Volunteer Application Received - Home of Hope";
        
        $message = "
            <html>
                <body style='font-family: Arial, sans-serif;'>
                    <h2>Thank You for Volunteering, " . htmlspecialchars($name) . "!</h2>
                    <p>We have received your volunteer application and truly appreciate your interest in helping our community.</p>
                    <p>Our team will review your application and will be in touch with you shortly regarding available opportunities.</p>
                    <p>In the meantime, if you have any questions, please don't hesitate to contact us.</p>
                    <p>Warm regards,<br>The Home of Hope Team</p>
                </body>
            </html>
        ";
        
        if (mail($to, $subject, $message, $headers)) {
            return true;
        }
        return false;
    }
    
    /**
     * Get email template
     */
    private static function getEmailTemplate($name, $message) {
        return "
            <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2>Hello " . htmlspecialchars($name) . ",</h2>
                        <p>" . nl2br(htmlspecialchars($message)) . "</p>
                        <hr style='border: 1px solid #ddd; margin: 20px 0;'>
                        <p style='color: #999; font-size: 12px;'>
                            This is an automated response. Please do not reply to this email.
                        </p>
                    </div>
                </body>
            </html>
        ";
    }
}

/**
 * Quick email function
 */
function sendEmail($to, $subject, $message, $headers = '') {
    if (empty($headers)) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $message, $headers);
}
?>
