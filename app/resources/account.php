<h1 class="hidden">Account Details</h1>
<div id="account-tab-container">
	<div id="account-tab-container-nav">
	    <ul id="account-tab-nav-list" class="clearfix">
	    	<li class="mobile prevbutton"><div class="" onclick="tabber1.previous();"></div></li>
		    <li class="tab-nav-item"><a href="#dashboard">Dashboard</a></li>
		    <li class="tab-nav-item billingtab hidden"><a href="#billing">Billing</a></li>
		    <li class="tab-nav-item"><a href="#contactdetails">Contact Details</a></li>
		    <li class="tab-nav-item"><a href="#prefs">Travel Preferences</a></li>
		    <li class="tab-nav-item"><a href="#accounts">Travel Accounts</a></li>
	    	<li class="mobile nextbutton"><div class="" onclick="tabber1.next();"></div></li>
	    </ul>
    </div>
    	
    <div id="account-header-display">
	    <div id="account-isprimary-display" data-source-object="Account" data-display-if="IsPrimaryContact__x=true" style="display:none;">Primary Account</div>
	    <div id="account-display-contact-wrapper"><select name="contactselect" id="contactselect"></select></div>
		<div class="new-contact-wrapper" data-source-object="Account" data-display-if="IsPrimaryContact__x=true" style="display:none;"><a id="new-contact-link" href="#">Add Travel Companion</a></div>
		<div id="account-display-membership-level">
			<label for="account-bar-status">Membership
				Level:</label><div id="account-bar-status" data-source-object="Membership"
				data-display-field="Name"></div>
		</div>
		<div class="renew-wrapper" data-source-object="Account" data-display-if="IsPrimaryContact__x=true" style="display:none;"><a id="renew-link" href="#billing">Upgrade<span  data-source-object="Account" data-display-if="Is_Renewal__c=false" style="display:none;">/Renew</span> Membership</a></div>
   		<div class="valid-through">
   			<label for="account-bar-end">Valid Through</label>
   			<span id="account-bar-end" data-source-object="Account"
			data-display-field="Membership_End_Date__c"></span>
	    </div>
   		<div class="member-since">
   			<label for="account-bar-end">Member Since</label>
   			<span id="account-bar-end" data-source-object="Account"
			data-display-field="Member_Since__c"></span>
	    </div>
   		<div class="renewal-text" data-source-object="Account" data-display-if="Is_Renewal__c=true" style="display:none;">
   			(Your Indagare account will renew 7 days prior to your membership lapsing.)
	    </div>
    </div>
    
    <div class="tab" id="dashboard">
	    <div class="tab-content">
		    <h2>Get Inspired</h2>
	    </div>
    </div>
	
    <div class="tab billingtab hidden" id="billing">
	    <div class="tab-content">
		    <h2>Billing Address</h2>
		    <div>
				<form class="clearfix" id="billing-detail-form" data-source-object="Account"
					data-edit-field="Id,BillingStreet,BillingCity,BillingState,BillingPostalCode,BillingCountry">
					<a class="form-save-button" action="save" href="#">Save</a>
					<a class="form-cancel-button" href="#">Close</a>
					<a class="form-edit-link" href="#">Edit Billing Address</a>
				</form>
		    </div>
		    
		    <h2>Payment Type</h2>
		    <div>
				<form class="clearfix" id="payment-detail-form" data-source-object="Account"
					data-edit-field="Id,Name,
						Credit_Card_Type__c|type=hidden|validate-type=cc_type|validate-group=b,
						Credit_Card_Number__c|validate-type=cc_num|validate-group=b,
						Credit_Card_Month__c|type=month|validate-type=cc_month|validate-group=c,
						Credit_Card_Year__c|type=cc_year|validate-type=cc_year|validate-group=c,
						Card_CVV_Number__c|validate-type=cc_cvv|validate-group=b
				">

					<a class="form-save-button" action="save" href="#">Save</a>
					<a class="form-cancel-button" href="#">Close</a>
					<a class="form-edit-link" href="#">Edit Card Information</a>
				</form>
		    </div>
		    
		    <h2>Membership Level</h2>
		    <div>
				<form class="clearfix editing" id="account-membership-upgrade-form">
				
					<div id="field-account-membership-upgrade-select" class="input-field field clearfix">
						<select id="account-membership-upgrade-select"></select>
						<label for="account-membership-upgrade-select">Membership Level</label>
					</div>
					<a id="upgrade-button" class="form-save-button" action="upgrade" href="#">Upgrade Now</a>
					<a id="autorenew-button" class="form-save-button" action="autorenew" href="#" style="display:none;">Enable Autorenew</a>
				</form>
		    </div>
	    </div>
    </div>
	
    <div class="tab" id="contactdetails">
	    <div class="tab-content">
	    	<h2>Login</h2>
	    	<form class="clearfix" id="contact-login" data-source-object="Contact"
				data-edit-field="Id,Email|validate-type=wp-unique-email">
				<div field-instance="chpassword1" id="field-wp-chpassword1" class="input-field field clearfix iform-row-3col">
					<input name="pwd1" id="wp-chpassword1" type="password" validate-group="pw" validate-type="password">	
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
		
				<div field-instance="chpassword2" id="field-wp-chpassword2" class="input-field field clearfix iform-row-3col">
					<input name="pwd2" id="wp-chpassword2" type="password" validate-group="pw" validate-type="password-verify">
					<label for="wp-password2">Verify Password</label>
				    <span class="errmsg">Passwords must match.</span>
				</div>

				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Login Information</a>
	    	</form>
	    	
		    <h2>Personal Details</h2>
	    	<form class="clearfix" id="contact" data-source-object="Contact"
				data-edit-field="Id,Salutation,FirstName|validate-type=name,LastName|validate-type=name,Title,Birthdate,Anniversary_Date__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Personal Details</a>
		    </form>
		    
		    <h2>Address</h2>
	    	<form class="clearfix" id="contact" data-source-object="Contact"
				data-edit-field="Id,
			        MailingStreet,MailingCity,MailingState,MailingPostalCode,MailingCountry,
			        OtherStreet,OtherCity,OtherState,OtherPostalCode,OtherCountry">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Address</a>
		    </form>
		    
		    <h2>Telephone</h2>
	    	<form class="clearfix" id="contact" data-source-object="Contact"
				data-edit-field="Id,
			        HomePhone|type=tel,Phone|type=tel,MobilePhone|type=tel">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Telephone</a>
		    </form>
		    
		    <h2>Assistant</h2>
	    	<form class="clearfix" id="contact" data-source-object="Contact"
				data-edit-field="Id,AssistantName|validate-type=name,Assistant_Email__c|validate-type=email">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Assistant Information</a>
		    </form>
		    
		</div>
    </div>
	
    <div class="tab" id="prefs">
	    <div class="tab-content">
		    <h2>Communication</h2>
			<form class="clearfix" id="travel-preferences" data-source-object="Contact"
				data-edit-field="Id,Preferred_Method_of_Contact__c,Phone_Communication_Preferences__c,
	        		Preferred_Format_to_Receive_Itineraries__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Communication Preferences</a>
			</form>
			
		    <h2>Itinerary</h2>
			<form class="clearfix" id="travel-preferences" data-source-object="Contact"
				data-edit-field="Id,How_Often_Do_You_Travel__c,How_Do_You_Like_to_Travel__c,Travel_Planning_Style__c,Who_Do_You_Travel_With__c,Prefer_a_Planned_Out_Schedule__c,
	        		Prefer_Doing_Things_on_Own__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Itinerary Preferences</a>
			</form>
			
		    <h2>Flights</h2>
			<form class="clearfix" id="travel-preferences" data-source-object="Contact"
				data-edit-field="Id,Short_Haul_Class_of_Service__c,Long_Haul_Class_of_Service__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Flight Preferences</a>
			</form>
			
		    <h2>Hotels</h2>
			<form class="clearfix" id="travel-preferences" data-source-object="Contact"
				data-edit-field="Id,Hotel_Style__c,Hotel_Amenities__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Hotel Preferences</a>
			</form>
			
		    <h2>Food</h2>
			<form class="clearfix" id="travel-preferences" data-source-object="Contact"
				data-edit-field="Id,Meal_Choice__c,Allergies_Food_Restrictions__c,Type_of_Restaurants_Preferred__c,
	        		Beverage_of_Choice__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Food Preferences</a>
			</form>
			
		    <h2>Other Travel Preferences</h2>
			<form class="clearfix" id="questionnaire" data-source-object="Contact"
				data-edit-field="Id,
	        		Travel_Memories__c,Pet_Peeves__c,Influences__c,Interests__c,
	        		Other_Travel_Preferences__c">
				<a class="form-save-button" action="save" href="#">Save</a>
				<a class="form-cancel-button" href="#">Close</a>
				<a class="form-edit-link" href="#">Edit Other Preferences</a>
			</form>
			
	    </div>
    </div>
	
    <div class="tab" id="accounts">
	    <div class="tab-content">
		    <h2>Travel Accounts</h2>
			<div id="profile-freq" class="card-list clearfix"></div>
			<div id="profile-freq-buttons" class="card-button-list card-list clearfix">
				<div class="card-item card-item-button">
					<a id="profile-freq-new" href="#" class="card-button-large">
						Add New Frequent Traveler Account
					</a>
				</div>
			</div>
		    
		    <h2>Travel Documents</h2>
			<div id="profile-passports" class="card-list clearfix"></div>
			<div id="profile-passports-buttons" class="card-button-list card-list clearfix">
				<div class="card-item card-item-button">
					<a id="profile-passport-new" href="#" class="card-button-large">
						Add New Travel Document
					</a>
				</div>
			</div>
	    </div>
    </div>
	
