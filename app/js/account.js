(function () {
    function init(){

        account.createTabber();
        account.initFields();
        account.buildButtonEventMgrs();
        window.setInterval("autosave()",300000);
        getElementsByClassName=function(node,classname)
        {
            if(node.getElementsByClassName){
                 return node.getElementsByClassName(classname);
            }
            else
            {
                var results = [];
                var elems = node.getElementsByTagName("*");
                for(var i=0;i<elems.length;i++){
                    if(elems[i].className.indexOf(classname)!=-1){
                        results[results.length]=elems[i];
                    }
                }
            return results;
            }
        };
        radiochecked=function(obj)
        {
        	for(i=0;i<obj.length;i++)
        	{
        	 if(obj[i].checked){
              if (obj[i].value==undefined)
              {
            	var empty="";
            	return empty;
              }
              else
              {	  
        	    return obj[i].value;
              }
        	 }
        	}	
        };
        checkjson=function(checkjson)
        {
         try 
         {
           JSON.parse(checkjson);
         } 
         catch (e) 
         {
            return false;
         }
         return true;       		
        }
        autosave=function()
        {
         //console.log("i just start on it");
         if (getElementsByClassName(document.getElementById("tab-container"),"active")[0].id=="aTab4")
         {
          // console.log("I'm in tab4");
           var postfields=new Array();
           postfields["m_bday_m"]=document.getElementById("m_bday_m").value;
           postfields["m_bday_d"]=document.getElementById("m_bday_d").value;
           postfields["m_bday_y"]=document.getElementById("m_bday_y").value;
           postfields["m_pass"]=document.getElementById("m_pass").value;
           postfields["remFFA"]=document.getElementById("remFFA").value;
           postfields["remSFFA"]=document.getElementById("remSFFA").value;
           postfields["remCFFA"]=document.getElementById("remCFFA").value;
           postfields["remChild"]=document.getElementById("remChild").value;
           for (var i=0;i<account.ffAccounts.length;i++)
           {
        	   postfields["m_ffa"+i]=document.getElementById("m_ffa"+i).value;
        	   postfields["m_ffn"+i]=document.getElementById("m_ffn"+i).value;
        	   postfields["m_ffaId"+i]=document.getElementsByName("m_ffaId"+i)[0].value;
           };	   
           postfields["s_name"]=document.getElementById("s_name").value;
           postfields["s_email"]=document.getElementById("s_email").value;
           postfields["s_id"]=document.getElementById("s_id").value;
           postfields["s_bday_m"]=document.getElementById("s_bday_m").value;
           postfields["s_bday_d"]=document.getElementById("s_bday_d").value;
           postfields["s_bday_y"]=document.getElementById("s_bday_y").value;
           postfields["s_pass"]=document.getElementById("s_pass").value;
           for (var j=0;j<account.spouseFFAccounts.length;j++)
           {        	   
        	   postfields["s_ffa"+j]=document.getElementById("s_ffa"+j).value;
        	   postfields["s_ffn"+j]=document.getElementById("s_ffn"+j).value;
        	   postfields["s_ffaId"+j]=document.getElementsByName("s_ffaId"+j)[0].value;
           };
           for (var k=0;k<account.children.length;k++)
           {
        	   postfields["c"+k+"_id"]=document.getElementsByName("c"+k+"_id")[0].value;
        	   postfields["c"+k+"_bday_m"]=document.getElementById("c"+k+"_bday_m").value;
        	   postfields["c"+k+"_bday_d"]=document.getElementById("c"+k+"_bday_d").value;
        	   postfields["c"+k+"_bday_y"]=document.getElementById("c"+k+"_bday_y").value;
        	   postfields["c"+k+"_name"]=document.getElementById("c"+k+"_name").value;
        	   for (var n=0;n<account.children[k].ffa.length;n++)
        	   {
        	     postfields["c_ffaId"+k+"_"+n]=document.getElementsByName("c_ffaId"+k+"_"+n)[0].value;
        	     postfields["c_ffa"+k+"_"+n]=document.getElementById("c_ffa"+k+"_"+n).value;
        	     postfields["c_ffn"+k+"_"+n]=document.getElementById("c_ffn"+k+"_"+n).value;
        	   }        	  
           };                                 
           postfields["a_name"]=document.getElementById("a_name").value;
           postfields["a_email"]=document.getElementById("a_email").value;
           postfields["a_phone"]=document.getElementById("a_phone").value;
           var contact_pref=document.getElementsByName("contact_pref");
           if (radiochecked(contact_pref)!=undefined)
           {
        	 postfields["contact_pref"]=radiochecked(contact_pref);    
           }	   
           else
           {
        	 postfields["contact_pref"]="";      
           }	   
           var delivery_pref=document.getElementsByName("delivery_pref");
           if (radiochecked(delivery_pref)!=undefined)
           {
        	 postfields["delivery_pref"]=radiochecked(delivery_pref);  
           }
           else
           {
        	   postfields["delivery_pref"]="";  
           }	   
           
           var posts="";
           for(var key in postfields)
           {
        	 posts+=key+"="+encodeURI(postfields[key])+"&";     
           };
           posts=posts.substring(0,posts.length-1);
           //console.log(posts);
           //console.log(postfields);
           var xmlhttp = new XMLHttpRequest();
           xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/uajax.php?task=upaccount",true);                      
           xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
           xmlhttp.onreadystatechange=function()
           {
                if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                {
                	if (checkjson(xmlhttp.responseText))
                	{
                	 	var result=JSON.parse(xmlhttp.responseText);
                		if (result.result=="true")
                		{
                		 //console.log('tab4 had been updated successfully!');	
                		}
                		else
                		{
                		 //console.log("tab4 couldn't been updated now!");	
                		}	
                		
                	}
                	else
                	{
                	  	//console.log("Error:"+xmlhttp.responseText);
                	}	                	                                       
                }
            };
           xmlhttp.send(posts);
         }
         else if (getElementsByClassName(document.getElementById("tab-container"),"active")[0].id=="aTab5")
         {
           //console.log("I'm in tab5");
           var postfields=new Array();
           var counts=document.getElementsByName("count");
           postfields["count"]=radiochecked(counts);
           var planning_styles=document.getElementsByName("planning_style");
           postfields["planning_style"]=radiochecked(planning_styles);           
           for(var i=1;i<=7;i++)
           {
        	 if (document.getElementById("tw"+i).checked==true)
        	 {
        		 postfields["tw"+i]=document.getElementById("tw"+i).value; 
        	 }	 
           }
           for(var j=1;j<=10;j++)
           {
        	 postfields["features_"+j]=document.getElementById("features_"+j).options[document.getElementById("features_"+j).selectedIndex].value;    
           }
           postfields["hotel_style"]=document.getElementById("hotel_style").value;
           postfields["hotel_amenities"]=document.getElementById("hotel_amenities").value;
           postfields["beverages"]=document.getElementById("beverages").value;
           postfields["allergies"]=document.getElementById("allergies").value;
           var itinerary_pref=document.getElementsByName("itinerary_pref");
           postfields["itinerary_pref"]=radiochecked(itinerary_pref);
           var itinerary_pref=document.getElementsByName("itinerary_pref");
           postfields["itinerary_pref"]=radiochecked(itinerary_pref);
           var itinerary_pref2=document.getElementsByName("itinerary_pref2");
           postfields["itinerary_pref2"]=radiochecked(itinerary_pref2);
           postfields["memories"]=document.getElementById("memories").value;
           postfields["peeves"]=document.getElementById("peeves").value;
           postfields["decisions"]=document.getElementById("decisions").value;
           postfields["else"]=document.getElementById("else").value;
           for(var k=1;k<=11;k++)
           {
        	 if (document.getElementById("interest"+k).checked==true)
        	 {
        		 postfields["interest"+k]=document.getElementById("interest"+k).value; 
        	 }	 
           }
           var sh_class=document.getElementsByName("sh_class");
           postfields["sh_class"]=radiochecked(sh_class);
           var sh_seat=document.getElementsByName("sh_seat");
           postfields["sh_seat"]=radiochecked(sh_seat);
           var sh_location=document.getElementsByName("sh_location");
           postfields["sh_location"]=radiochecked(sh_location);
           var lh_class=document.getElementsByName("lh_class");
           postfields["lh_class"]=radiochecked(lh_class);
           var lh_seat=document.getElementsByName("lh_seat");
           postfields["lh_seat"]=radiochecked(lh_seat);
           var lh_location=document.getElementsByName("lh_location");
           postfields["lh_location"]=radiochecked(lh_location);
           var posts="";
           for(var key in postfields)
           {
        	 posts+=key+"="+encodeURI(postfields[key])+"&";     
           };
           posts=posts.substring(0,posts.length-1);
           //console.log(posts);
           //console.log(postfields);
           
           var xmlhttp = new XMLHttpRequest();
           xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/uajax.php?task=uppref",true);                      
           xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
           xmlhttp.onreadystatechange=function()
           {
                if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                {
                	if (checkjson(xmlhttp.responseText))
                	{
                	 	var result=JSON.parse(xmlhttp.responseText);
                		if (result.result=="true")
                		{
                		 //console.log('tab5 had been updated successfully!');	
                		}
                		else
                		{
                		 //console.log("tab5 couldn't been updated now!");	
                		}	
                		
                	}
                	else
                	{
                	  	console.log("Error:"+xmlhttp.responseText);
                	}	                	                                       
                }
            };
           xmlhttp.send(posts); 
         };
/*         else if (getElementsByClassName(document.getElementById("tab-container"),"active")[0].id=="aTab1")
         {
        	console.log("i'm in tab1");
        	var postfields=new Array();
        	postfields["prefix"]=document.getElementById("user_prefix").options[document.getElementById("user_prefix").selectedIndex].value;
        	postfields["fn"]=document.getElementById("fn").value;
        	postfields["initial"]=document.getElementById("initial").value;
        	postfields["ln"]=document.getElementById("ln").value;
        	postfields["email"]=document.getElementById("email").value;
        	postfields["s_address1"]=document.getElementById("s_address1").value;
        	postfields["s_address2"]=document.getElementById("s_address2").value;
        	postfields["s_city"]=document.getElementById("s_city").value;
        	postfields["s_state"]=document.getElementById("s_state").value;
        	postfields["s_zip"]=document.getElementById("s_zip").value;
        	postfields["s_country"]=document.getElementById("s_country").value;
        	postfields["phone"]=document.getElementById("phone").value;
        	postfields["phone_w"]=document.getElementById("phone_w").value;
        	postfields["phone_m"]=document.getElementById("phone_m").value;
        	var posts="";
            for(var key in postfields)
            {
         	 posts+=key+"="+encodeURI(postfields[key])+"&";     
            };
            posts=posts.substring(0,posts.length-1);
            console.log(posts);
        	console.log(postfields);
        	var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/uajax.php?task=upcontact",true);                      
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange=function()
            {
                 if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                 {
                 	if (checkjson(xmlhttp.responseText))
                 	{                 	 	
                 		var result=JSON.parse(xmlhttp.responseText);
                 		if (result.result=="true")
                 		{
                 		 console.log('tab1 had been updated successfully!');	
                 		}
                 		else
                 		{
                 		 console.log("tab1 couldn't been updated now!");	
                 		}	
                 		
                 	}
                 	else
                 	{
                 	  	console.log("Error:"+xmlhttp.responseText);
                 	}	                	                                       
                 }
             };
            xmlhttp.send(posts);
        	
         }*/
/*         else if (getElementsByClassName(document.getElementById("tab-container"),"active")[0].id=="aTab2")
         {
        	console.log("i'm in tab2");
         	var postfields=new Array();
         	postfields["mb"]=document.getElementById("membership_level").options[document.getElementById("membership_level").selectedIndex].value;
         	postfields["mb_y"]=document.getElementById("membership_years").options[document.getElementById("membership_years").selectedIndex].value;
         	postfields["address1"]=document.getElementById("address1").value;
         	postfields["address2"]=document.getElementById("address2").value;
         	postfields["city"]=document.getElementById("city").value;
         	postfields["state"]=document.getElementById("state").value;
         	postfields["zip"]=document.getElementById("zip").value;
         	postfields["cc_holder"]=document.getElementById("cc_holder").value;
         	postfields["cc_m"]=document.getElementById("cc_month").options[document.getElementById("cc_month").selectedIndex].value;
         	postfields["cc_y"]=document.getElementById("cc_year").options[document.getElementById("cc_year").selectedIndex].value;         	
         	postfields["ccv"]=document.getElementById("ccv").value;
         	postfields["userid"]=user.crmId;
         	postfields["s_address1"]=document.getElementById("s_address1").value;
         	postfields["s_address2"]=document.getElementById("s_address2").value;
         	postfields["s_city"]=document.getElementById("s_city").value;
         	postfields["s_state"]=document.getElementById("s_state").value;
         	postfields["s_zip"]=document.getElementById("s_zip").value;
         	postfields["s_country"]=document.getElementById("s_country").value;
         	var posts="";
            for(var key in postfields)
            {
         	 posts+=key+"="+encodeURI(postfields[key])+"&";     
            };
            posts=posts.substring(0,posts.length-1);
            console.log(posts);
         	console.log(postfields);
         	var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/uajax.php?task=uprenew",true);                      
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange=function()
            {
                 if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                 {
                 	if (checkjson(xmlhttp.responseText))
                 	{                 	 	
                 		var result=JSON.parse(xmlhttp.responseText);
                 		if (result.result=="true")
                 		{
                 		 console.log('tab2 had been updated successfully!');	
                 		}
                 		else
                 		{
                 		 console.log("tab2 couldn't been updated now!");	
                 		}	
                 		
                 	}
                 	else
                 	{
                 	  	console.log("Error:"+xmlhttp.responseText);
                 	}	                	                                       
                 }
             };
            xmlhttp.send(posts);
         }*/	 
         
        };
        

    }   
    if (window.addEventListener) {
        window.addEventListener('DOMContentLoaded', init, false);
    } else {
        window.attachEvent('onload', init);
    }
} ());

