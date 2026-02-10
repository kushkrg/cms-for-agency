<?php
/**
 * Evolvcode CMS - Mailer Class
 * 
 * Simple SMTP mailer using settings from database.
 * Falls back to PHP mail() if SMTP is not configured.
 */

class Mailer
{
    private static ?Mailer $instance = null;
    
    private string $smtpHost = '';
    private int $smtpPort = 587;
    private string $smtpUser = '';
    private string $smtpPass = '';
    private string $smtpEncryption = 'tls';
    private string $fromEmail = '';
    private string $fromName = '';
    private bool $smtpEnabled = false;
    
    private array $errors = [];
    
    private function __construct()
    {
        $this->loadSettings();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): Mailer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load SMTP settings from database
     */
    private function loadSettings(): void
    {
        $this->smtpHost = getSetting('smtp_host', '');
        $this->smtpPort = (int) getSetting('smtp_port', 587);
        $this->smtpUser = getSetting('smtp_user', '');
        $this->smtpPass = getSetting('smtp_pass', '');
        $this->smtpEncryption = getSetting('smtp_encryption', 'tls');
        $this->fromEmail = getSetting('smtp_from_email', getSetting('contact_email', ''));
        $this->fromName = getSetting('smtp_from_name', getSetting('site_name', 'Evolvcode'));
        
        $this->smtpEnabled = !empty($this->smtpHost) && !empty($this->smtpUser);
    }
    
    /**
     * Send email
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $this->errors = [];
        
        $replyTo = $options['reply_to'] ?? null;
        $isHtml = $options['html'] ?? false;
        $cc = $options['cc'] ?? null;
        $bcc = $options['bcc'] ?? null;
        
        // Use SMTP if configured, otherwise fall back to mail()
        if ($this->smtpEnabled) {
            return $this->sendViaSMTP($to, $subject, $body, $replyTo, $isHtml, $cc, $bcc);
        } else {
            return $this->sendViaMail($to, $subject, $body, $replyTo, $isHtml);
        }
    }
    
    /**
     * Send via PHP mail() function
     */
    private function sendViaMail(string $to, string $subject, string $body, ?string $replyTo, bool $isHtml): bool
    {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        
        if ($isHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }
        
        if ($this->fromEmail) {
            $headers[] = "From: {$this->fromName} <{$this->fromEmail}>";
        }
        
        if ($replyTo) {
            $headers[] = "Reply-To: {$replyTo}";
        }
        
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if (!$result) {
            $this->errors[] = 'Failed to send email via mail()';
        }
        
        return $result;
    }
    
    /**
     * Send via SMTP using fsockopen
     * This is a basic SMTP implementation without external dependencies
     */
    private function sendViaSMTP(string $to, string $subject, string $body, ?string $replyTo, bool $isHtml, ?string $cc, ?string $bcc): bool
    {
        try {
            // Determine connection type
            $prefix = '';
            if ($this->smtpEncryption === 'ssl') {
                $prefix = 'ssl://';
            }
            
            // Connect to SMTP server
            $socket = @fsockopen(
                $prefix . $this->smtpHost,
                $this->smtpPort,
                $errno,
                $errstr,
                30
            );
            
            if (!$socket) {
                $this->errors[] = "Could not connect to SMTP server: $errstr ($errno)";
                return $this->sendViaMail($to, $subject, $body, $replyTo, $isHtml);
            }
            
            // Read server greeting
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '220') {
                $this->errors[] = "SMTP greeting failed: $response";
                fclose($socket);
                return false;
            }
            
            // EHLO
            $this->sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
            $response = $this->getResponse($socket);
            
            // STARTTLS if needed
            if ($this->smtpEncryption === 'tls' && strpos($response, 'STARTTLS') !== false) {
                $this->sendCommand($socket, "STARTTLS");
                $response = $this->getResponse($socket);
                if (substr($response, 0, 3) != '220') {
                    $this->errors[] = "STARTTLS failed: $response";
                    fclose($socket);
                    return false;
                }
                
                // Enable TLS encryption
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                
                // Send EHLO again after STARTTLS
                $this->sendCommand($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
                $this->getResponse($socket);
            }
            
            // AUTH LOGIN
            $this->sendCommand($socket, "AUTH LOGIN");
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '334') {
                $this->errors[] = "AUTH LOGIN failed: $response";
                fclose($socket);
                return false;
            }
            
            // Username
            $this->sendCommand($socket, base64_encode($this->smtpUser));
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '334') {
                $this->errors[] = "Username rejected: $response";
                fclose($socket);
                return false;
            }
            