</div>

<div class="help-box-wrapper">
	<div class="help-box">
		<h2 class="help-box-title">Questions about your account?</h2>
		<div class="help-box-content">
			<span class="keep-together">Contact our membership team</span>
			<span class="keep-together">at <a href="tel:+12129882611">212-988-2611</a></span>
			<span class="keep-together">or email <a href="mailto:membership@indagare.com">membership@indagare.com</a>.</span>
		</div>
	</div>
</div>



<div id="new-contact-form" style="display: none !important;">
	<div class="detail-element clearfix">
		<form class="editing clearfix">
			<div id="field-Username" class="input-field field clearfix">
				<input maxlength="80" name="Username" id="newcontact-Username"
					type="text" validate-type="wp-unique-username"><label for="contactUsername">Username</label>
				    <span class="errmsg">Username is not available.  Please try another one.</span>
			</div>
			<div id="field-Password" class="input-field field clearfix">
				<input maxlength="80" name="Password" id="newcontact-Password"
					type="password" validate-type="password" validate-group="a"><label for="contactPassword">Password</label>
				    <span class="errmsg">Passwords must:<br/>
				    	<ul>
						 	<li class="passlen-msg">Be at least <span class="passlen-num">6</span> characters long</li>
						 	<li class="passcase-msg">Contain a mix of uppercase and lowercase letters</li>
						 	<li class="passnum-msg">Contain at least one number</li>
						 	<li class="passchar-msg">Contain at least one special character (non-letter or number)</li>
					 	</ul>
				 	</span>
			</div>
			<div id="field-PasswordVerify" class="input-field field clearfix">
				<input maxlength="80" name="PasswordVerify"
					id="newcontact-PasswordVerify" type="password" validate-type="password-verify" validate-group="a"><label
					for="contactPasswordVerify">Verify Password</label>
				    <span class="errmsg">Passwords must match.</span>
			</div>
			<div id="field-FirstName" class="input-field field clearfix">
				<input maxlength="40" name="FirstName" id="newcontact-FirstName"
					type="text" validate-type="exists"><label for="contactFirstName">First
					Name</label>
				    <span class="errmsg">You must enter a First Name</span>
			</div>
			<div id="field-LastName" class="input-field field clearfix">
				<input maxlength="80" name="LastName" id="newcontact-LastName"
					type="text" validate-type="exists"><label for="contactLastName">Last Name</label>
				    <span class="errmsg">You must enter a Last Name.</span>
			</div>
			<div id="field-Email" class="input-field field clearfix">
				<input maxlength="80" name="Email" id="newcontact-Email" type="text" validate-type="wp-unique-email"><label
					for="contactEmail">Email</label>
				    <span class="errmsg"></span>
			</div>
			<div id="field-Phone" class="input-field field clearfix">
				<input maxlength="40" name="Phone" id="newcontact-Phone" type="tel" validate-type="phone"><label
					for="contactPhone">Phone</label>
				    <span class="errmsg">You must enter a phone number.</span>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/countries.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/numeral.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery.scrollTo.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery-confirm.min.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/jquery.creditCardValidator.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/shr.validate.js"></script>
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/account.js"></script>