var account = {
    ffAccounts: [],
    spouseFFAccounts: [],
    children: [],
    processing: false,
    createTabber: function() {
            this.tabber1 = new Yetii({
                    id: 'tab-container',
                    class: 'tab'
            });

    },
    initFields: function() {
       //document.getElementById("user_prefix").options[document.getElementById("user_prefix").selectedIndex].value = user.title;	       
       //document.getElementById("user_prefix").value = user.title;
       prefix = '#user_prefix';
       //prefixoptions='#user_prefix  option[value="'+user.title+'"]';
       jQuery(prefix).val(user.title);
   	   //jQuery(prefixoptions).attr('selected','selected');
       jQuery(prefix).customSelect();
       document.getElementById("ln").value = user.lname;
       document.getElementById("initial").value = user.initial;
       document.getElementById("fn").value = user.fname;
       document.getElementById("email").value = user.email;
       document.getElementById("phone").value = user.phone_h;
       document.getElementById("phone_w").value = user.phone_w;
       document.getElementById("phone_m").value = user.phone_m;
       document.getElementById("s_address1").value = user.addr1;
       document.getElementById("s_address2").value = user.addr2;
       document.getElementById("s_city").value = user.city;
       document.getElementById("s_state").value = user.state;
       document.getElementById("s_zip").value = user.postal;
       document.getElementById("s_country").value = user.country;
       //alert(user.contact_pref);
       document.getElementById("member_level").innerHTML = mbs[user.mb-1].name;
       document.getElementById("member_exp").innerHTML = user.mb_exp;
       document.getElementById("chkShip").onchange = this.setBillingAddr;
       this.buildMembershipDD(user.mb);
       this.buildCCYearDD();
       document.getElementById("view_terms").onclick = function (e) {
            e.preventDefault();
            jQuery('#terms').show();
       };
       document.getElementById("subRenewal").onclick = function () {
            account.validateRenew();
       };
        
       document.getElementById("renew").onclick = function (e) {
            e.preventDefault();
            jQuery('#renew_pane').show();

//			jQuery('.customselectdyn').customSelect();
			jQuery('#membership_level').customSelect();
                        jQuery('#membership_level').css("height", "25px");
			jQuery('#membership_years').customSelect();
                        jQuery('#membership_years').css("height", "25px");
                        jQuery('#cc_month').css("height", "25px");
                        jQuery('#cc_year').css("height", "25px");

       };
        
       if (typeof userExt !== 'undefined'){
            var bd = userExt.birthday.split("-");
            document.getElementById("m_bday_y").value = bd[0];
            document.getElementById("m_bday_m").value = bd[1];
            document.getElementById("m_bday_d").value = bd[2];            
            document.getElementById("m_pass").value = userExt.passport;
            
            if (typeof spouse !== 'undefined'){
                document.getElementById("s_name").value = spouse.name;
                document.getElementById("s_email").value = spouse.email;
                var sbd = spouse.birthday.split("-");
                document.getElementById("s_bday_y").value = sbd[0];
                document.getElementById("s_bday_m").value = sbd[1];
                document.getElementById("s_bday_d").value = sbd[2];
                document.getElementById("s_pass").value = spouse.passport;
            }
            
            document.getElementById("a_name").value = userExt.assistent;
            document.getElementById("a_email").value = userExt.assistentEmail;
            document.getElementById("a_phone").value = userExt.assistentPhone;
           
            obj_contact_pref=document.getElementsByName("contact_pref");
            for(i=0;i<obj_contact_pref.length;i++){            	
            	if(obj_contact_pref[i].value==userExt.contact_pref){
            		obj_contact_pref[i].checked=true;
            	}
            }
            
            
            obj_delivery_pref=document.getElementsByName("delivery_pref");
            for(i=0;i<obj_delivery_pref.length;i++){            	
            	if(obj_delivery_pref[i].value==userExt.delivery_pref){
            		obj_delivery_pref[i].checked=true;
            	}
            }
            //document.getElementById("allergies").value = userExt.allergies;
            //document.getElementById("other").value = userExt.other;
       }
       
       if (typeof userFFAs !== "undefined"){
            this.ffAccounts = userFFAs;
       }
       if (typeof spouseFfa !== "undefined"){
            this.spouseFFAccounts = spouseFfa;
       }
       if (typeof children !== "undefined"){
            this.children = children;
       }
       
       if (this.ffAccounts.length > 0) {
            this.updateFFAList();
       }
       
       if (this.spouseFFAccounts.length > 0) {
            this.updateSpouseFFAList();
       }
       
       if (this.children.length > 0) {
            this.updateChildrenList();
       }
       
       if (typeof userPrefs !== 'undefined') {
            if(userPrefs.length > 0) {
                for (var p = 0; p < userPrefs.length; p++) {
                    //console.log(document.getElementById(userPrefs[p].pref + userPrefs[p].value));
                    if (document.getElementById(userPrefs[p].pref + userPrefs[p].value) !== null) {
                        if (document.getElementById(userPrefs[p].pref + userPrefs[p].value).type === "radio") {
                                 document.getElementById(userPrefs[p].pref + userPrefs[p].value).checked = true;
                             }
                    }
                    else if (document.getElementById(userPrefs[p].pref).type === "checkbox") {
                            if (userPrefs[p].value == "on") {
                                 document.getElementById(userPrefs[p].pref).checked = true;
                             }
                    }
                    else if (document.getElementById(userPrefs[p].pref).type === "select-one") {                       
//                    	console.log('i\'m in a hole');
//                            	console.log ( userPrefs[p].pref );
//                            	console.log ( userPrefs[p].value );
                            	myselect = '#'+userPrefs[p].pref;
                            	//myselectOption = '#'+userPrefs[p].pref+' option[value='+userPrefs[p].value+']';
//                            	console.log (myselectOption);
                            	jQuery(myselect).val(userPrefs[p].value);
                            	//jQuery(myselectOption).attr('selected','selected');
								jQuery(myselect).customSelect();
                    }
                    else {
                    	 //console.log(document.getElementById(userPrefs[p].pref).type);
                         document.getElementById(userPrefs[p].pref).value = userPrefs[p].value;
                    }
                 }
            }
        }
    },
    buildButtonEventMgrs: function () {
        document.getElementById("subTab1").onclick = function () {};
        document.getElementById("addFFa").onclick = function () {
            account.addFfa(document.getElementById("m_ffn").value, document.getElementById("m_ffa").value);
            document.getElementById("m_ffn").value = "";
            document.getElementById("m_ffa").value = "";
        };
        document.getElementById("addSpouseFFa").onclick = function () {
            account.addSpouseFfa(document.getElementById("s_ffn").value, document.getElementById("s_ffa").value);
            document.getElementById("s_ffn").value = "";
            document.getElementById("s_ffa").value = "";
        };
        document.getElementById("addChild").onclick = function () {
            account.addChild(document.getElementById("c_name").value, 
                document.getElementById("c_bday_y").value + "-" + document.getElementById("c_bday_m").value + "-" + document.getElementById("c_bday_d").value);
            document.getElementById("c_name").value = "";
            document.getElementById("c_bday_m").value = "";
            document.getElementById("c_bday_d").value = "";
            document.getElementById("c_bday_y").value = "";
        };
    },
    buildMembershipDD: function (mb) {
        var mb_select = document.getElementById("membership_level");
        var option;
       for (var m in mbs) {
           if (mb <= mbs[m].level){
           option = document.createElement("option");
           option.text = mbs[m].name;
           option.value = mbs[m].level;
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
       }
       this.setSelectedMBYears(mb_select.options[mb_select.selectedIndex].value - 1);  
       mb_select.onchange = function () {
           account.setSelectedMBYears(mb_select.options[mb_select.selectedIndex].value - 1);
       };
    },
    setSelectedMBYears: function (m) {
        var mby_select = document.getElementById("membership_years");
        mby_select.options.length = 0;
        var option1 = document.createElement("option");
        option1.text = "$" + (mbs[m].p1/100) + ".00 for 1 year";
        option1.value = "1";
        var option2 = document.createElement("option");
        option2.text = "$" + (mbs[m].p2/100) + ".00 for 2 years";
        option2.value = "2";
        var option3 = document.createElement("option");
        option3.text = "$" + (mbs[m].p3/100) + ".00 for 3 years";
        option3.value = "3"; 
        try { 
               mby_select.add(option3, mby_select.options[null]);
               mby_select.add(option2, mby_select.options[0]);
               mby_select.add(option1, mby_select.options[0]);
               mby_select.options[0].selected = 'selected';               
           }
           catch (e) {
               mby_select.add(option3, null);
               mby_select.add(option2, option3);
               mby_select.add(option1, option2);
               mby_select.options[0].selected = 'selected';
           }
        jQuery('#membership_years').trigger("render");
    },
    setBillingAddr: function() {
        var chkbox = document.getElementById("chkShip");
        if (chkbox.checked) {
            document.getElementById("address1").value = document.getElementById("s_address1").value;
            document.getElementById("address1").readOnly = true;
            document.getElementById("address2").value = document.getElementById("s_address2").value;
            document.getElementById("address2").readOnly = true;
            document.getElementById("city").value = document.getElementById("s_city").value;
            document.getElementById("city").readOnly = true;
            document.getElementById("state").value = document.getElementById("s_state").value;
            document.getElementById("state").readOnly = true;
            document.getElementById("zip").value = document.getElementById("s_zip").value;
            document.getElementById("zip").readOnly = true;
            document.getElementById("country").value = document.getElementById("s_country").value;
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
    validateRenew: function () {
        var cc_month = document.getElementById("cc_month");
        var cc_year = document.getElementById("cc_year");
        var msg = "";
        var complete = true;
        
        jQuery('.validatetext').text('');
        
        
        if (document.getElementById("agree2terms").checked == false) {
            msg = "You have to agree to our Terms & Conditions.";
            jQuery('#tab3_TandC_info').text(msg);
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
        //debugger;
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

        if (complete) {
            this.processPay();
        }
    },
    processPay: function () {
        if (!this.processing){
            this.processing = true;
            var cc_month = document.getElementById("cc_month");
            var cc_year = document.getElementById("cc_year");
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    var result = xmlhttp.responseText.split("-");
                    //console.log(result);
                    if(result[0] == "APPROVED") {

                        var date = new Date();
                        document.getElementById("memberdate").innerHTML = "<strong>Signup Date: </strong><span>" + (date.getMonth() + 1) + '/' + date.getDate() + '/' +  date.getFullYear() + "</span>";
                        document.getElementById("memberlevel").innerHTML = "<strong>Membership Level: </strong><span>" + document.getElementById("membership_level").options[document.getElementById("membership_level").selectedIndex].text+ "</span>";
                        document.getElementById("membercost").innerHTML = "<strong>Membership Cost: </strong><span>" + document.getElementById("membership_years").options[document.getElementById("membership_years").selectedIndex].text+ "</span>";
                        document.getElementById("memberlength").innerHTML = "<strong>Duration: </strong><span>" + document.getElementById("membership_years").options[document.getElementById("membership_years").selectedIndex].text+ "</span>";
                        document.getElementById("membercardholder").innerHTML = "<strong>Cardholder Name: </strong><span>" + document.getElementById("cc_holder").value+ "</span>";
                        var c = document.getElementById("cc_num").value;
                        document.getElementById("membercard").innerHTML = "<strong>Payment Using Credit Card Ending In: </strong><span>" + c.substr(c.length - 4)+ "</span>";
                        document.getElementById("membertransaction").innerHTML = "<strong>Transaction Code: </strong><span>" + result[1]+ "</span>";



                        // wire up redirect event
                        document.getElementById("membercomplete").onclick = function (e) {
                            e.preventDefault();
                            window.location.href = "/account/";
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
                    account.processing = false;
                }
            }; 
            xmlhttp.open("POST","/wp-content/themes/indagare/app/lib/iajax.php?task=renew",true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            
            //var mb_select = document.getElementById("membership_level");
            //var mby_select = document.getElementById("membership_years");
            
            var posts = "userid=" + user.crmId +  "&s_address1=" + 
                    encodeURI(document.getElementById("s_address1").value) + "&s_address2=" + 
                    encodeURI(document.getElementById("s_address2").value) + "&s_city=" +
                    encodeURI(document.getElementById("s_city").value) + "&s_state=" +
                    encodeURI(document.getElementById("s_state").value) + "&s_zip=" +
                    encodeURI(document.getElementById("s_zip").value) + "&s_country=" +
                    encodeURI(document.getElementById("s_country").value) + "&address1=" + 
                    encodeURI(document.getElementById("address1").value) + "&address2=" + 
                    encodeURI(document.getElementById("address2").value) + "&city=" +
                    encodeURI(document.getElementById("city").value) + "&state=" +
                    encodeURI(document.getElementById("state").value) + "&zip=" +
                    encodeURI(document.getElementById("zip").value) + "&country=" +
                    encodeURI(document.getElementById("country").value) + "&cc_holder=" +
                    encodeURI(document.getElementById("cc_holder").value) + "&cc_num=" +
                    encodeURI(document.getElementById("cc_num").value) + "&ccv=" +
                    encodeURI(document.getElementById("ccv").value) + "&cc_m=" +
                    encodeURI(cc_month.options[cc_month.selectedIndex].value) + "&cc_y=" +
                    encodeURI(cc_year.options[cc_year.selectedIndex].value)+ "&mb=" +
                    encodeURI(document.getElementById("membership_level").value) + "&mb_y=" +
                    encodeURI(document.getElementById("membership_years").options[document.getElementById("membership_years").selectedIndex].value);

            xmlhttp.send(posts);
        
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
    } ,
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
    addFfa: function (ffn, ffair) {
        this.ffAccounts.push({id:0, n: ffn, a: ffair});
        this.updateFFAList();
    },
    addSpouseFfa: function (ffn, ffair) {
        this.spouseFFAccounts.push({id:0, n: ffn, a: ffair});
        this.updateSpouseFFAList();
    },
    updateFFAList: function () {
        var html = "";
        for (var i = 0; i < this.ffAccounts.length; i++) {
			html += "<div class=\"fieldgroup\">\n";
            html += "<div class=\"field\"><input type=\"hidden\" name=\"m_ffaId" + i + "\" value=\"" + this.ffAccounts[i].id + "\">";
            html += "<label>Airline</label><input type=\"text\" name=\"m_ffa" + i + "\" id=\"m_ffa" + i + 
                    "\" value=\"" + this.ffAccounts[i].a + "\">" + 
                    "</div>\n" + 
                    "<div class=\"field\">\n" + 
                    "<label>Frequent Flier #</label><input type=\"text\" name=\"m_ffn" + i + "\" id=\"m_ffn" + i + "\" value=\"" +this.ffAccounts[i].n + "\">\n" + 
					"</div>\n" +
                    "<div class=\"field\">\n" + 
                    "<label></label><input class=\"mini\" id=\"remFFA" + i + "\" type=\"Button\" value=\"Remove\" ffa=\"" + i + "\">\n</div>\n</div>\n";
        }
        document.getElementById("ffa_list").innerHTML = html;
        
        for (i = 0; i < this.ffAccounts.length; i++) {
                    //console.log(i);
                    //console.log(account.ffAccounts[i]);
                    document.getElementById("remFFA" + i).onclick = function () {
                        var index = this.getAttribute("ffa");
                        var rem = document.getElementById("remFFA").value;
                        if (rem === "0"){
                            document.getElementById("remFFA").value = account.ffAccounts[index].id;
                        }
                        else {
                            document.getElementById("remFFA").value = rem + "," + account.ffAccounts[index].id; 
                        }
                        account.remFromArray(account.ffAccounts, index);
                        account.updateFFAList();
                    };
         }
        
    },
    remFromArray: function(myArray, index) {
        return myArray.splice(index, 1);
    },
    updateSpouseFFAList: function () {
        var html = "";
        for (var i = 0; i < this.spouseFFAccounts.length; i++) {
            html += "<div class=\"fieldgroup\">\n";
            html += "<div class=\"field\"><input type=\"hidden\" name=\"s_ffaId" + i + "\" value=\"" + this.spouseFFAccounts[i].id + "\">";
            html += "<label>Airline</label><input type=\"text\" name=\"s_ffa" + i + "\" id=\"s_ffa" + i + 
                    "\" value=\"" + this.spouseFFAccounts[i].a + "\">" + 
                    "</div>\n" + 
                    "<div class=\"field\">\n" + 
                    "<label>Frequent Flier #</label><input type=\"text\" name=\"s_ffn" + i + "\" id=\"s_ffn" + i + "\" value=\"" +this.spouseFFAccounts[i].n + "\">\n</div>\n" +
                    "<div class=\"field\">\n" + 
                    "<label></label><input class=\"mini\" id=\"remSFFA" + i + "\" type=\"Button\" value=\"Remove\" ffa=\"" + i + "\">\n" + 
                    "</div>\n</div>\n";
        }
        document.getElementById("spouse_ffa_list").innerHTML = html;
        
        for (i = 0; i < this.spouseFFAccounts.length; i++) {
                    //console.log(i);
                    //console.log(account.spouseFFAccounts[i]);
                    document.getElementById("remSFFA" + i).onclick = function () {
                        var index = this.getAttribute("ffa");
                        var rem = document.getElementById("remSFFA").value;
                        if (rem === "0"){
                            document.getElementById("remSFFA").value = account.spouseFFAccounts[index].id;
                        }
                        else {
                            document.getElementById("remSFFA").value = rem + "," + account.spouseFFAccounts[index].id; 
                        }
                        account.remFromArray(account.spouseFFAccounts, index);
                        account.updateSpouseFFAList();
                    };
         }
    },
    addChild: function (name, birthday) {
        this.children.push({id:0, name: name, birthday: birthday, ffa: []});
        this.updateChildrenList();
    },
    addChildFfa: function (child, ffn, ffair) {
        this.children[child].ffa.push({id:0, n: ffn, a: ffair});
        this.updateChildFFAList();
    },
    updateChildFFAList: function(id) {
        var html = "";
        for (var i = 0; i < this.children[id].ffa.length; i++) {
            html += "<div class=\"field\"><input type=\"hidden\" name=\"c_ffaId" + id + "_" + i + "\" value=\"" + this.children[id].ffa[i].id + "\">";
                    html += "<label>Airline</label><input type=\"text\" name=\"c_ffa" + id + "_" + i + "\" id=\"c_ffa" + id + "_" + i + 
                    "\" value=\"" + this.children[id].ffa[i].a + "\">" + 
                    "</div>\n" + 
                    "<div class=\"field\">\n" + 
                    "<label>Frequent Flier #</label><input type=\"text\" name=\"c_ffn" + id + "_" + i + "\" id=\"c_ffn" + id + "_" + i + "\" value=\"" +this.children[id].ffa[i].n + "\"></div>\n" + 
                    "<div class=\"field\"><label></label><input class=\"mini\" id=\"remCFFA" + id + "_" + i + "\" type=\"Button\" value=\"Remove\" cid=\"" + id + "\" ffa=\"" + i + "\"></div>\n\n";
        }
        //html += "</ul>";
        document.getElementById("child" + id + "_ffa_list").innerHTML = html;
        
        for (j = 0; j < this.children[id].ffa.length; j++) {
                    //console.log("remCFFA" + id + "_" + j);
                    document.getElementById("remCFFA" + id + "_" + j).onclick = function () {
                        var index = this.getAttribute("ffa");
                        var cid = this.getAttribute("cid");
                        //console.log(index, cid);
                        var rem = document.getElementById("remCFFA").value;
                        if (rem === "0"){
                            document.getElementById("remCFFA").value = account.children[cid].ffa[index].id;
                        }
                        else {
                            document.getElementById("remCFFA").value = rem + "," + account.children[cid].ffa[index].id; 
                        }
                        account.remFromArray(account.children[cid].ffa, index);
                        account.updateChildFFAList(cid);
                    };
                }
    },
    updateChildrenList: function () {
        var html = "";
        for (var i = 0; i < this.children.length; i++) {
			html += "<div class=\"fieldgroup\">\n";
            html += "<div id=\"c" + i + "\" >";
            html += "<div class=\"field\"><input type=\"hidden\" name=\"c" + i + "_id\" value=\"" + this.children[i].id + "\">";
            html += "<div class=\"field\"><label>Child Name</label><input type=\"text\" name=\"c" + i + "_name\" id=\"c" + i + "_name\" value=\"" + this.children[i].name + "\"></div>";
            html += "<div class=\"field\"><label></label><input class=\"small\" id=\"rmChild" + i + "\" type=\"Button\" value=\"Remove Child\" cid=\"" + i + "\"></div>";
            html += "<div class=\"field\"><label>Child Birthday</label><input class=\"mini\" type=\"text\" name=\"c" + i + "_bday_m\" id=\"c" + i + "_bday_m\" value=\"" + this.children[i].birthday.split('-')[1] + "\"> / ";
            html += "<input class=\"mini\" type=\"text\" name=\"c" + i + "_bday_d\" id=\"c" + i + "_bday_d\" value=\"" + this.children[i].birthday.split('-')[2] + "\"> / <input class=\"mini\" type=\"text\" name=\"c" + i + "_bday_y\" id=\"c" + i + "_bday_y\" value=\"" + this.children[i].birthday.split('-')[0] + "\"><span id=\"c"+i+"_bdayval\" class=\"validate\"></span></div>";
            html += "<div id=\"child" + i + "_ffa_list\">";
            
            if (this.children[i].ffa.length > 0) {
                for (var j = 0; j < this.children[i].ffa.length; j++) {
                    //console.log(this.children[i].ffa[j].id);
                    html += "<div class=\"field\"><input type=\"hidden\" name=\"c_ffaId" + i + "_" + j + "\" value=\"" + this.children[i].ffa[j].id + "\">";
                    html += "<label>Airline</label><input type=\"text\" name=\"c_ffa" + i + "_" + j + "\" id=\"c_ffa" + i + "_" + j + 
                    "\" value=\"" + this.children[i].ffa[j].a + "\">" + 
                    "</div>\n" + 
                    "<div class=\"field\">\n" + 
                    "<label>Frequent Flier #</label><input type=\"text\" name=\"c_ffn" + i + "_" + j + "\" id=\"c_ffn" + i + "_" + j + "\" value=\"" +this.children[i].ffa[j].n + "\">\n" + 
					"</div>\n" +
                    "<div class=\"field\">\n" + 
                    "<label></label><input class=\"mini\" id=\"remCFFA" + i + "_" + j + "\" type=\"Button\" value=\"Remove\" cid=\"" + i + "\" ffa=\"" + j + "\">\n" + 
					"</div>\n";
                }
            }
            html += "</div>";
            html += "<div class=\"field\"><label>Airline</label><input type=\"text\" name=\"c" + i + "_ffa\" id=\"c" + i + "_ffa\">";
            html += "</div>";
            html += "<div class=\"field\"><label>Frequent Flier #</label><input type=\"text\" name=\"c" + i + "_ffn\" id=\"c" + i + "_ffn\">";
            html += "</div>";
			html += "</div>\n";
            html += "<div class=\"field\"><label></label>\n";
            html += "<input id=\"addChild" + i + "FFa\" type=\"Button\" class=\"mini\" value=\"Add\" cid=\"" + i + "\"></div>";
            html += "</div>";
        }
        
        document.getElementById("children_list").innerHTML = html;
        
         for (i = 0; i < this.children.length; i++) {
             
             document.getElementById("rmChild" + i).onclick = function () {
                        var cid = this.getAttribute("cid");
                        //console.log(index, cid);
                        var rem = document.getElementById("remChild").value;
                        if (rem === "0"){
                            document.getElementById("remChild").value = account.children[cid].id;
                        }
                        else {
                            document.getElementById("remChild").value = rem + "," + account.children[cid].id; 
                        }
                        account.remFromArray(account.children, cid);
                        account.updateChildrenList(cid);
                    };
             
             document.getElementById("addChild" + i + "FFa").onclick = function () {
                 var i = this.getAttribute("cid");
                account.children[i].ffa.push({id:0, n: document.getElementById("c" + i + "_ffn").value, a: document.getElementById("c" + i + "_ffa").value});
                account.updateChildFFAList(i);
                document.getElementById("c" + i + "_ffa").value = "";
                document.getElementById("c" + i + "_ffn").value = "";
                };
                
                for (j = 0; j < this.children[i].ffa.length; j++) {
                    //console.log("remCFFA" + i + "_" + j);
                    document.getElementById("remCFFA" + i + "_" + j).onclick = function () {
                        var index = this.getAttribute("ffa");
                        var cid = this.getAttribute("cid");
                        console.log(index, cid);
                        var rem = document.getElementById("remCFFA").value;
                        if (rem === "0"){
                            document.getElementById("remCFFA").value = account.children[cid].ffa[index].id;
                        }
                        else {
                            document.getElementById("remCFFA").value = rem + "," + account.children[cid].ffa[index].id; 
                        }
                        account.remFromArray(account.children[cid].ffa, index);
                        account.updateChildFFAList(cid);
                    };
                }
         }
    },
    checknumers: function (s)
    {
       if (s!=null && s!="")
      {
       return !isNaN(s);
      }
      else
      {
       return false; 	   
      }	   
    	    
    	    	
    },
    checkbrithvalidate : function () {
    	complete = true;
    	//debugger;
    	//ps=this.children;
    	//alert(this.children);
    	//check for member birthday
    	initmsg="";
    	jQuery('#m_bdayval').text(initmsg);
    	jQuery('#s_bdayval').text(initmsg);    	
    	for (var y = 0; y < this.children.length; y++) 
    	{
    		jQuery('#c'+y+'_bdayval').text(initmsg);	
    	};
    	if (!this.checknumers(document.getElementById("m_bday_m").value)) {
    		if (!this.checknumers(document.getElementById("m_bday_d").value)) 
    		{   
    			if (!this.checknumers(document.getElementById("m_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH,DAY and YEAR in Member.";
                    jQuery('#m_bdayval').text(msg);
                    complete = false;
    			}
    			else
    			{	
    			    msg = "Please enter a valid  number for MONTH and DAY in Member.";
                    jQuery('#m_bdayval').text(msg);
                    complete = false;
    			}
    		}
    		else
    		{  
    			if (!this.checknumers(document.getElementById("m_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH and YEAR in Member.";
       	            jQuery('#m_bdayval').text(msg);
       	            complete = false;
    			}
    			else
    			{
    			    msg = "Please enter a valid  number for MONTH in Member.";
    	            jQuery('#m_bdayval').text(msg);
    	            complete = false;
    			}	
    		   
    		}	
    		
            //complete = false;   
        }
    	else
    	{
    		if (!this.checknumers(document.getElementById("m_bday_d").value)) 
    		{
    			if (!this.checknumers(document.getElementById("m_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for DAY and Year in Member.";
    	          jQuery('#m_bdayval').text(msg);
    	          complete = false;	
        		}
    			else
    			{
    			  msg = "Please enter a valid  number for DAY in Member.";
    	          jQuery('#m_bdayval').text(msg);
    	          complete = false;	
    			}	
    				
    		}
    		else
    		{
    			if (!this.checknumers(document.getElementById("m_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for Year in Member.";
      	          jQuery('#m_bdayval').text(msg);
      	          complete = false;		
        		}    				
    		}	
    	};
    	
    	//check for Spouse birthday
    	if (!this.checknumers(document.getElementById("s_bday_m").value)) {
    		if (!this.checknumers(document.getElementById("s_bday_d").value)) 
    		{   
    			if (!this.checknumers(document.getElementById("s_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH,DAY and YEAR in Spouse.";
                    jQuery('#s_bdayval').text(msg);
                    complete = false;
    			}
    			else
    			{	
    			    msg = "Please enter a valid  number for MONTH and DAY in Spouse.";
                    jQuery('#s_bdayval').text(msg);
                    complete = false;
    			}
    		}
    		else
    		{  
    			if (!this.checknumers(document.getElementById("s_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH and YEAR in Spouse.";
       	            jQuery('#s_bdayval').text(msg);
       	            complete = false;
    			}
    			else
    			{
    			    msg = "Please enter a valid  number for MONTH in Spouse.";
    	            jQuery('#s_bdayval').text(msg);
    	            complete = false;
    			}	
    		   
    		}	
    		
            //complete = false;   
        }
    	else
    	{
    		if (!this.checknumers(document.getElementById("s_bday_d").value)) 
    		{
    			if (!this.checknumers(document.getElementById("s_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for DAY and Year in Spouse.";
    	          jQuery('#s_bdayval').text(msg);
    	          complete = false;	
        		}
    			else
    			{
    			  msg = "Please enter a valid  number for DAY in Spouse.";
    	          jQuery('#s_bdayval').text(msg);
    	          complete = false;	
    			}	
    				
    		}
    		else
    		{
    			if (!this.checknumers(document.getElementById("s_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for Year in Spouse.";
      	          jQuery('#s_bdayval').text(msg);
      	          complete = false;		
        		}    				
    		}	
    	};
    	//check for multi-child birthday
    	for (var i = 0; i < this.children.length; i++) {
    		//alert(document.getElementById("c"+i+"_bday_m").value);
    		if (!this.checknumers(document.getElementById("c"+i+"_bday_m").value)) {
        		if (!this.checknumers(document.getElementById("c"+i+"_bday_d").value)) 
        		{   
        			if (!this.checknumers(document.getElementById("c"+i+"_bday_y").value)) 
        			{
        				msg = "Please enter a valid  number for MONTH,DAY and YEAR in Child.";
                        jQuery('#c'+i+'_bdayval').text(msg);
                        complete = false;
        			}
        			else
        			{	
        			    msg = "Please enter a valid  number for MONTH and DAY in Child.";
                        jQuery('#c'+i+'_bdayval').text(msg);
                        complete = false;
        			}
        		}
        		else
        		{  
        			if (!this.checknumers(document.getElementById("c"+i+"_bday_y").value)) 
        			{
        				msg = "Please enter a valid  number for MONTH and YEAR in Child.";
           	            jQuery('#c'+i+'_bdayval').text(msg);
           	            complete = false;
        			}
        			else
        			{
        			    msg = "Please enter a valid  number for MONTH in Member.";
        	            jQuery('#c'+i+'_bdayval').text(msg);
        	            complete = false;
        			}	
        		   
        		}	
        		
                //complete = false;   
            }
        	else
        	{
        		if (!this.checknumers(document.getElementById("c"+i+"_bday_d").value)) 
        		{
        			if (!this.checknumers(document.getElementById("c"+i+"_bday_y").value)) 
            		{
        			  msg = "Please enter a valid  number for DAY and Year in Child.";
        	          jQuery('#c'+i+'_bdayval').text(msg);
        	          complete = false;	
            		}
        			else
        			{
        			  msg = "Please enter a valid  number for DAY in Child.";
        	          jQuery('#c'+i+'_bdayval').text(msg);
        	          complete = false;	
        			}	
        				
        		}
        		else
        		{
        			if (!this.checknumers(document.getElementById("c"+i+"_bday_y").value)) 
            		{
        			  msg = "Please enter a valid  number for Year in Child.";
          	          jQuery('#c'+i+'_bdayval').text(msg);
          	          complete = false;		
            		}    				
        		}	
        	};
    	};
    	//check for Child birthday
    	/*if (!this.checknumers(document.getElementById("c_bday_m").value)) {
    		if (!this.checknumers(document.getElementById("c_bday_d").value)) 
    		{   
    			if (!this.checknumers(document.getElementById("c_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH,DAY and YEAR in Child.";
                    jQuery('#c_bdayval').text(msg);
                    complete = false;
    			}
    			else
    			{	
    			    msg = "Please enter a valid  number for MONTH and DAY in Child.";
                    jQuery('#c_bdayval').text(msg);
                    complete = false;
    			}
    		}
    		else
    		{  
    			if (!this.checknumers(document.getElementById("c_bday_y").value)) 
    			{
    				msg = "Please enter a valid  number for MONTH and YEAR in Child.";
       	            jQuery('#c_bdayval').text(msg);
       	            complete = false;
    			}
    			else
    			{
    			    msg = "Please enter a valid  number for MONTH in Member.";
    	            jQuery('#c_bdayval').text(msg);
    	            complete = false;
    			}	
    		   
    		}	
    		
            //complete = false;   
        }
    	else
    	{
    		if (!this.checknumers(document.getElementById("c_bday_d").value)) 
    		{
    			if (!this.checknumers(document.getElementById("c_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for DAY and Year in Child.";
    	          jQuery('#c_bdayval').text(msg);
    	          complete = false;	
        		}
    			else
    			{
    			  msg = "Please enter a valid  number for DAY in Child.";
    	          jQuery('#c_bdayval').text(msg);
    	          complete = false;	
    			}	
    				
    		}
    		else
    		{
    			if (!this.checknumers(document.getElementById("c_bday_y").value)) 
        		{
    			  msg = "Please enter a valid  number for Year in Child.";
      	          jQuery('#c_bdayval').text(msg);
      	          complete = false;		
        		}    				
    		}	
    	};*/
    	
    	return complete;    	
    }
    
};