            // Password
            $this->sendCommand($socket, base64_encode($this->smtpPass));
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '235') {
                $this->errors[] = "Password rejected: $response";
                fclose($socket);
                return false;
            }
            
            // MAIL FROM
            $from = $this->fromEmail ?: $this->smtpUser;
            $this->sendCommand($socket, "MAIL FROM:<$from>");
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '250') {
                $this->errors[] = "MAIL FROM failed: $response";
                fclose($socket);
                return false;
            }
            
            // RCPT TO
            $this->sendCommand($socket, "RCPT TO:<$to>");
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '250' && substr($response, 0, 3) != '251') {
                $this->errors[] = "RCPT TO failed: $response";
                fclose($socket);
                return false;
            }
            
            // DATA
            $this->sendCommand($socket, "DATA");
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '354') {
                $this->errors[] = "DATA failed: $response";
                fclose($socket);
                return false;
            }
            
            // Build email headers and body
            $message = "Subject: $subject\r\n";
            $message .= "To: $to\r\n";
            $message .= "From: {$this->fromName} <$from>\r\n";
            
            if ($replyTo) {
                $message .= "Reply-To: $replyTo\r\n";
            }
            if ($cc) {
                $message .= "Cc: $cc\r\n";
            }
            
            $message .= "MIME-Version: 1.0\r\n";
            if ($isHtml) {
                $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $message .= "Date: " . date('r') . "\r\n";
            $message .= "\r\n";
            $message .= $body;
            $message .= "\r\n.";
            
            $this->sendCommand($socket, $message);
            $response = $this->getResponse($socket);
            if (substr($response, 0, 3) != '250') {
                $this->errors[] = "Message send failed: $response";
                fclose($socket);
                return false;
            }
            
            // QUIT
            $this->sendCommand($socket, "QUIT");
            fclose($socket);
            
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = 'SMTP Exception: ' . $e->getMessage();
            // Fall back to mail()
            return $this->sendViaMail($to, $subject, $body, $replyTo, $isHtml);
        }
    }
    
    /**
     * Send command to SMTP server
     */
    private function sendCommand($socket, string $command): void
    {
        fwrite($socket, $command . "\r\n");
    }
    
    /**
     * Get response from SMTP server
     */
    private function getResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            // Check if this is the last line (space after status code)
            if (substr($line, 3, 1) == ' ') {
                break;
            }
        }
        return trim($response);
    }
    
    /**
     * Test SMTP connection
     */
    public function testConnection(): array
    {
        if (!$this->smtpEnabled) {
            return [
                'success' => false,
                'message' => 'SMTP is not configured. Please set SMTP host, username, and password.'
            ];
        }
        
        try {
            $prefix = $this->smtpEncryption === 'ssl' ? 'ssl://' : '';
            
            $socket = @fsockopen(
                $prefix . $this->smtpHost,
                $this->smtpPort,
                $errno,
                $errstr,
                10
            );
            
            if (!$socket) {
                return [
                    'success' => false,
                    'message' => "Connection failed: $errstr ($errno)"
                ];
            }
            
            $response = $this->getResponse($socket);
            fclose($socket);
            
            if (substr($response, 0, 3) == '220') {
                return [
                    'success' => true,
                    'message' => 'SMTP connection successful!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Unexpected response: $response"
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get last errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Check if SMTP is enabled
     */
    public function isSmtpEnabled(): bool
    {
        return $this->smtpEnabled;
    }
    
    /**
     * Static helper to send email
     */
    public static function sendMail(string $to, string $subject, string $body, array $options = []): bool
    {
        return self::getInstance()->send($to, $subject, $body, $options);
    }
}
