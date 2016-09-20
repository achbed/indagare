<?php namespace indagare\util;

//include_once('Mail.php');
//include_once('Mail/mime.php');
//include_once('Mail/mime.php');

class IndagareMailer { 

    private $headers = array();
    private $params = array();
    private $body;
    private $recipients;
    
    public function __construct() {
        $this->params["host"] = "smtp.sendgrid.net"; 
        $this->params["port"] = "25"; 
        $this->params["auth"] = true; 
        $this->params["username"] = "indagare@clinic-it.com"; 
        $this->params["password"] = '!nd@g@r3m3l!$$@$g'; 
        $this->headers["from"] = "info@indagare.com";
    }
    
    public function setFrom($from) {
        $this->headers["From"] = $from;
    }
    
    public function setTo($to) {
        $this->headers["To"] = $to;
    }
    
    public function setSubject($subject) {
        $this->headers["Subject"] = $subject;
    }
    
    public function setBody ($body) {
        $this->body = $body;
    }
    
    public function setRecipients ($recipients) {
        $this->recipients = recipients;
    }
    
    public function send($subject, $body, $recipients) {
        //print $body;
        //print("<br>");
        
        $this->setSubject($subject);
        $this->setTo($recipients);
        
        //print_r($this->headers);
        //print("<br>");
        //print_r($this->params);
        //print("<br>");
        
        $mail_object = \Mail::factory("smtp", $this->params); 
        $mail_object->send($recipients, $this->headers, $body);
    }
    
    public function sendHtml($subject, $body, $recipients) {
        $this->setSubject($subject);
        $this->setTo($recipients);
        
        $crlf = "\n";
        
        // Creating the Mime message
        $mime = new \Mail_mime($crlf);

        // Setting the body of the email
        $mime->setTXTBody($body);
        $mime->setHTMLBody($body);

        $body = $mime->get();
        
        $this->headers = $mime->headers($this->headers);
        
        $mail_object = \Mail::factory("smtp", $this->params); 
        $mail_object->send($recipients, $this->headers, $body);
        
    } 
}
