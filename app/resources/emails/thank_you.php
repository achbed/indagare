<?php
function createThankyouEmail($name, $street, $city, $state, $zip, $country, $email, $membership) {
    return "<!-- Inliner Build Version 4380b7741bb759d6cb997545f3add21ad48f010b -->
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional //EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
  <head>
    <title>Welcome to Indagare</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <meta name=\"viewport\" content=\"width=320, target-densitydpi=device-dpi\" />
<!--[if gte mso 9]>
<style _tmplitem=\"309\" >
.article-content ol, .article-content ul {
   margin: 0 0 0 24px;
   padding: 0;
   list-style-position: inside;
}
</style>
<![endif]-->
  </head>
  <body style=\"width: 100% !important; font-family: HelveticaNeue, sans-serif; background: #c7c7c7; margin: 0; padding: 0;\" bgcolor=\"#c7c7c7\">
    <style type=\"text/css\">
@media only screen and (max-width: 660px) {
  table[class=w0] {
    width: 0 !important;
  }
  td[class=w0] {
    width: 0 !important;
  }
  table[class=w10] {
    width: 10px !important;
  }
  td[class=w10] {
    width: 10px !important;
  }
  img[class=w10] {
    width: 10px !important;
  }
  table[class=w15] {
    width: 5px !important;
  }
  td[class=w15] {
    width: 5px !important;
  }
  img[class=w15] {
    width: 5px !important;
  }
  table[class=w30] {
    width: 10px !important;
  }
  td[class=w30] {
    width: 10px !important;
  }
  img[class=w30] {
    width: 10px !important;
  }
  table[class=w60] {
    width: 10px !important;
  }
  td[class=w60] {
    width: 10px !important;
  }
  img[class=w60] {
    width: 10px !important;
  }
  table[class=w125] {
    width: 80px !important;
  }
  td[class=w125] {
    width: 80px !important;
  }
  img[class=w125] {
    width: 80px !important;
  }
  table[class=w130] {
    width: 55px !important;
  }
  td[class=w130] {
    width: 55px !important;
  }
  img[class=w130] {
    width: 55px !important;
  }
  table[class=w140] {
    width: 90% !important;
  }
  td[class=w140] {
    width: 90% !important;
  }
  img[class=w140] {
    width: 90% !important;
  }
  table[class=w160] {
    width: 180px !important;
  }
  td[class=w160] {
    width: 180px !important;
  }
  img[class=w160] {
    width: 180px !important;
  }
  table[class=w170] {
    width: 100% !important;
  }
  td[class=w170] {
    width: 100% !important;
  }
  img[class=w170] {
    width: 100% !important;
  }
  table[class=w180] {
    width: 80px !important;
  }
  td[class=w180] {
    width: 80px !important;
  }
  img[class=w180] {
    width: 80px !important;
  }
  table[class=w195] {
    width: 80px !important;
  }
  td[class=w195] {
    width: 80px !important;
  }
  img[class=w195] {
    width: 80px !important;
  }
  table[class=w200] {
    width: 100% !important; float: none !important;
  }
  td[class=w200] {
    width: 100% !important; float: none !important;
  }
  img[class=w200] {
    width: 100% !important; float: none !important;
  }
  table[class=w220] {
    width: 80px !important;
  }
  td[class=w220] {
    width: 80px !important;
  }
  img[class=w220] {
    width: 80px !important;
  }
  table[class=w255] {
    width: 185px !important;
  }
  td[class=w255] {
    width: 185px !important;
  }
  img[class=w255] {
    width: 185px !important;
  }
  table[class=w260] {
    width: 100% !important;
  }
  td[class=w260] {
    width: 100% !important;
  }
  img[class=w260] {
    width: 100% !important;
  }
  table[class=w275] {
    width: 135px !important;
  }
  td[class=w275] {
    width: 135px !important;
  }
  img[class=w275] {
    width: 135px !important;
  }
  table[class=w280] {
    width: 135px !important;
  }
  td[class=w280] {
    width: 135px !important;
  }
  img[class=w280] {
    width: 135px !important;
  }
  table[class=w290] {
    width: 140px !important;
  }
  td[class=w290] {
    width: 140px !important;
  }
  img[class=w290] {
    width: 140px !important;
  }
  table[class=w300] {
    width: 140px !important;
  }
  td[class=w300] {
    width: 140px !important;
  }
  img[class=w300] {
    width: 140px !important;
  }
  table[class=w310] {
    width: 140px !important;
  }
  td[class=w310] {
    width: 140px !important;
  }
  img[class=w310] {
    width: 140px !important;
  }
  table[class=w325] {
    width: 95px !important;
  }
  td[class=w325] {
    width: 95px !important;
  }
  img[class=w325] {
    width: 95px !important;
  }
  table[class=w360] {
    width: 140px !important;
  }
  td[class=w360] {
    width: 140px !important;
  }
  img[class=w360] {
    width: 140px !important;
  }
  table[class=w410] {
    width: 100% !important; float: none !important;
  }
  td[class=w410] {
    width: 100% !important; float: none !important;
  }
  img[class=w410] {
    width: 100% !important; float: none !important;
  }
  table[class=w440] {
    width: 100% !important; float: none !important;
  }
  td[class=w440] {
    width: 100% !important; float: none !important;
  }
  img[class=w440] {
    width: 100% !important; float: none !important;
  }
  table[class=w470] {
    width: 200px !important;
  }
  td[class=w470] {
    width: 200px !important;
  }
  img[class=w470] {
    width: 200px !important;
  }
  table[class=w580] {
    width: 280px !important;
  }
  td[class=w580] {
    width: 280px !important;
  }
  img[class=w580] {
    width: 280px !important;
  }
  table[class=w640] {
    width: 300px !important;
  }
  td[class=w640] {
    width: 300px !important;
  }
  img[class=w640] {
    width: 300px !important;
  }
  table[class*=hide] {
    display: none !important;
  }
  td[class*=hide] {
    display: none !important;
  }
  img[class*=hide] {
    display: none !important;
  }
  p[class*=hide] {
    display: none !important;
  }
  span[class*=hide] {
    display: none !important;
  }
  table[class=h0] {
    height: 0 !important;
  }
  td[class=h0] {
    height: 0 !important;
  }
  #headline p {
    font-size: 30px !important;
  }
  .article-content {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  #left-sidebar {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  .header-content {
    -webkit-text-size-adjust: 80% !important; -ms-text-size-adjust: 80% !important;
  }
  img {
    height: auto; line-height: 100%;
  }
}
</style>
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"background-table\" style=\"background: #999;\" bgcolor=\"#999\">
      <tbody>
        <tr style=\"border-collapse: collapse;\"><td align=\"center\" bgcolor=\"#c7c7c7\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
        	<table class=\"w640\" style=\"margin: 0 10px;\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td id=\"header\" class=\"w640\" width=\"640\" align=\"center\" bgcolor=\"#000000\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
						<table class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td valign=\"middle\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w200\" width=\"200\" align=\"left\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											<td class=\"w140\" width=\"140\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><img class=\"w200\" alt=\"\" src=\"http://images.indagare.com/wp-content/uploads/indagare-logo-email.png\" style=\"outline: none; text-decoration: none; display: block;\" /></td>
											<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											</tr></tbody></table><table class=\"w440\" width=\"440\" align=\"right\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<td class=\"w290\" width=\"290\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\">Contact us at <strong><a href=\"mailto:bookings@indagare.com\" style=\"color: #ffffff; text-decoration: none;\">bookings@indagare.com</a></strong> or <br style=\"line-height: 100%;\" /><strong>+1 212 988 2611</strong> Monday through Friday, 9:30am-6:00pm EST</p></td>
												<td class=\"w10\" width=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<!--<td class=\"w130\" width=\"130\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\"></p></td>
												<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>-->
											</tr></tbody></table></td>
							</tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
                </tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"30\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr id=\"simple-content-row\" style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
					<table align=\"left\" class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
							<td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<p align=\"left\" class=\"article-title\" style=\"font-size: 18px; line-height: 24px; color: #000000; font-weight: bold; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">Welcome, $name</p>
													<div align=\"left\" class=\"article-content\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">
														<p style=\"margin-bottom: 15px;\">Thank you for your order at Indagare.com. We hope that you will enjoy being a part of our travel community. To get started, visit: <a href=\"http://www.indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">www.indagare.com</a></p>
                                                                                                                
