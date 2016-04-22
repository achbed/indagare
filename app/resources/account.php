<?php ?>
<div id="tab-container">   
    
    <ul id="tab-container-nav" class="show">
        <li><span class="link"><a id="aTab1" href="#tab1">Contact</a></span></li>
        <li><span class="link"><a id="aTab2" href="#tab2">Membership</a></span></li>
        <li><span class="link"><a id="aTab3" href="#tab3">Benefits</a></span></li>
        <li><span class="link"><a id="aTab4" href="#tab4">Travel Profile</a></span></li>
        <li><span class="link"><a id="aTab5" href="#tab5">Travel Preferences</a></span></li>
    </ul>
    
    <div class="tab" id="tab1">
        <!--<h1>Contact</h1>-->
        <form action="/wp-content/themes/indagare/app/lib/account_user_update.php" method="POST">
            <div class="field"><label>Name</label><span class="form-item"><select id="user_prefix" name="prefix" class="small"><option value=""></option>
                <option value="Mr.">Mr.</option>
                <option value="Mrs.">Mrs.</option>
                <option value="Ms.p">Ms.</option>
                <option value="Dr.">Dr.</option>
            </select><b class="icon" data-icon="&#xf0d7;"></b></span>
                <input type="text" name="fn" id="fn" value=""><input class="mini" type="text" name="initial" id="initial" value="" size="3"><input type="text" name="ln" id="ln" value=""><span id="lnval" class="validate"></span></div>
            <div class="field"><label>Email *</label><input type="email" name="email" id="email" value=""><span id="emailval" class="validate"></span></div>
            <h3></h3>
            <div class="field"><label>Street Address 1</label><input type="text" name="s_address1" id="s_address1"><span id="shipval" class="validate validatetext"></span></div>
            <div class="field"><label>Street Address 2</label><input type="text" name="s_address2" id="s_address2"></div>
            <div class="field"><label>City</label><input type="text" name="s_city" id="s_city"></div>
            <div class="field"><label>State/Province</label><input type="text" name="s_state" id="s_state"></div>
            <div class="field"><label>Zip/Postal Code</label><input type="text" name="s_zip" id="s_zip"></div>
            <div class="field"><label>Country</label><input type="text" name="s_country" id="s_country"></div>
            <div class="field"><label>Phone (Home)</label><input type="text" name="phone" id="phone" value=""><span id="phoneval" class="validate"></span></div>
            <div class="field"><label>Phone (Work)</label><input type="text" name="phone_w" id="phone_w" value=""><span id="phoneval" class="validate"></span></div>
            <div class="field"><label>Phone (Mobile)</label><input type="text" name="phone_m" id="phone_m" value=""><span id="phoneval" class="validate"></span></div>

            <div class="field"><label></label><input type="submit" name="subTab1" id="subTab1" class="button primary" value="Update"></div>
        </form>
    </div>
    
    <div class="tab" id="tab2">
        <!--<h1>Membership</h1>-->
        <div class="field"><label>Membership Level: </label><span class="form-item"><b class="icon" data-icon="&#xf0d7;"><span id="member_level"></span></b></span></div>
        <div class="field"><label>Expiration: </label><span class="form-item"><b class="icon" data-icon="&#xf0d7;"><span id="member_exp"></span></b></span><input type="submit" name="renew" id="renew" class="button primary" value="Renew"></div>
    
        <div class="renew_pane" id="renew_pane">
            <h1>Payment</h1>
            <h3>Membership Information</h3>
            <div class="field contain"><label>Membership Level</label><span id="renew_pane_member_level"></span></div>
            <div class="field"><label>Amount/Duration</label><span class="form-item"><select id="membership_years"></select><b class="icon" data-icon="&#xf0d7;"></b></span></div>

            <div id="billingBlock">
                <h3>Billing Address</h3>
                <div class="field"><label></label><input type="checkbox" name="chkShip" id="chkShip" value="1"> Same as shipping address</span></div>
                <div class="field"><label>Street *</label><input type="text" name="address1" id="address1"><span id="billval" class="validate validatetext"></span></div>
                <div class="field"><label></label><input type="text" name="address2" id="address2"></div>
                <div class="field"><label>City *</label><input type="text" name="city" id="city"></div>
                <div class="field"><label>State/Province *</label><input type="text" name="state" id="state"></div>
                <div class="field"><label>Zip/Postal Code *</label><input type="text" name="zip" id="zip"></div>
                <div class="field"><label>Country *</label><input type="text" name="country" id="country"></div>

                <h3>Billing Information</h3>
                <div class="field"><label>Name on Card *</label><input type="text" name="cc_holder" id="cc_holder"><span id="ccnameval" class="validate validatetext"></span></div>
                <div class="field"><label>Credit Card Number *</label><input type="text" name="cc_num" id="cc_num"><span id="ccval" class="validate validatetext"></span></div>
                <div class="field"><label>Expiration Date *</label><span class="form-item"><select class="small" name="cc_month" id="cc_month">
                    <option value="01">01</option>
                    <option value="02">02</option>
                    <option value="03">03</option>
                    <option value="04">04</option>
                    <option value="05">05</option>
                    <option value="06">06</option>
                    <option value="07">07</option>
                    <option value="08">08</option>
                    <option value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>    
                </select><b class="icon" data-icon="&#xf0d7;"></b></span> / <span class="form-item"><select class="small" name="cc_year" id="cc_year"></select><b class="icon" data-icon="&#xf0d7;"></b></span><span id="ccexpval" class="validate validatetext"></div>
                <div class="field"><label>CCV *</label><input class="small" type="text" name="ccv" id="ccv"><span id="ccccvval" class="validate validatetext"></span></div>
            </div>
            <div class="field"><label></label><input type="checkbox" id="agree2terms"> I have read and agree to the <a href="#" id="view_terms">Terms & Conditions</a> * <span id="tab3_TandC_info" class="validate"></span></div>
            <div id="terms" style="display: none">
                <p><strong>Terms</strong><br />
                If you are not satisfied with your membership, you may cancel within 30 days of purchase and receive a full refund. Otherwise, all membership fees are non-refundable. By clicking on the Complete Payment button below, I authorize Indagare Travel to initiate an electronic debit to my bank account in the amount stated above.</p>
                <p>In order to set up a an Indagare Travel membership you must provide us with valid credit card information. By submitting such credit card information, you give Indagare Travel permission to charge all fees incurred through your account to the credit card you designate on your registration form. All such fees (including renewal fees) will be charged at the time they are incurred. Renewal fees will be incurred upon the commencement of each renewal term.</p>
                <p>Indagare Travel will send an e-mail to the e-mail address in your registration form reminding you that your Indagare Travel membership is about to expire. We will send you this notice at least 60 days before the expiration date of your membership. If you do not contact Indagare Travel, as instructed on your e-mail reminder, indicating that you do not wish to renew, Indagare Travel will automatically renew your membership for the same membership term as your previous Indagare Travel membership and charge your credit card on the first day of your renewal membership term. The renewal charge will be equal to the then-current membership fee for your membership term. By registering, you give permission to Indagare Travel to automatically charge your membership fee to your credit card for each renewal term.</p>
            </div>

            <div class="field"><label></label><input type="Button" name="subRenewal" id="subRenewal" class="button primary" value="Submit"></div>
	    <div class="field"><label></label>* Indicates required field</div>
        </div>
    </div>
    
    <div class="tab" id="tab3">
        <!--<h1>Benefits</h1>-->
