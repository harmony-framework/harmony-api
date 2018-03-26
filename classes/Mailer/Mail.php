<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private $settings;
    private $mailer;
    // Redefine the exception so message isn't optional
    public function __construct($settings) {
        $this->settings = $settings;
        $this->mailer = new PHPMailer;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Host = $settings['host'];          // your email host, to test I use localhost and check emails using test mail server application (catches all  sent mails)
	    $this->mailer->SMTPSecure = $settings['secureType'];                // set blank for localhost
	    $this->mailer->Port = $settings['port'];          // 25 for local host
	    $this->mailer->Username = $settings['username'];  // I set sender email in my mailer call
	    $this->mailer->Password = $settings['password'];
        $this->mailer->isSMTP();
        // $this->mailer->SMTPDebug = 3;
        $this->mailer->isHTML(true);
    }

    // custom string representation of object
    public function send($message, $subject,  $to) {
        $settings = $this->settings;
        $this->mailer->setFrom($settings['sendermail'], $settings['sendername']);
        $this->mailer->addAddress($to);
        $this->mailer->Subject  = $subject;
        $this->mailer->msgHTML("<html>".$message."</html>");
        // $this->mailer->AltBody = 'This is a plain-text message body';
        if(!$this->mailer->send()) {
            return false;
        } else {
            return true;
        }
    }

    public function sendTemplate($template, $message, $subject,  $to) {
        $settings = $this->settings;
        $template = file_get_contents(__DIR__ . '/../../templates/mail-templates/'.$template);
        $template = str_replace("%BODY%",$message,$template);
        $this->mailer->setFrom($settings['sendermail'], $settings['sendername']);
        $this->mailer->addAddress($to);
        $this->mailer->Subject  = $subject;
        $this->mailer->msgHTML($template);
        // $this->mailer->AltBody = 'This is a plain-text message body';
        if(!$this->mailer->send()) {
            return false;
        } else {
            return true;
        }
    }

}