<p style=\"margin-bottom: 15px;\"><strong>How to Use Indagare</strong><br>Planning an upcoming trip? Our staff scouts the world and helps our members plan 
everything from a long weekend in LA to a safari in Botswana or a multi-generational 
birthday in Marrakech. We have a curated network of expert guides and the ability 
to create special experiences across the globe.</p>

<p style=\"margin-bottom: 15px;\">All members have access to our curated reviews as well as our bookings department
for hotel reservations. As a member, you are eligible for preferred rates and
amenities at hundreds of luxury hotels. These include Aman, Four Seasons, Orient
Express, Rosewood, Peninsula, Park Hyatt and many hand-picked by our staff. We
can also access Platinum and Centurion privileges through American Express.</p>

<p style=\"margin-bottom: 15px;\">Please let us know if you are planning a trip, and we would be happy to assist you: 
<strong>bookings@indagare.com</strong> or <strong>212-988-2611</strong>. 
</p>

<p style=\"margin-bottom: 15px;\">Thank you.</p>
                                                                                                                
<p style=\"margin-bottom: 15px;\">Please find your receipt below. If you have any questions about your order, please email <a href=\"mailto:orders@indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">orders@indagare.com</a>.</p>

													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table><table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<div align=\"left\" class=\"article-content article-content-nested\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif; border-top-width: 2px; border-top-color: #00a4d3; border-top-style: solid; border-bottom-width: 2px; border-bottom-color: #00a4d3; border-bottom-style: solid; background: #e2f4fe;\">
														<p style=\"margin: 15px 10px 0;\"><strong>$name</strong></p>
										
														<p style=\"margin: 15px 10px 0;\">$street<br style=\"line-height: 100%;\" />
														$city, $state $zip $country<br style=\"line-height: 100%;\" />
														$email
														</p>

														<p class=\"cost\" style=\"color: #ee4833; margin: 15px 10px;\"><strong>ITEM: $membership</strong></p>
													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
							<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
						</tr></tbody></table></td></tr></tbody></table></td>
	</tr>
      </tbody>
    </table>
  </body>