<?php

		echo 'benefits list';

?>
    </div>
    
    <div class="tab" id="tab4">
        <!--<h1>Travel Profile</h1>-->
        <form action="/wp-content/themes/indagare/app/lib/account_update.php" method="POST">
        <div class="field"><label>Member Birthday</label><input class="mini" type="text" size="2" name="m_bday_m" id="m_bday_m"> / <input class="mini" type="text" size="2" name="m_bday_d" id="m_bday_d"> / <input class="mini" type="text" size="4" name="m_bday_y" id="m_bday_y"><span id="shipval" class="validate validatetext"></span></div>
        <div class="field"><label>Passport issuing country:</label><input type="text" name="m_pass" id="m_pass"></div>
        <div id="ffa_list"></div>
        <div class="field">
            <label>Airline</label><input type="text" name="m_ff_a" id="m_ffa">
            <label>Frequent Flier #</label><input type="text" name="m_ffn" id="m_ffn">
        </div>
        <div class="field"><label></label><input class="small" id="addFFa" type="Button" value="Add"></div>
        <h3></h3>
        <div class="field"><label>Spouse/Domestic Partner Name</label><input type="text" name="s_name" id="s_name"></div>
        <div class="field"><label>Spouse/Domestic Partner Email</label><input type="text" name="s_email" id="s_email"></div>
        <div class="field"><label>Spouse/Domestic Partner Birthday</label><input class="mini" type="text" name="s_bday_m" size="2" id="s_bday_m"> / <input class="mini" type="text" size="2" name="s_bday_d" id="s_bday_d"> / <input class="mini" type="text" size="4" name="s_bday_y" id="s_bday_y"></div>
        <div class="field"><label>Passport issuing country:</label><input type="text" name="s_pass" id="s_pass"></div>
        <div id="spouse_ffa_list"></div>
        <div class="field">
            <label>Airline</label><input type="text" name="s_ff_a" id="s_ffa">
            <label>Frequent Flier #</label><input type="text" name="s_ffn" id="s_ffn">
        </div>
        <div class="field"><label></label><input class="small" id="addSpouseFFa" type="Button" value="Add"></div>
        <h3></h3>
        <div id="children_list"></div>
        <p>Add a Child</p>
        <div class="field"><label>Child Name</label><input type="text" name="c_name" id="c_name"></div>
        <div class="field"><label>Child Birthday</label><input class="mini" type="text" size="2" name="c_bday_m" id="c_bday_m"> / <input class="mini" type="text" size="2" name="c_bday_d" id="c_bday_d"> / <input class="mini" type="text" size="4" name="c_bday_y" id="c_bday_y"></div>
        <div class="field"><label></label><input class="small" id="addChild" type="Button" value="Add"></div>
        <h3></h3>
        <div class="field"><label>Assistant's Name</label><input type="text" name="a_name" id="a_name"></div>
        <div class="field"><label>Assistant's Email</label><input type="text" name="a_email" id="a_email"></div>
        <div class="field"><label>Assistant's Phone</label><input type="text" name="a_phone" id="a_phone"></div>
        <h3></h3>
        <div class="field">
            How do you prefer to be contacted?<br>
            <input type="radio" value="Email" name="contact_pref" id="contact_pref"> Email 
            <input type="radio" value="Phone" name="contact_pref" id="contact_pref"> Phone
        </div>
        <div class="field">
            How do you prefer to receive itineraries and travel documents?<br>
            <input type="radio" value="Digitally" name="delivery_pref" id="delivery_pref"> Digitally
            <input type="radio" value="HardCopy" name="delivery_pref" id="delivery_pref"> Hard Copy (shipping fees may apply)
        </div>
        <p>Thank you for filling in your travel profile. Indagare's travel specialist will review this information to help better assist with your trip planning.</p>
        <div class="field"><label></label><input type="submit" name="subTab4" id="subTab4" class="button primary" value="Update"></div>
        </form>
    </div>
    
    <div class="tab" id="tab5">
        <!--<h1>Travel Preferences</h1>-->
        <form action="/wp-content/themes/indagare/app/lib/account_user_survey.php" method="POST">
        <div class="field"><label>How often do you travel?</label> <br>
            <input type="radio" value="1" name="count" id="count"> Every school holiday plus a summer trip<br>
            <input type="radio" value="2" name="count" id="count"> One big trip a year<br>
            <input type="radio" value="3" name="count" id="count"> 2-3 weeks per year plus long weekends<br>
            <input type="radio" value="4" name="count" id="count"> 4-5 weeks per year plus long weekends<br>
            <input type="radio" value="5" name="count" id="count"> 6+ weeks a year
        </div>
        
        <div class="field"><label>How would you describe your trip planning style?</label> <br>
            <input type="radio" value="1" name="planning_style" id="planning_style"> Last minute booker (within one month of travel)<br>
            <input type="radio" value="2" name="planning_style" id="planning_style"> Average advance planner (within 1-4 months of travel)<br>
            <input type="radio" value="3" name="planning_style" id="planning_style"> Scheduled traveler (4-8 months)<br>
            <input type="radio" value="4" name="planning_style" id="planning_style"> Early-booker (8-12 months prior)
        </div>
        
        <div class="field"><label>Who do you travel with? Check all that apply.</label> <br>
            <input type="checkbox" name="tw1" id="tw1"> My family (including children) <br>
            <input type="checkbox" name="tw2" id="tw2"> My family (all adults)<br>
            <input type="checkbox" name="tw3" id="tw3"> Couple<br>
            <input type="checkbox" name="tw4" id="tw4"> Friend getaways<br>
            <input type="checkbox" name="tw5" id="tw5"> Multi-generational trips<br>
            <input type="checkbox" name="tw6" id="tw6"> Large-scale destination celebrations<br> 
            <input type="checkbox" name="tw7" id="tw7"> Multi-family trips
        </div>
        
        <div class="field"><label>When choosing a hotel, which features are most important to you? Rank in order.</label> <br>
            <select name="features_1" id="features_1">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Service <br>
            <select name="features_2" id="features_1">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Location <br>
            <select name="features_3" id="features_3">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Reputation “best in town” <br>
            <select name="features_4" id="features_4">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Design style <br>
            <select name="features_5" id="features_5">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Sense of place (reflects local culture) <br>
            <select name="features_6" id="features_6">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Brand name <br>
            <select name="features_7" id="features_7">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Pool <br>
            <select name="features_8" id="features_8">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Gym <br>
            <select name="features_9" id="features_9">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Family-friendly service and amenities <br>
            <select name="features_10" id="features_10">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>Price
        </div>
        
        <div class="field"><label>What style of hotel do you prefer? </label> <br>
            <textarea name="hotel_style" id="hotel_style"></textarea>
        </div>

        <div class="field"><label>What hotel amenities do you value most? </label> <br>
            <textarea name="hotel_amenities" id="hotel_amenities"></textarea>
        </div>

        <div class="field"><label>Please list your beverages of choice. </label> <br>
            <textarea name="beverages" id="beverages"></textarea>
        </div>

        <div class="field"><label>How do you like to travel: </label> <br> 
                
                <input type="radio" value="1" name="itinerary_pref" id="itinerary_pref"> Do you prefer a robust and busy itinerary  <br>
                <input type="radio" value="2" name="itinerary_pref" id="itinerary_pref"> or a more relaxed schedule with a mix of activities and down time?  <br>
                 <br>    
                <input type="radio" value="1" name="itinerary_pref2" id="itinerary_pref2"> Do you like everything planned out for you including things like spa appointments and airport transfers  <br>
                <input type="radio" value="2" name="itinerary_pref2" id="itinerary_pref2">or do you prefer to do those things on your own?  <br>
        </div>        
                
        <div class="field"><label>Please share some of your most fond travel memories and tell us why they were so special.</label> <br>
            <textarea name="memories" id="memories"></textarea>
        </div>

        <div class="field"><label>What are your pet peeves when traveling?</label> <br>
            <textarea name="peeves" id="peeves"></textarea>
        </div>

        <div class="field"><label>Tell us what influences your travel decisions.</label> <br>
            <textarea name="decisions" id="decisions"></textarea>
        </div>

        <div class="field"><label>Is there anything else you would like to share about your travel preferences?</label> <br>
            <textarea name="else" id="else"></textarea>
        </div>

        <div class="field"><label>Please select your interests:</label> <br>
            <input type="checkbox" name="interest1" id="interest1"> Sports<br>
            <input type="checkbox" name="interest2" id="interest2"> Food & Wine<br>
            <input type="checkbox" name="interest3" id="interest3"> Shopping<br>
            <input type="checkbox" name="interest4" id="interest4"> Museums and Galleries<br>
            <input type="checkbox" name="interest5" id="interest5"> History<br>
            <input type="checkbox" name="interest6" id="interest6"> Nature<br>
            <input type="checkbox" name="interest7" id="interest7"> Cooking<br>
            <input type="checkbox" name="interest8" id="interest8"> Hiking<br>
            <input type="checkbox" name="interest9" id="interest9"> Live music<br>
            <input type="checkbox" name="interest10" id="interest10"> Performing Arts<br>
            <input type="checkbox" name="interest11" id="interest11"> Other
        </div>
        
        <h2>Airline Seating and Meal Preference</h2>
        <h3>Short Haul</h3>
        <div class="field"><label>Class of service: </label>
            <input type="radio" value="1" name="sh_class" id="sh_class">economy, 
            <input type="radio" value="2" name="sh_class" id="sh_class">premium economy, 
            <input type="radio" value="3" name="sh_class" id="sh_class">business, 
            <input type="radio" value="4" name="sh_class" id="sh_class">first
        </div>
        <div class="field"><label>Seat preference: </label>
            <input type="radio" value="1" name="sh_seat" id="sh_seat">front,
            <input type="radio" value="2" name="sh_seat" id="sh_seat">back,
            <input type="radio" value="3" name="sh_seat" id="sh_seat">right,
            <input type="radio" value="4" name="sh_seat" id="sh_seat">left
        </div>
        <div class="field"><label>Location: </label>
            <input type="radio" value="1" name="sh_location" id="sh_location">window,
            <input type="radio" value="2" name="sh_location" id="sh_location">aisle,
            <input type="radio" value="3" name="sh_location" id="sh_location">middle
        </div>
        <h3>Long Haul</h3>
        <div class="field"><label>Class of service: </label>
            <input type="radio" value="1" name="lh_class" id="lh_class">economy, 
            <input type="radio" value="2" name="lh_class" id="lh_class">premium economy, 
            <input type="radio" value="3" name="lh_class" id="lh_class">business, 
            <input type="radio" value="4" name="lh_class" id="lh_class">first
        </div>
        <div class="field"><label>Seat preference: </label>
            <input type="radio" value="1" name="lh_seat" id="lh_seat">front,
            <input type="radio" value="2" name="lh_seat" id="lh_seat">back,
            <input type="radio" value="3" name="lh_seat" id="lh_seat">right,
            <input type="radio" value="4" name="lh_seat" id="lh_seat">left
        </div>
        <div class="field"><label>Location: </label>
            <input type="radio" value="1" name="lh_location" id="lh_location">window,
            <input type="radio" value="2" name="lh_location" id="lh_location">aisle,
            <input type="radio" value="3" name="lh_location" id="lh_location">middle
        </div>
        
        <div>
            Allergies/Food Restrictions<br>
            <textarea name="allergies" id="allergies"></textarea>
        </div>
        <div class="field"><label></label><input type="submit" name="subTab1" id="subTab1" class="button primary" value="Update"></div>
        </form>
    </div>
</div>
    
<script type="text/javascript" src="/wp-content/themes/indagare/app/js/account.js"></script>
<?php ?>