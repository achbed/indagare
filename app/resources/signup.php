<?php print '<!-- PHP VERSION -->'; ?><div id="signup-form-container">
	<div class="tab">
		<div class="tab-content">
		
			<div id="formdescription"></div>
	
		    <h2>Account Information</h2>
		    
		    <form class="editing clearfix">
				<div field-instance="username" id="field-wp-username" class="input-field field clearfix iform-row-3col iform-row-clear">
					<input name="username" id="wp-username" type="text" validate-type="wp-unique-username">
					<label for="wp-username">Username</label>
				    <span class="errmsg">Username is not available.  Please try another one.</span>
				</div>
		
				<div field-instance="password1" id="field-wp-password1" class="input-field field clearfix iform-row-3col">
					<input name="pwd1" id="wp-password1" type="password" validate-group="pw" validate-type="password">
					<label for="wp-password1">Password</label>
				    <span class="errmsg">Passwords must:<br/>
				    	<ul>
						 	<li id="passlen">Be at least <span id="passlen_num">6</span> characters long</li>
						 	<li id="passcase">Contain a mix of uppercase and lowercase letters</li>
						 	<li id="passnum">Contain at least one number</li>
						 	<li id="passchar">Contain at least one special character (non-letter or number)</li>
					 	</ul>
				 	</span>
				</div>
		
				<div field-instance="password2" id="field-wp-password2" class="input-field field clearfix iform-row-3col">
					<input name="pwd2" id="wp-password2" type="password" validate-group="pw" validate-type="password-verify">
					<label for="wp-password2">Verify Password</label>
				    <span class="errmsg">Passwords must match.</span>
				</div>

				<div field-instance="FirstName" id="field-contact-FirstName" class="input-field field clearfix iform-row-2col iform-row-clear">
					<input name="FirstName" id="contact-FirstName" type="text" validate-type="name">
					<label for="contact-FirstName">First Name</label>
				    <span class="errmsg">You must enter a First Name</span>
				</div>
				
				<div field-instance="LastName" id="field-contact-LastName" class="input-field field clearfix iform-row-2col">
					<input name="LastName" id="contact-LastName" type="text" validate-type="name">
					<label for="contact-LastName">Last Name</label>
				    <span class="errmsg">You must enter a Last Name.</span>
				</div>
				
				<div field-instance="Email" id="field-contact-Email" class="input-field field clearfix iform-row-2col iform-row-clear">
					<input name="Email" id="contact-Email" type="text" validate-type="sf-unique-email">
					<label for="contact-Email">Email</label>
				    <span class="errmsg"></span>
				</div>
				
				<div field-instance="HomePhone" id="field-contact-HomePhone" class="input-field field clearfix iform-row-2col">
					<input name="HomePhone" id="contact-HomePhone" type="tel" validate-type="phone">
					<label for="contact-HomePhone">Phone</label>
				    <span class="errmsg">You must enter a phone number.</span>
				</div>
		
				<div field-instance="HearAbout" id="field-contact-HearAbout" class="input-field field clearfix iform-row-2col iform-row-clear">
					<select name="HearAbout" id="contact-HearAbout" validate-type="ignore">
					<option value="">Choose one...</option>
					<?php
						$a = new \WPSF\Account();
						$options = $a->picklistValues('How_Did_You_Hear_About_Us__c');
						foreach ( $options as $o ) {
							print "<option value=\"{$o['value']}\">{$o['label']}</option>";
						}
					?>
					</select>
					<label for="contact-HearAbout">How Did You Hear About Us?</label>
				    <span class="errmsg"></span>
				</div>
				
				<div field-instance="ReferredBy" id="field-contact-ReferredBy" class="input-field field clearfix iform-row-2col">
					<input name="ReferredBy" id="contact-ReferredBy" type="text" validate-type="ignore">
					<label for="contact-ReferredBy">Referred By?</label>
				    <span class="errmsg"></span>
				</div>
		
			</form>

		    <h2>Membership Level</h2>
		
			<form class="editing clearfix">
				<div id="field-account-tgCode" class="input-field field clearfix iform-row-2col iform-row-clear">
					<input maxlength="40" name="tgCode" id="tgCode" type="text" value="" validate-type="sf-promocode">
					<label for="contact-FirstName">Membership Code</label>
				    <span class="errmsg">Invalid membership code.</span>
				</div>
			    
				<div id="field-account-Membership_Level__c" class="input-field field clearfix iform-row-2col">
					<select name="Membership_Level__c" id="Membership_Level__c" validate-type="ignore">
					</select>
					<label for="membership_level">Membership</label>
				</div>
			</form>
	
	        <h2 class="billing">Billing Information</h2>
	        
	        <form class="editing clearfix billing">
				<div id="field-contact-s_address1" class="input-field field clearfix iform-row-1col">
					<textarea name="s_address1" id="s_address1" validate-type="exists"></textarea>
					<label for="s_address1">Street Address</label>
				    <span class="errmsg">You must enter a street address.</span>
				</div>
		
				<div id="field-contact-s_city" class="input-field field clearfix iform-row-4col">
					<input name="s_city" id="s_city" type="text" validate-type="exists">
					<label for="s_city">City</label>
				    <span class="errmsg">You must enter a city.</span>
				</div>
		
				<div id="field-s_state" class="input-field field clearfix iform-row-4col">
					<select name="s_state" id="s_state" validate-type="exists"></select>
					<label for="s_state">State / Province</label>
				    <span class="errmsg">You must choose a state/province.</span>
				</div>
		
				<div id="field-s_zip" class="input-field field clearfix iform-row-4col">
					<input name="s_zip" id="s_zip" type="text" validate-type="exists">
					<label for="s_zip">Zip / Postal Code</label>
				    <span class="errmsg">You must enter a postal code.</span>
				</div>
		
				<div id="field-s_country" class="input-field field clearfix iform-row-4col">
					<select name="s_country" id="s_country" validate-type="exists"></select>
					<label for="s_country">Country</label>
				    <span class="errmsg">You must choose a country.</span>
				</div>
				
				<div id="field-cc_num" class="input-field field clearfix iform-row-2col">
					<input name="cc_num" id="cc_num" type="text" validate-group="cc" validate-type="cc_num">
					<label for="cc_num">Credit Card Number</label>
				    <span class="errmsg">Please enter a valid credit card number.</span>
				</div>
		
				<div id="field-cc_month" class="input-field field clearfix iform-row-6col">
					<select name="cc_month" id="cc_month" validate-group="cc" validate-type="cc_month"></select>
					<label for="cc_month">Expiration Month</label>
				    <span class="errmsg">You must choose a valid month.</span>
				</div>
		
				<div id="field-cc_year" class="input-field field clearfix iform-row-6col">
					<select name="cc_year" id="cc_year" validate-group="cc" validate-type="cc_year"></select>
					<label for="cc_year">Expiration Year</label>
				    <span class="errmsg">You must choose a valid year.</span>
				</div>
		
				<div id="field-ccv" class="input-field field clearfix iform-row-6col">
					<input name="ccv" id="ccv" type="text" validate-group="cc" validate-type="cvv">
					<label for="ccv">CVV</label>
				    <span class="errmsg">Please enter the security code on the back of your credit card.</span>
				</div>
		
			</form>
	
		    <div class="inputgroup hidden">
			    <input type="hidden" name="refCode" id="refCode" value="">
			    <input type="hidden" name="cc_type" id="cc_type" value="">
			    <?php wp_nonce_field( IND_SIGNUP_NONCE_ACTION, IND_SIGNUP_NONCE_NAME ); ?>
	        </div>
	        
		    <div class="inputgroup">
		        <div class="field validate noicon"><label></label><input type="checkbox" id="agree2terms" validate-type="checked"> I have read and agree to the <a href="#" id="view_terms">Terms &amp; Conditions</a> <span class="errmsg">You must check this box to show that you agree to our Terms & Conditions.</span></div>
	        	<div id="terms" style="display: none">
		            <h4><strong>Terms</strong></h4>
		            <p>If you are not satisfied with your membership, you may cancel within 30 days of purchase and receive a full refund. Otherwise, all membership fees are non-refundable. By clicking on the Create Account button below, I authorize Indagare Travel to initiate an electronic debit to my bank account in the amount stated above.</p>
		            <p>In order to set up an Indagare Travel membership you must provide us with valid credit card information. By submitting such credit card information, you give Indagare Travel permission to charge all fees incurred through your account to the credit card you designate on your registration form. All such fees (including renewal fees) will be charged at the time they are incurred. Renewal fees will be incurred upon the commencement of each renewal term.</p>
	            	<p>Indagare Travel will send an e-mail to the e-mail address in your registration form reminding you that your Indagare Travel membership is about to expire. We will send you this notice at least 60 days before the expiration date of your membership. If you do not contact Indagare Travel, as instructed on your e-mail reminder, indicating that you do not wish to renew, Indagare Travel will automatically renew your membership for the same membership term as your previous Indagare Travel membership and charge your credit card on the first day of your renewal membership term. The renewal charge will be equal to the then-current membership fee for your membership term. By registering, you give permission to Indagare Travel to automatically charge your membership fee to your credit card for each renewal term.</p>
	        	</div>
	       
	        	<div class="field"><label></label><input type="Button" name="subTab3" id="subTab3" class="button primary" value="Create Account"></div>
	    	</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="/wp-content/themes/indagare/app/js/countries.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/numeral.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery.scrollTo.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery-confirm.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery.creditCardValidator.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/shr.validate.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/signup.js"></script>
