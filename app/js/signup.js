(function () {
    function init(){
    	signup.createTabber();
        signup.initFields();
        signup.buildMembershipDD();
        if (trialCode != 'false') {
            document.getElementById("tgCode").value = trialCode;
            signup.tgCodeLookup();
        }
        if (!showTrial) {
            document.getElementById("trialStuff").style.setProperty("display", "none");
            //signup.tgCodeLookup();
        }
        else {
            //document.getElementById("memberLevel").style.setProperty("display", "none"); 
            jQuery("#memberLevel").hide();
        }
        signup.buildButtonEventMgrs();
        signup.buildCCYearDD();

        jQuery('.customselectdyn').customSelect();
        jQuery('.customselectdyn').wrap('<span class="customSelectWrap"></span>');
                        jQuery('#membership_level').css("height", "25px");
                        jQuery('#membership_years').css("height", "25px");
                        jQuery('#cc_month').css("height", "25px");
                        jQuery('#cc_year').css("height", "25px");
                        jQuery('#user_prefix').css("height", "25px");
        

    }   
    if (window.addEventListener) {
        window.addEventListener('DOMContentLoaded', init, false);
    } else {
        window.attachEvent('onload', init);
    }
} ());

var signup = {
    createTabber: function() {
		this.tabber1 = new Yetii({
			id: 'tab-container',
			class: 'tab'
		});
	},	
    usrNameChk: false,
    isTrial: false,
    trialType: 0,
    initFields: function() {
       document.getElementById("ln").value = acc.lastname;
       document.getElementById("initial").value = acc.middle_initial;
       document.getElementById("fn").value = acc.firstname;
       document.getElementById("email").value = acc.email;
       document.getElementById("refCode").value = rc;
       if (redirect=="swifttrip") {	   
            jQuery("#lightbox-signup-complete").append('<input id="backtohotel" type="submit" value="Back to Hotel" class="button">');
       }
    },
    buildButtonEventMgrs: function () {
        document.getElementById("tgGiftLookup").onclick = function () {
            //console.log("button");
            signup.tgCodeLookup();
        };
        document.getElementById("subTab1").onclick = function () {
            signup.validateTab1();
        };
        document.getElementById("subTab21").onclick = function () {
            signup.subTab21();
        };
        /*document.getElementById("subTab22").onclick = function () {
            signup.subTab22();
        };*/
        document.getElementById("subTab3").onclick = function () {
            signup.validateTab3();
        };
        document.getElementById("view_terms").onclick = function (e) {
            e.preventDefault();
            jQuery('#terms').show();
        };
        document.getElementById("username").onchange = signup.checkUsername;
        document.getElementById("chkShip").onchange = signup.setAddr;
    },  
    buildMembershipDD: function () {
        var mb_select = document.getElementById("membership_level");
        var option;
       for (var m in mbs) {
           option = document.createElement("option");
           option.text = mbs[m].name;
           option.value = m;
           if (mbs[m].level == mb) {
               option.selected = 'selected';
           }
           try {
               mb_select.add(option);
           }
           catch (e) {
               mb_select.add(option, null);
           }
       }
       //jQuery('#membership_years').customSelect();
       this.setSelectedMBYears(mb_select.options[mb_select.selectedIndex].value);  
       mb_select.onchange = function () {
           signup.setSelectedMBYears(mb_select.options[mb_select.selectedIndex].value);
       };
    },  
    buildCCYearDD: function () {
        var cc_year = document.getElementById("cc_year");
        var y = new Date().getFullYear(); 
        var option;
        for (var i = y; i <= (y + 10); i++){
           option = document.createElement("option");
           option.text = i;
           option.value = i - 2000;
           try {
               cc_year.add(option);
           }
           catch (e) {
               cc_year.add(option, null);
           }
        }
    },
    setSelectedMBYears: function (m) {
        
        var mby_select = document.getElementById("membership_years");
        mby_select.options.length = 0;
        var option1 = document.createElement("option");
        if ( dc == 0 ) {
	        option1.text = "$" + (mbs[m].p1/100) + ".00 for 1 year";
        } else {
	        option1.text = "$" + (mbs[m].p1/100) + ".00 for 1 year (" + dc + "% discount applied)";
        }
        option1.value = "1";

		// display all years with no discount code
		if ( dc == 0 ) {
			var option2 = document.createElement("option");
			option2.text = "$" + (mbs[m].p2/100) + ".00 for 2 years";
			option2.value = "2";
			var option3 = document.createElement("option");
			option3.text = "$" + (mbs[m].p3/100) + ".00 for 3 years";
			option3.value = "3"; 
		}
		
        try { 
            mby_select.add(option3, mby_select.options[null]);
            mby_select.add(option2, mby_select.options[0]);
            mby_select.add(option1, mby_select.options[0]);
        }
        catch (e) {
			// display all years with no discount code
			if ( dc == 0 ) {
				mby_select.add(option3, null);
				mby_select.add(option2, option3);
            }
            mby_select.add(option1, option2);
        }
        jQuery('#membership_years').trigger("render");
        //jQuery('#membership_years').trigger('update');
    },
    validateTab1: function () {
        var msg;
        var complete = true;

		jQuery('.validate').text('');

        if (document.getElementById("fn").value == '') {
            msg = "Please enter a first name.";
            jQuery('#fnval').text(msg);
            complete = false;
        } 
        if (document.getElementById("ln").value == '') {
           msg = "Please enter a last name.";
            jQuery('#lnval').text(msg);
            complete = false;
        }
        if (!document.getElementById("email").value.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/)) {
            msg = "Please enter a valid email address.";
            jQuery('#emailval').text(msg);
            complete = false;
        }
        if (document.getElementById("phone").value == '') {
            msg = "Please enter a phone number.";
            jQuery('#phoneval').text(msg);
            complete = false;
        }
        if (document.getElementById("s_address1").value == '' ||
            document.getElementById("s_city").value == '' ||
            document.getElementById("s_zip").value == '' ||
            document.getElementById("s_country").value == ''
        ){
            msg = "Please enter a complete shipping address";
			jQuery('#shipval').text(msg);
            complete = false;    
        }
        if (!complete) {
//            document.getElementById("messages").innerHTML = msg;
        }
        else {
            this.sendTab1();
            if (rc != "" || this.isTrial) {
                this.subTab21();
            }
            else {
                this.tabber1.show(2);
            }
        }
    },
    validateTab3: function () {
        var cc_month = document.getElementById("cc_month");
        var cc_year = document.getElementById("cc_year");
        var msg = "";
        var complete = true;
        
        jQuery('.validatetext').text('');
        
        
        if (document.getElementById("agree2terms").checked == false) {
            msg = "You have to agree to out Terms & Conditions.";
			jQuery('#tab3_TandC_info').text(msg);
            complete = false;
        }
        if (document.getElementById("username").value == '') {
            msg = "Please enter a username.";
			jQuery('#tab3_username_info').text(msg);
            complete = false;
        }
        if (!signup.usrNameChk) {
            msg = "Please enter a different username.";
            complete = false;
        } else {
            jQuery('#tab3_username_info').text('');
        }
        if (document.getElementById("password1").value == '') {
            msg = "Please enter a password.";
            jQuery('#passval').text(msg);
            complete = false;
        }
        if (document.getElementById("password1").value != document.getElementById("password2").value) {
            msg = "Passwords do not match.";
            jQuery('#passval').text(msg);
            complete = false;
        }
        if (document.getElementById("s_address1_2").value == '' ||
            document.getElementById("s_city_2").value == '' ||
            document.getElementById("s_zip_2").value == '' ||
            document.getElementById("s_country_2").value == ''
        ){
            msg = "Please enter a complete shipping address";
            jQuery('#shipval').text(msg);
            complete = false;    
        }
        if (!this.isTrial) {
            if (document.getElementById("address1").value == '' ||
                document.getElementById("city").value == '' ||
                document.getElementById("zip").value == '' ||
                document.getElementById("country").value == ''
            ){
                msg = "Please enter a complete billing address";
                jQuery('#billval').text(msg);
                complete = false;    
            }
            if (document.getElementById("cc_holder").value == ''){
                msg = "Please enter the name on your credit card.";
                            jQuery('#ccnameval').text(msg);
                complete = false;    
            }
            if (document.getElementById("cc_num").value == ''){
                msg = "Please enter the number on your credit card.";
                            jQuery('#ccval').text(msg);
                complete = false;    
            }
            if (document.getElementById("ccv").value == ''){
                msg = "Please enter the security code on your credit card.";
                            jQuery('#ccccvval').text(msg);
                complete = false;    
            }
            if (!this.checkCCNumber(document.getElementById("cc_num").value)) {
                msg = "Please enter a valid credit card number.";
                            jQuery('#ccval').text(msg);
                complete = false;   
            }
            if (!this.checkCCDate(cc_month.options[cc_month.selectedIndex].value,
                        cc_year.options[cc_year.selectedIndex].value)){
                msg = "Please enter a valid credit card expiration date.";
                            jQuery('#ccexpval').text(msg);
                complete = false;   
            }
            if (!this.checkCCDate(cc_month.options[cc_month.selectedIndex].value,
                        cc_year.options[cc_year.selectedIndex].value)){
                msg = "Please enter a valid credit card expiration date.";
                            jQuery('#ccexpval').text(msg);
                complete = false;   
            }
        }
//        document.getElementById("messages").innerHTML = msg;

        if (complete) {
            if (this.isTrial) {
                this.processTrial();
            }
            else {
                this.processPay();
            }
        }
    },
    processing: false,
    processTrial: function () {
        if (!this.processing){
            this.processing = true;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    if (xmlhttp.responseText = "true") {
                        var mb_select = document.getElementById("membership_level");
                        var mby_select = document.getElementById("membership_years");
                        var date = new Date();
                        document.getElementById("memberdate").innerHTML = (date.getMonth() + 1) + '/' + date.getDate() + '/' +  date.getFullYear();
                        document.getElementById("membercost").innerHTML = "";
                        document.getElementById("memberlength").innerHTML = mby_select.options[mby_select.selectedIndex].text;
                        document.getElementById("membercardholder").innerHTML = "";
                        document.getElementById("membercard").innerHTML = "";
                        document.getElementById("membertransaction").innerHTML = "";
                        // wire up redirect event
                        document.getElementById("membercomplete").onclick = function () {
                            window.location = "/welcome/";
                        };

                        jQuery.magnificPopup.open({
                              items: {
                                    type: 'inline',
                                    src: '#lightbox-signup-complete', // can be a HTML string, jQuery object, or CSS selector
                                    midClick: true
                              },
                        });
                    }
                    else {

                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
                                src: '#lightbox-signup-error', // can be a HTML string, jQuery object, or CSS selector
                                midClick: true
                            },
                        });
                    }
                    signup.processing = false;
                }
            };
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=newTrial",true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            var posts = "username=" + encodeURI(document.getElementById("username").value) + "&password=" + 
                    encodeURI(document.getElementById("password1").value) + "&s_address1=" + 
                    encodeURI(document.getElementById("s_address1_2").value) + "&s_address2=" + 
                    encodeURI(document.getElementById("s_address2_2").value) + "&s_city=" +
                    encodeURI(document.getElementById("s_city_2").value) + "&s_state=" +
                    encodeURI(document.getElementById("s_state_2").value) + "&s_zip=" +
                    encodeURI(document.getElementById("s_zip_2").value) + "&s_country=" +
                    encodeURI(document.getElementById("s_country_2").value) + "&passKey=" + 
                    encodeURI(document.getElementById("refCode").value) + "&top_destinations=" + 
                    encodeURI(document.getElementById("top_destinations").value) +
                    "&fav_hotels=" + 
                    encodeURI(document.getElementById("fav_hotels").value) +
                    "&reason_travel=" +
                    encodeURI(document.getElementById("reason_travel").value) +
                    "&next_destination=" +
                    encodeURI(document.getElementById("next_destination").value);;

            xmlhttp.send(posts);
        }
    },    
    processPay: function () {
        if (!this.processing){
            this.processing = true;
            var cc_month = document.getElementById("cc_month");
            var cc_year = document.getElementById("cc_year");
            var otherparam=["pc","gdsType","cin","cout"];
            var addparam="";
            var otherempty=false;
            for (var i=0;i<otherparam.length;i++)
            {

              if (swifttriparm[otherparam[i]]!=undefined)
              {
                //console.log(swifttriparm[otherparam[i]]);
                addparam += otherparam[i] + "=" + swifttriparm[otherparam[i]] + "&";
              }
              else
              {
            	  
                addparam += otherparam[i] + "=&";	  
                otherempty=true;   
              } 	  
            }
            //console.log(addparam);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    var result = xmlhttp.responseText.split("-");
                    //console.log(result);
                    //result[0]="APPROVED";
                    //console.log(result[result.length-1]);
                    if(result[0] == "APPROVED") {
                        
                        var date = new Date();
                        document.getElementById("memberdate").innerHTML = (date.getMonth() + 1) + '/' + date.getDate() + '/' +  date.getFullYear();
                        document.getElementById("membercardholder").innerHTML = document.getElementById("cc_holder").value;
                        var c = document.getElementById("cc_num").value;
                        document.getElementById("membercard").innerHTML = c.substr(c.length - 4);
                        document.getElementById("membertransaction").innerHTML = result[1];
                        // wire up redirect event                        
                        if (redirect=="swifttrip")
                        {
                        	document.getElementById("membercomplete").onclick = function () {
                                window.location = "/welcome/";
                            };
                        	
                        	if (otherempty==false)
                           {
                        		document.getElementById("backtohotel").onclick = function () {
                        			var ssodata=result[result.length-1].split(":");
                        			if  (ssodata[0]=="sso")
                        			{
                        				var redirections="https://book.indagare.com/do/hotel/CheckHotelAvailability?"+addparam+"ssoToken="+ssodata[1]; 	
                        			}
                        			else
                        			{
                        				var redirections="https://book.indagare.com/do/hotel/CheckHotelAvailability?"+addparam+"ssoToken=";
                        			}
                        			//console.log(redirections);
                        			window.location = redirections;
                                };	
                           }
                           else
                           {
                        	   var ssodata=result[result.length-1].split(":");
                        	   document.getElementById("backtohotel").onclick = function () {
                        		   var redirections="https://book.indagare.com/do/hotel/CheckHotelAvailability?"+addparam+"ssoToken="+ssodata[1];
                        		   window.location = redirections;
                               }; 	
                           }	
                        }
                        else
                        {
                        	document.getElementById("membercomplete").onclick = function () {
                                window.location = "/welcome/";
                            };
                        }	
                        

                        jQuery.magnificPopup.open({
                              items: {
                                    type: 'inline',
                                    src: '#lightbox-signup-complete', // can be a HTML string, jQuery object, or CSS selector
                                    midClick: true,
                                    
                              },
                              showCloseBtn:false,
                        });
                    }
                    else {
                    	
                        jQuery.magnificPopup.open({
                            items: {
                                type: 'inline',
                                src: '#lightbox-signup-error', // can be a HTML string, jQuery object, or CSS selector
                                midClick: true,
                                
                            },
                            //showCloseBtn:false,
                        });
                    }
                signup.processing = false;
                }
            };
            //alert(source);
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=payment",true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            var mb_select = document.getElementById("membership_level");
            var mby_select = document.getElementById("membership_years");
            var posts = "prefix="+user_prefix.options[user_prefix.selectedIndex].value+"&fn=" + encodeURI(document.getElementById("fn").value) + "&ln=" + 
                encodeURI(document.getElementById("ln").value) + "&minitial=" + 
                encodeURI(document.getElementById("initial").value) + "&email=" + 
                encodeURI(document.getElementById("email").value) + "&l=" +
                mb_select.options[mb_select.selectedIndex].value + "&y=" +
                mby_select.options[mby_select.selectedIndex].value + "&tgCode=" +
                document.getElementById("tgCode").value + "&phone=" +
                encodeURI(document.getElementById("phone").value) + "&username=" + encodeURI(document.getElementById("username").value) + "&password=" + 
                    encodeURI(document.getElementById("password1").value) + "&s_address1=" + 
                    encodeURI(document.getElementById("s_address1_2").value) + "&s_address2=" + 
                    encodeURI(document.getElementById("s_address2_2").value) + "&s_city=" +
                    encodeURI(document.getElementById("s_city_2").value) + "&s_state=" +
                    encodeURI(document.getElementById("s_state_2").value) + "&s_zip=" +
                    encodeURI(document.getElementById("s_zip_2").value) + "&s_country=" +
                    encodeURI(document.getElementById("s_country_2").value) + "&address1=" + 
                    encodeURI(document.getElementById("address1").value) + "&address2=" + 
                    encodeURI(document.getElementById("address2").value) + "&city=" +
                    encodeURI(document.getElementById("city").value) + "&state=" +
                    encodeURI(document.getElementById("state").value) + "&zip=" +
                    encodeURI(document.getElementById("zip").value) + "&country=" +
                    encodeURI(document.getElementById("country").value) + "&passKey=" + 
                    encodeURI(document.getElementById("refCode").value) + "&cc_holder=" +
                    encodeURI(document.getElementById("cc_holder").value) + "&cc_num=" +
                    encodeURI(document.getElementById("cc_num").value) + "&ccv=" +
                    encodeURI(document.getElementById("ccv").value) + "&cc_m=" +
                    encodeURI(cc_month.options[cc_month.selectedIndex].value) + "&cc_y=" +
                    encodeURI(cc_year.options[cc_year.selectedIndex].value) + "&dc=" +
                    dc + "&top_destinations=" + 
                    encodeURI(document.getElementById("top_destinations").value) +
                    "&fav_hotels=" + 
                    encodeURI(document.getElementById("fav_hotels").value) +
                    "&reason_travel=" +
                    encodeURI(document.getElementById("reason_travel").value) +
                    "&next_destination=" +
                    encodeURI(document.getElementById("next_destination").value);

            xmlhttp.send(posts);
        
        }	
        
    },
    sendTab1: function () {
        acc.firstname = document.getElementById("fn").value;
        acc.lastname = document.getElementById("ln").value;
        acc.middle_initial = document.getElementById("initial").value;
        acc.email = document.getElementById("email").value;
        var prefix= document.getElementById("user_prefix");
        var mb_select = document.getElementById("membership_level");
        var mby_select = document.getElementById("membership_years");

        document.getElementById("tab3_member_level").innerHTML = mb_select.options[mb_select.selectedIndex].text;
        document.getElementById("tab3_amount").innerHTML = mby_select.options[mby_select.selectedIndex].text;
        acc.level = mb_select.options[mb_select.selectedIndex].value;
        acc.years = mby_select.options[mby_select.selectedIndex].value;
        acc.prefix = prefix.options[prefix.selectedIndex].value;
        
        document.getElementById("memberlevel").innerHTML = mb_select.options[mb_select.selectedIndex].text;
        document.getElementById("membercost").innerHTML = mby_select.options[mby_select.selectedIndex].text;
        document.getElementById("memberlength").innerHTML = mby_select.options[mby_select.selectedIndex].value + "/years";

        document.getElementById("s_address1_2").value = document.getElementById("s_address1").value;
        document.getElementById("s_address2_2").value = document.getElementById("s_address2").value;
        document.getElementById("s_city_2").value = document.getElementById("s_city").value;
        document.getElementById("s_state_2").value = document.getElementById("s_state").value;
        document.getElementById("s_zip_2").value = document.getElementById("s_zip").value;
        document.getElementById("s_country_2").value = document.getElementById("s_country").value;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){};
        xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=signup1",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        var posts = "prefix="+prefix.options[prefix.selectedIndex].value+"&fn=" + encodeURI(document.getElementById("fn").value) + "&ln=" + 
                encodeURI(document.getElementById("ln").value) + "&minitial=" + 
                encodeURI(document.getElementById("initial").value) + "&email=" + 
                encodeURI(document.getElementById("email").value) + "&l=" +
                mb_select.options[mb_select.selectedIndex].value + "&y=" +
                mby_select.options[mby_select.selectedIndex].value + "&tgCode=" +
                document.getElementById("tgCode").value + "&s_address1=" + 
                encodeURI(document.getElementById("s_address1").value) + "&s_address2=" + 
                encodeURI(document.getElementById("s_address2").value) + "&s_city=" +
                encodeURI(document.getElementById("s_city").value) + "&s_state=" +
                encodeURI(document.getElementById("s_state").value) + "&s_zip=" +
                encodeURI(document.getElementById("s_zip").value) + "&s_country=" +
                encodeURI(document.getElementById("s_country").value) + "&phone=" +
                encodeURI(document.getElementById("phone").value);
        xmlhttp.send(posts);
    },
    subTab21: function () {
        /*var msg;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){};
        xmlhttp.open("GET","/wp-content/themes/indagare/app/lib/iajax.php?task=signup21&rc=" + document.getElementById("refCode").value,false);
        xmlhttp.send();
        if (xmlhttp.responseText == "true") {*/
            this.tabber1.show(3);
            jQuery('#codeval').text('');
            if (this.isTrial){
                jQuery('#billingBlock').hide();
            }
        /*}
        else {
            msg = "This is not a valid code.";
//            document.getElementById("messages").innerHTML = msg;
			  jQuery('#codeval').text(msg);
        }  */
    },
    tgCodeLookup : function () {
      //console.log("tgLookup");
      //document.getElementById("memberLevel").style.setProperty("display", "block"); 
      jQuery("#memberLevel").show();
        var msg;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){};
        xmlhttp.open("GET","/wp-content/themes/indagare/app/lib/iajax.php?task=chkTrialKey&rc=" + document.getElementById("tgCode").value,false);
        xmlhttp.send();
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            //console.log(xmlhttp.responseText);
            var result = xmlhttp.responseText.split("|");
            if (result[0] == "true") {
                this.isTrial = true;
                document.getElementById("refCode").value = document.getElementById("tgCode").value;
                jQuery('#tg_codeval').text('');
                var mb_select = document.getElementById("membership_level");
                var mby_select = document.getElementById("membership_years");
                if (result[1] == 1) {
                   mb_select.selectedIndex = 0;
                   
                   var option = document.createElement("option");
                    option.text = "1 Year/complimentary";
                    option.value = "trial";
                    option.selected = 'selected';
                    try {
                       document.getElementById("membership_level").add(option);
                    }
                    catch (e) {
                        document.getElementById("membership_level").add(option, null);
                    } 
                   this.trialType = 1;
                }
                else {   
                    var option = document.createElement("option");
                    option.text = "Indagare Trial Membership";
                    option.value = "trial";
                    option.selected = 'selected';
                    try {
                        document.getElementById("membership_level").add(option);
                    }
                    catch (e) {
                        //console.log(e);
                        document.getElementById("membership_level").add(option, null);
                    } 
                    var option = document.createElement("option");
                    option.text = "30 Days";
                    option.value = "trial";
                    option.selected = 'true';
                    try {
                        document.getElementById("membership_years").add(option);
                    }
                    catch (e) {
                        //console.log(e);
                        document.getElementById("membership_years").add(option, null);
                    }   
                }
                jQuery('#membership_level').trigger("render");//.customSelect();
                jQuery('#membership_years').trigger("render");//.customSelect();
                document.getElementById("membership_level").setAttribute("disabled", "true");
                document.getElementById("membership_years").setAttribute("disabled", "true");
                document.getElementById("tg_codeval").innerHTML = "<span class=\"validated\">Code accepted.</span>";
            }
            else {
                document.getElementById("tg_codeval").innerHTML = "<span class=\"validate\">This is not a valid code. You may proceed and purchase a regular membership to Indagare, but you will be charged the annual fee.</span>";
            }  
        }
    },
    subTab22: function () {
        var msg;
        var complete = true;
/*
        if (document.getElementById("top_destinations").value == '' || 
            document.getElementById("fav_hotels").value == '' ||
            document.getElementById("reason_travel").value == '' ||
            document.getElementById("next_destination").value == '') {
            msg = "Please answer all questions.";
            complete = false;
        }
 */

		jQuery('.validatetext').text('');

        if (document.getElementById("top_destinations").value == '') {
            msg = "Please enter your destinations.";
			jQuery('#destval').text(msg);
            complete = false;
        }

        if (document.getElementById("fav_hotels").value == '') {
            msg = "Please list your favorite hotels.";
			jQuery('#hotelval').text(msg);
            complete = false;
        }

        if (document.getElementById("reason_travel").value == '') {
            msg = "Please tell us your travel reasons.";
			jQuery('#reasonval').text(msg);
            complete = false;
        }

        if (document.getElementById("next_destination").value == '') {
            msg = "Please let us know your next destination.";
			jQuery('#nextval').text(msg);
            complete = false;
        }

        if (!complete) {
//            document.getElementById("messages").innerHTML = msg;
			  
        }
        else {
            //console.log("send email");
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange=function(){};
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=signup22",true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            var posts = "top_destinations=" + 
                    encodeURI(document.getElementById("top_destinations").value) +
                    "&fav_hotels=" + 
                    encodeURI(document.getElementById("fav_hotels").value) +
                    "&reason_travel=" +
                    encodeURI(document.getElementById("reason_travel").value) +
                    "&next_destination=" +
                    encodeURI(document.getElementById("next_destination").value);
            xmlhttp.send(posts);
            
			/*jQuery.magnificPopup.open({
			  items: {
				type: 'inline',
				src: '#lightbox-signup-application', // can be a HTML string, jQuery object, or CSS selector
				midClick: true
			  },
			});*/

            this.tabber1.show(3);
        }
    },
    checkUsername: function () {
	
		//console.log('check username');
	
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            //console.log(xmlhttp.responseText);
            if (xmlhttp.responseText == "true") {
                document.getElementById("tab3_username_info").innerHTML = "Username already exists.";
                signup.usrNameChk = false;
            }
            else {
                document.getElementById("tab3_username_info").innerHTML = "<span class=\"validated\">Username accepted.</span>";
                signup.usrNameChk = true;
            }
        };
        xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=chkLogin",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("login=" + encodeURI(document.getElementById("username").value));
    },
    setAddr: function() {
        var chkbox = document.getElementById("chkShip");
        if (chkbox.checked) {
            document.getElementById("address1").value = document.getElementById("s_address1_2").value;
            document.getElementById("address1").readOnly = true;
            document.getElementById("address2").value = document.getElementById("s_address2_2").value;
            document.getElementById("address2").readOnly = true;
            document.getElementById("city").value = document.getElementById("s_city_2").value;
            document.getElementById("city").readOnly = true;
            document.getElementById("state").value = document.getElementById("s_state_2").value;
            document.getElementById("state").readOnly = true;
            document.getElementById("zip").value = document.getElementById("s_zip_2").value;
            document.getElementById("zip").readOnly = true;
            document.getElementById("country").value = document.getElementById("s_country_2").value;
            document.getElementById("country").readOnly = true;
            //console.log("chk");
        }
        else {
            document.getElementById("address1").readOnly = false;
            document.getElementById("address2").readOnly = false;
            document.getElementById("city").readOnly = false;
            document.getElementById("state").readOnly = false;
            document.getElementById("zip").readOnly = false;
            document.getElementById("country").readOnly = false;
        }
    },
    checkCCNumber: function (num) {
        //console.log(num);
        var amex = /^(?:3[47][0-9]{13})$/; 
        var visa = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
        var master = /^(?:5[1-5][0-9]{14})$/; 
        var discover = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/; 
        var diners = /^(?:3(?:0[0-5]|[68][0-9])[0-9]{11})$/; 
        var jbc = /^(?:(?:2131|1800|35\d{3})\d{11})$/;  
        /*console.log(num.match(amex),
            num.match(visa),
            num.match(master),
            num.match(discover),
            num.match(diners),
            num.match(jbc));*/
        
        if (num.match(amex) ||
            num.match(visa) ||
            num.match(master) ||
            num.match(discover) ||
            num.match(diners) ||
            num.match(jbc)
        ){
            return true;
        }   
        return false;   
    },
    checkCCDate: function (month, year) {
        var d = new Date();
//        var y = d.getFullYear();
        var y = d.getFullYear().toString().substr(2,2);
        var m = d.getMonth();
        
        if ( year == y && (Number(m) > Number(month)) ) {
            return false;
        }
        return true;
    }  
};