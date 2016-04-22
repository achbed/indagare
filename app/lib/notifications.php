<?php namespace indagare\notify;

    include_once 'mail.php';
    include_once 'Mail.php';
    include_once 'config.php';

class EmailNotification {
    public static function sendResetPWD($key, $email, $user) {
        
        $baseURL = \indagare\config\Config::$baseURL;
        
        $message = "$user,

Please follow the link below to reset your password for Indagare.com:

http://$baseURL/wp-content/themes/indagare/pwd_reset.php?key=$key

Thanks,
The Indagare Team
http://www.indagare.com";
        
        $m = new \indagare\util\IndagareMailer();
        $m->send('Your Indagare Account', $message, $email);
        
    }
    
    public static function sendNewsletterSignup($email) {
        
        $message = "<p>
Thank you for joining Indagare&rsquo;s mailing list! Keep an eye on your inbox for exciting travel news and updates from around the globe.
</p>

<p>
The Indagare team scouts destinations worldwide to ensure we have the best, most relevant travel advice to share with our community. Our website &mdash; <a href=\"http://www.indagare.com\">www.indagare.com</a> &mdash; features destination reports on locations both familiar and far-flung and reviews of the best hotels, shops and activities each area has to offer. We help our members discover authentic experiences and plan remarkable, memorable journeys.
</p>

<p>
If you would like to learn more about becoming a member, contact our membership department by emailing <a href=\"mailto:membership@indagare.com\">membership@indagare.com</a> or by calling 212-988-2611 or visit our <a href=\"http://www.indagare.com/join/\">Join</a> page to become a member now.
</p>

<p>
We hope you enjoy receiving our updates and that you consider becoming a part of the next generation of travel wisdom.
</p>

<p>
Thanks again for signing up! <br />
The Indagare Team
</p>

<p>
Want the latest news on our travels? Like us on <a href=\"https://www.facebook.com/indagaretravel/\">Facebook</a> and follow us on <a href=\"https://www.instagram.com/indagaretravel/?hl=en\">Instagram</a>.
</p>
";
        
        $m = new \indagare\util\IndagareMailer();
        $m->sendHtml('Your Indagare Newsletter Subscription', $message, $email);
        
    }
}