</html>
";
}
function createThankyouRenewEmail($name, $street, $city, $state, $zip, $country, $email, $membership) {
    return "<!-- Inliner Build Version 4380b7741bb759d6cb997545f3add21ad48f010b -->
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional //EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
  <head>
    <title>Welcome back</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <meta name=\"viewport\" content=\"width=320, target-densitydpi=device-dpi\" />
<!--[if gte mso 9]>
<style _tmplitem=\"309\" >
.article-content ol, .article-content ul {
   margin: 0 0 0 24px;
   padding: 0;
   list-style-position: inside;
}
</style>
<![endif]-->
  </head>
  <body style=\"width: 100% !important; font-family: HelveticaNeue, sans-serif; background: #c7c7c7; margin: 0; padding: 0;\" bgcolor=\"#c7c7c7\">
    <style type=\"text/css\">
@media only screen and (max-width: 660px) {
  table[class=w0] {
    width: 0 !important;
  }
  td[class=w0] {
    width: 0 !important;
  }
  table[class=w10] {
    width: 10px !important;
  }
  td[class=w10] {
    width: 10px !important;
  }
  img[class=w10] {
    width: 10px !important;
  }
  table[class=w15] {
    width: 5px !important;
  }
  td[class=w15] {
    width: 5px !important;
  }
  img[class=w15] {
    width: 5px !important;
  }
  table[class=w30] {
    width: 10px !important;
  }
  td[class=w30] {
    width: 10px !important;
  }
  img[class=w30] {
    width: 10px !important;
  }
  table[class=w60] {
    width: 10px !important;
  }
  td[class=w60] {
    width: 10px !important;
  }
  img[class=w60] {
    width: 10px !important;
  }
  table[class=w125] {
    width: 80px !important;
  }
  td[class=w125] {
    width: 80px !important;
  }
  img[class=w125] {
    width: 80px !important;
  }
  table[class=w130] {
    width: 55px !important;
  }
  td[class=w130] {
    width: 55px !important;
  }
  img[class=w130] {
    width: 55px !important;
  }
  table[class=w140] {
    width: 90% !important;
  }
  td[class=w140] {
    width: 90% !important;
  }
  img[class=w140] {
    width: 90% !important;
  }
  table[class=w160] {
    width: 180px !important;
  }
  td[class=w160] {
    width: 180px !important;
  }
  img[class=w160] {
    width: 180px !important;
  }
  table[class=w170] {
    width: 100% !important;
  }
  td[class=w170] {
    width: 100% !important;
  }
  img[class=w170] {
    width: 100% !important;
  }
  table[class=w180] {
    width: 80px !important;
  }
  td[class=w180] {
    width: 80px !important;
  }
  img[class=w180] {
    width: 80px !important;
  }
  table[class=w195] {
    width: 80px !important;
  }
  td[class=w195] {
    width: 80px !important;
  }
  img[class=w195] {
    width: 80px !important;
  }
  table[class=w200] {
    width: 100% !important; float: none !important;
  }
  td[class=w200] {
    width: 100% !important; float: none !important;
  }
  img[class=w200] {
    width: 100% !important; float: none !important;
  }
  table[class=w220] {
    width: 80px !important;
  }
  td[class=w220] {
    width: 80px !important;
  }
  img[class=w220] {
    width: 80px !important;
  }
  table[class=w255] {
    width: 185px !important;
  }
  td[class=w255] {
    width: 185px !important;
  }
  img[class=w255] {
    width: 185px !important;
  }
  table[class=w260] {
    width: 100% !important;
  }
  td[class=w260] {
    width: 100% !important;
  }
  img[class=w260] {
    width: 100% !important;
  }
  table[class=w275] {
    width: 135px !important;
  }
  td[class=w275] {
    width: 135px !important;
  }
  img[class=w275] {
    width: 135px !important;
  }
  table[class=w280] {
    width: 135px !important;
  }
  td[class=w280] {
    width: 135px !important;
  }
  img[class=w280] {
    width: 135px !important;
  }
  table[class=w290] {
    width: 140px !important;
  }
  td[class=w290] {
    width: 140px !important;
  }
  img[class=w290] {
    width: 140px !important;
  }
  table[class=w300] {
    width: 140px !important;
  }
  td[class=w300] {
    width: 140px !important;
  }
  img[class=w300] {
    width: 140px !important;
  }
  table[class=w310] {
    width: 140px !important;
  }
  td[class=w310] {
    width: 140px !important;
  }
  img[class=w310] {
    width: 140px !important;
  }
  table[class=w325] {
    width: 95px !important;
  }
  td[class=w325] {
    width: 95px !important;
  }
  img[class=w325] {
    width: 95px !important;
  }
  table[class=w360] {
    width: 140px !important;
  }
  td[class=w360] {
    width: 140px !important;
  }
  img[class=w360] {
    width: 140px !important;
  }
  table[class=w410] {
    width: 100% !important; float: none !important;
  }
  td[class=w410] {
    width: 100% !important; float: none !important;
  }
  img[class=w410] {
    width: 100% !important; float: none !important;
  }
  table[class=w440] {
    width: 100% !important; float: none !important;
  }
  td[class=w440] {
    width: 100% !important; float: none !important;
  }
  img[class=w440] {
    width: 100% !important; float: none !important;
  }
  table[class=w470] {
    width: 200px !important;
  }
  td[class=w470] {
    width: 200px !important;
  }
  img[class=w470] {
    width: 200px !important;
  }
  table[class=w580] {
    width: 280px !important;
  }
  td[class=w580] {
    width: 280px !important;
  }
  img[class=w580] {
    width: 280px !important;
  }
  table[class=w640] {
    width: 300px !important;
  }
  td[class=w640] {
    width: 300px !important;
  }
  img[class=w640] {
    width: 300px !important;
  }
  table[class*=hide] {
    display: none !important;
  }
  td[class*=hide] {
    display: none !important;
  }
  img[class*=hide] {
    display: none !important;
  }
  p[class*=hide] {
    display: none !important;
  }
  span[class*=hide] {
    display: none !important;
  }
  table[class=h0] {
    height: 0 !important;
  }
  td[class=h0] {
    height: 0 !important;
  }
  #headline p {
    font-size: 30px !important;
  }
  .article-content {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  #left-sidebar {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  .header-content {
    -webkit-text-size-adjust: 80% !important; -ms-text-size-adjust: 80% !important;
  }
  img {
    height: auto; line-height: 100%;
  }
}
</style>
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"background-table\" style=\"background: #999;\" bgcolor=\"#999\">
      <tbody>
        <tr style=\"border-collapse: collapse;\"><td align=\"center\" bgcolor=\"#c7c7c7\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
        	<table class=\"w640\" style=\"margin: 0 10px;\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td id=\"header\" class=\"w640\" width=\"640\" align=\"center\" bgcolor=\"#000000\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
						<table class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td valign=\"middle\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w200\" width=\"200\" align=\"left\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											<td class=\"w140\" width=\"140\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><img class=\"w200\" alt=\"\" src=\"http://images.indagare.com/wp-content/uploads/indagare-logo-email.png\" style=\"outline: none; text-decoration: none; display: block;\" /></td>
											<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											</tr></tbody></table><table class=\"w440\" width=\"440\" align=\"right\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<td class=\"w290\" width=\"290\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\">Contact us at <strong><a href=\"mailto:bookings@indagare.com\" style=\"color: #ffffff; text-decoration: none;\">bookings@indagare.com</a></strong> or <br style=\"line-height: 100%;\" /><strong>+1 212 988 2611</strong> Monday through Friday, 9:30am-6:00pm EST</p></td>
												<td class=\"w10\" width=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<!--<td class=\"w130\" width=\"130\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\"></p></td>
												<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>-->
											</tr></tbody></table></td>
							</tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
                </tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"30\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr id=\"simple-content-row\" style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
					<table align=\"left\" class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
							<td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<p align=\"left\" class=\"article-title\" style=\"font-size: 18px; line-height: 24px; color: #000000; font-weight: bold; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">Welcome back, $name</p>
													<div align=\"left\" class=\"article-content\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">
														<p style=\"margin-bottom: 15px;\">Thank you for your renewal at Indagare.com. We are thrilled that you have chosen to continue being a part of our travel community<a href=\"http://www.indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">www.indagare.com</a></p>
                                                                                                                
<p style=\"margin-bottom: 15px;\"><strong>Reminder on How to Use Indagare</strong><br>Planning an upcoming trip? Our staff scouts the world and helps our members plan 
everything from a long weekend in LA to a safari in Botswana or a multi-generational 
birthday in Marrakech. We have a curated network of expert guides and the ability 
to create special experiences across the globe.</p>

<p style=\"margin-bottom: 15px;\">As you know, all members have access to our curated reviews as well as our bookings department
for hotel reservations. As a member, you are eligible for preferred rates and
amenities at hundreds of luxury hotels. These include Aman, Four Seasons, Orient
Express, Rosewood, Peninsula, Park Hyatt and many hand-picked by our staff. We
can also access Platinum and Centurion privileges through American Express.</p>

<p style=\"margin-bottom: 15px;\">Please let us know if you are planning a trip, and we would be happy to assist you: 
<strong>bookings@indagare.com</strong> or <strong>212-988-2611</strong>. 
</p>

<p style=\"margin-bottom: 15px;\">Thank you.</p>
                                                                                                                
<p style=\"margin-bottom: 15px;\">Please find your receipt below. If you have any questions about your order, please email <a href=\"mailto:orders@indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">orders@indagare.com</a>.</p>

													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table><table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<div align=\"left\" class=\"article-content article-content-nested\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif; border-top-width: 2px; border-top-color: #00a4d3; border-top-style: solid; border-bottom-width: 2px; border-bottom-color: #00a4d3; border-bottom-style: solid; background: #e2f4fe;\">
														<p style=\"margin: 15px 10px 0;\"><strong>$name</strong></p>
										
														<p style=\"margin: 15px 10px 0;\">$street<br style=\"line-height: 100%;\" />
														$city, $state $zip $country<br style=\"line-height: 100%;\" />
														$email
														</p>

														<p class=\"cost\" style=\"color: #ee4833; margin: 15px 10px;\"><strong>ITEM: $membership</strong></p>
													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
							<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
						</tr></tbody></table></td></tr></tbody></table></td>
	</tr>
      </tbody>
    </table>
  </body>
</html>
";
}
function createThankyouUpgradeEmail($name, $street, $city, $state, $zip, $country, $email, $membership) {
    return "<!-- Inliner Build Version 4380b7741bb759d6cb997545f3add21ad48f010b -->
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional //EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
  <head>
    <title>Welcome back</title>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
    <meta name=\"viewport\" content=\"width=320, target-densitydpi=device-dpi\" />
<!--[if gte mso 9]>
<style _tmplitem=\"309\" >
.article-content ol, .article-content ul {
   margin: 0 0 0 24px;
   padding: 0;
   list-style-position: inside;
}
</style>
<![endif]-->
  </head>
  <body style=\"width: 100% !important; font-family: HelveticaNeue, sans-serif; background: #c7c7c7; margin: 0; padding: 0;\" bgcolor=\"#c7c7c7\">
    <style type=\"text/css\">
@media only screen and (max-width: 660px) {
  table[class=w0] {
    width: 0 !important;
  }
  td[class=w0] {
    width: 0 !important;
  }
  table[class=w10] {
    width: 10px !important;
  }
  td[class=w10] {
    width: 10px !important;
  }
  img[class=w10] {
    width: 10px !important;
  }
  table[class=w15] {
    width: 5px !important;
  }
  td[class=w15] {
    width: 5px !important;
  }
  img[class=w15] {
    width: 5px !important;
  }
  table[class=w30] {
    width: 10px !important;
  }
  td[class=w30] {
    width: 10px !important;
  }
  img[class=w30] {
    width: 10px !important;
  }
  table[class=w60] {
    width: 10px !important;
  }
  td[class=w60] {
    width: 10px !important;
  }
  img[class=w60] {
    width: 10px !important;
  }
  table[class=w125] {
    width: 80px !important;
  }
  td[class=w125] {
    width: 80px !important;
  }
  img[class=w125] {
    width: 80px !important;
  }
  table[class=w130] {
    width: 55px !important;
  }
  td[class=w130] {
    width: 55px !important;
  }
  img[class=w130] {
    width: 55px !important;
  }
  table[class=w140] {
    width: 90% !important;
  }
  td[class=w140] {
    width: 90% !important;
  }
  img[class=w140] {
    width: 90% !important;
  }
  table[class=w160] {
    width: 180px !important;
  }
  td[class=w160] {
    width: 180px !important;
  }
  img[class=w160] {
    width: 180px !important;
  }
  table[class=w170] {
    width: 100% !important;
  }
  td[class=w170] {
    width: 100% !important;
  }
  img[class=w170] {
    width: 100% !important;
  }
  table[class=w180] {
    width: 80px !important;
  }
  td[class=w180] {
    width: 80px !important;
  }
  img[class=w180] {
    width: 80px !important;
  }
  table[class=w195] {
    width: 80px !important;
  }
  td[class=w195] {
    width: 80px !important;
  }
  img[class=w195] {
    width: 80px !important;
  }
  table[class=w200] {
    width: 100% !important; float: none !important;
  }
  td[class=w200] {
    width: 100% !important; float: none !important;
  }
  img[class=w200] {
    width: 100% !important; float: none !important;
  }
  table[class=w220] {
    width: 80px !important;
  }
  td[class=w220] {
    width: 80px !important;
  }
  img[class=w220] {
    width: 80px !important;
  }
  table[class=w255] {
    width: 185px !important;
  }
  td[class=w255] {
    width: 185px !important;
  }
  img[class=w255] {
    width: 185px !important;
  }
  table[class=w260] {
    width: 100% !important;
  }
  td[class=w260] {
    width: 100% !important;
  }
  img[class=w260] {
    width: 100% !important;
  }
  table[class=w275] {
    width: 135px !important;
  }
  td[class=w275] {
    width: 135px !important;
  }
  img[class=w275] {
    width: 135px !important;
  }
  table[class=w280] {
    width: 135px !important;
  }
  td[class=w280] {
    width: 135px !important;
  }
  img[class=w280] {
    width: 135px !important;
  }
  table[class=w290] {
    width: 140px !important;
  }
  td[class=w290] {
    width: 140px !important;
  }
  img[class=w290] {
    width: 140px !important;
  }
  table[class=w300] {
    width: 140px !important;
  }
  td[class=w300] {
    width: 140px !important;
  }
  img[class=w300] {
    width: 140px !important;
  }
  table[class=w310] {
    width: 140px !important;
  }
  td[class=w310] {
    width: 140px !important;
  }
  img[class=w310] {
    width: 140px !important;
  }
  table[class=w325] {
    width: 95px !important;
  }
  td[class=w325] {
    width: 95px !important;
  }
  img[class=w325] {
    width: 95px !important;
  }
  table[class=w360] {
    width: 140px !important;
  }
  td[class=w360] {
    width: 140px !important;
  }
  img[class=w360] {
    width: 140px !important;
  }
  table[class=w410] {
    width: 100% !important; float: none !important;
  }
  td[class=w410] {
    width: 100% !important; float: none !important;
  }
  img[class=w410] {
    width: 100% !important; float: none !important;
  }
  table[class=w440] {
    width: 100% !important; float: none !important;
  }
  td[class=w440] {
    width: 100% !important; float: none !important;
  }
  img[class=w440] {
    width: 100% !important; float: none !important;
  }
  table[class=w470] {
    width: 200px !important;
  }
  td[class=w470] {
    width: 200px !important;
  }
  img[class=w470] {
    width: 200px !important;
  }
  table[class=w580] {
    width: 280px !important;
  }
  td[class=w580] {
    width: 280px !important;
  }
  img[class=w580] {
    width: 280px !important;
  }
  table[class=w640] {
    width: 300px !important;
  }
  td[class=w640] {
    width: 300px !important;
  }
  img[class=w640] {
    width: 300px !important;
  }
  table[class*=hide] {
    display: none !important;
  }
  td[class*=hide] {
    display: none !important;
  }
  img[class*=hide] {
    display: none !important;
  }
  p[class*=hide] {
    display: none !important;
  }
  span[class*=hide] {
    display: none !important;
  }
  table[class=h0] {
    height: 0 !important;
  }
  td[class=h0] {
    height: 0 !important;
  }
  #headline p {
    font-size: 30px !important;
  }
  .article-content {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  #left-sidebar {
    -webkit-text-size-adjust: 90% !important; -ms-text-size-adjust: 90% !important;
  }
  .header-content {
    -webkit-text-size-adjust: 80% !important; -ms-text-size-adjust: 80% !important;
  }
  img {
    height: auto; line-height: 100%;
  }
}
</style>
    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"background-table\" style=\"background: #999;\" bgcolor=\"#999\">
      <tbody>
        <tr style=\"border-collapse: collapse;\"><td align=\"center\" bgcolor=\"#c7c7c7\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
        	<table class=\"w640\" style=\"margin: 0 10px;\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td id=\"header\" class=\"w640\" width=\"640\" align=\"center\" bgcolor=\"#000000\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
						<table class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr style=\"border-collapse: collapse;\"><td valign=\"middle\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w200\" width=\"200\" align=\"left\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											<td class=\"w140\" width=\"140\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><img class=\"w200\" alt=\"\" src=\"http://images.indagare.com/wp-content/uploads/indagare-logo-email.png\" style=\"outline: none; text-decoration: none; display: block;\" /></td>
											<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
											</tr></tbody></table><table class=\"w440\" width=\"440\" align=\"right\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<td class=\"w290\" width=\"290\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\">Contact us at <strong><a href=\"mailto:bookings@indagare.com\" style=\"color: #ffffff; text-decoration: none;\">bookings@indagare.com</a></strong> or <br style=\"line-height: 100%;\" /><strong>+1 212 988 2611</strong> Monday through Friday, 9:30am-6:00pm EST</p></td>
												<td class=\"w10\" width=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
												<!--<td class=\"w130\" width=\"130\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"><p style=\"font-size: 13px; line-height: 18px; color: #ffffff; font-family: HelveticaNeue, sans-serif; margin: 0;\"></p></td>
												<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>-->
											</tr></tbody></table></td>
							</tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"20\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
                </tr><tr style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" height=\"30\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr><tr id=\"simple-content-row\" style=\"border-collapse: collapse;\"><td class=\"w640\" width=\"640\" bgcolor=\"#ffffff\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
					<table align=\"left\" class=\"w640\" width=\"640\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
							<td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
										<table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<p align=\"left\" class=\"article-title\" style=\"font-size: 18px; line-height: 24px; color: #000000; font-weight: bold; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">Welcome back, $name</p>
													<div align=\"left\" class=\"article-content\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif;\">
														<p style=\"margin-bottom: 15px;\">Thank you for your membership upgrade at Indagare.com. We are thrilled that you have chosen to continue being a part of our travel community<a href=\"http://www.indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">www.indagare.com</a></p>
                                                                                                                
<p style=\"margin-bottom: 15px;\"><strong>Reminder on How to Use Indagare</strong><br>Planning an upcoming trip? Our staff scouts the world and helps our members plan 
everything from a long weekend in LA to a safari in Botswana or a multi-generational 
birthday in Marrakech. We have a curated network of expert guides and the ability 
to create special experiences across the globe.</p>

<p style=\"margin-bottom: 15px;\">As you know, all members have access to our curated reviews as well as our bookings department
for hotel reservations. As a member, you are eligible for preferred rates and
amenities at hundreds of luxury hotels. These include Aman, Four Seasons, Orient
Express, Rosewood, Peninsula, Park Hyatt and many hand-picked by our staff. We
can also access Platinum and Centurion privileges through American Express.</p>

<p style=\"margin-bottom: 15px;\">Please let us know if you are planning a trip, and we would be happy to assist you: 
<strong>bookings@indagare.com</strong> or <strong>212-988-2611</strong>. 
</p>

<p style=\"margin-bottom: 15px;\">Thank you.</p>
                                                                                                                
<p style=\"margin-bottom: 15px;\">Please find your receipt below. If you have any questions about your order, please email <a href=\"mailto:orders@indagare.com\" style=\"color: #000000; font-weight: bold; text-decoration: none;\">orders@indagare.com</a>.</p>

													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table><table class=\"w580\" width=\"580\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tbody><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\">
													<div align=\"left\" class=\"article-content article-content-nested\" style=\"font-size: 13px; line-height: 18px; color: #000000; margin-top: 0px; margin-bottom: 18px; font-family: HelveticaNeue, sans-serif; border-top-width: 2px; border-top-color: #00a4d3; border-top-style: solid; border-bottom-width: 2px; border-bottom-color: #00a4d3; border-bottom-style: solid; background: #e2f4fe;\">
														<p style=\"margin: 15px 10px 0;\"><strong>$name</strong></p>
										
														<p style=\"margin: 15px 10px 0;\">$street<br style=\"line-height: 100%;\" />
														$city, $state $zip $country<br style=\"line-height: 100%;\" />
														$email
														</p>

														<p class=\"cost\" style=\"color: #ee4833; margin: 15px 10px;\"><strong>ITEM: $membership</strong></p>
													</div>
												</td>
											</tr><tr style=\"border-collapse: collapse;\"><td class=\"w580\" width=\"580\" height=\"10\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td></tr></tbody></table></td>
							<td class=\"w30\" width=\"30\" style=\"border-collapse: collapse; font-family: HelveticaNeue, sans-serif;\"></td>
						</tr></tbody></table></td></tr></tbody></table></td>
	</tr>
      </tbody>
    </table>
  </body>
</html>
";
}
?>
