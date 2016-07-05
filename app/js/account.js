/*Polyfills!*/
if (!Array.isArray) {
  Array.isArray = function(arg) {
    return Object.prototype.toString.call(arg) === '[object Array]';
  };
}

var user;
if(!user) {
	user = {};
	user.Account = null;
	user.Contacts = [];
}
var getting = {};

/*
this.tabber1 = new Yetii({
        id: 'tab-container',
        class: 'tab'
});
*/

jQuery(document).on('click','#profile-passport-new',function(e) {
	e.preventDefault();
	var i = newCardItem('passport',blank.PassportVisa);
	var t = jQuery('#profile-passports');
	var y = t.find('.card-item-passport').last();
	if( !y.length ) {
		t.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
});

jQuery(document).on('click','#profile-freq-new',function(e) {
	e.preventDefault();
	var i = newCardItem('freq',blank.FrequentTravel);
	var t = jQuery('#profile-passports');
	var y = t.find('.card-item-freq').last();
	if( !y.length ) {
		t.append(i);
	} else {
		y.after(i);
	}
	jQuery.scrollTo(i,300,{offset:-25});
});

jQuery(document).on('click','.card-save-button',function(e) {
	e.preventDefault();
	jQuery(e.target).addClass('processing').prop({disabled:true});
	var f = jQuery(e.target).parents('form'),
		type = f.attr('form-object'),
		postdata = getFormData(f),
		i,id_set=false,tr_set=(type!='PassportVisa');
	
	postdata.push({name:'action',value:'wpsf-putobject'});
	postdata.push({name:'objectType',value:type});
	
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		method: "POST",
		data : postdata 
	}).done(function(result) {
		if(!result.success) {
			jQuery.alert({
				title:'Save failed',
				content:result.data[0]
			});
		} else {
			f.find('[name=Id]').val(result.data.Id.value);
		}
	}).always(function(){
		jQuery(e.target).removeClass('processing').prop({disabled:false});
	});
});

jQuery(document).on('click','.card-delete-button',function(e) {
	e.preventDefault();
	var f = jQuery(e.target).parents('form'),
		type = f.attr('form-object'),
		id = f.find('input[name=Id]').first().val(),
		postdata = {action:"wpsf-delobject",Id:id,objectType:type};
	
		if((type == 'PassportVisa')||(type == 'FrequentTravel')) {
			postdata['Contact__c'] = f.find('[name=Contact__c]').first().val();
		}
    jQuery.confirm({
        title: 'Are you sure?',
        content: 'This cannot be un-done.',
        keyboardEnabled: true,
        confirmKeys:[],
        confirmButton: 'Yes, Delete it',
        confirmButtonClass: 'btn-danger',
        cancelButton: 'Cancel',
    	confirm: function () {
    		if ( id != '' ) {
    			jQuery(e.target).addClass('processing').prop({disabled:true});
    			// We have an Id.  We need to actually delete this.
    			jQuery.ajax("/wp-admin/admin-ajax.php", {
    				method: "POST",
					data : postdata 
    			}).done(function(result) {
    				if(!result.success) {
    					jQuery.alert({
    						title:'Delete failed',
    						content:result.data
    					});
    				} else {
            			f.parent().remove();
    				}
    			}).always(function(){
    				jQuery(e.target).removeClass('processing').prop({disabled:false});
    			});
    		} else {
				f.parent().remove();
    		}
    	}
	});
});

var progressDialog = null;

jQuery(document).on('change','#contactselect',function(){
	if ( jQuery('#contactselect').val() == '' ) {
		// New account creation selected.  Pop up the dialog!
	    jQuery.confirm({
	        title: 'Create New Contact',
	        content: jQuery('#new-contact-form').html(),
	        keyboardEnabled: false,
	        confirmButton: 'Create',
	        confirmButtonClass: 'btn-info',
	        cancelButton: 'Cancel',
        	confirm: function () {
        		createNewContact(this.$content);
        		progressDialog = jQuery.alert({
        			title:'Creating contact',
        			content:'Please wait...',
        			closeIcon: false,
        			confirmButton:'',
        			cancelButton:'',
        			confirmButtonClass:'hidden',
        			cancelButtonClass:'hidden',
        		});
	        }
	    });
	}
	
	contactSelectionChange();
});

jQuery(document).on('click','#contact-select-bar-item',function(e){
	e.preventDefault();
	e.stopPropagation();
});

jQuery(document).on('click','.bar-element',function(e){
	var p = jQuery(e.currentTarget),
		a = p.attr('collapse-match'),
		b = p.attr('collapse-open'),
		c = p.attr('collapse-close');
	e.preventDefault();
	if(b) {
		jQuery(b).removeClass('collapsed');
	}
	if(c) {
		jQuery(c).addClass('collapsed');
	}
	if(p.hasClass('collapsed')){
		p.removeClass('collapsed');
		if(a) {
			jQuery(a).removeClass('collapsed');
		}
		jQuery.scrollTo('#account-bar',300,{offset:-25});
	} else {
		p.addClass('collapsed');
		if(a) {
			jQuery(a).addClass('collapsed');
		}
	}
});
	
jQuery(document).ready(function(){
	getAccount();
});

function getFormData(f) {
	var r = [];
	jQuery(f).find('select,input,textarea').each(function(x,i){
		var j = jQuery(i), n = j.attr('name'), v = j.val(), t = j.attr('picker');
		if(n) {
			if(t == 'date') {
				v = jQuery(j).datepicker('getDate');
				if(v)
					v = v.toISOString();
			}
			r.push({name:n,value:v});
		}
	});
	return r;
}

function getAccount() {
	if(getting.account) return;
	getting.account = true;
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		data : {
			action : "wpsf-getaccount",
		} 
	}).done(function(result) {
		user.Account = result.data;
		user.Membership = user.Account.Membership__x;
		user.Contacts = user.Account.Contacts__x;
		jQuery('[form-object]').addClass('needs-reload');
		updateAccountBar();
		getting.account = false;
		updateContacts();
	});
}

function updateContacts() {
	if ( ! user.Contacts ) {
		return;
	}
	jQuery('#contactselect').html('');
	var first = null;
	for( c in user.Contacts ) {
		if(!first) first = c;
		var i = user.Contacts[c];
		jQuery('<option></option>')
			.attr({value:i.Id.value})
			.html(i.Name.value)
			.appendTo('#contactselect');
	}
	if ( user.Account.IsPrimaryContact__x ) {
		jQuery('<option></option>')
			.attr({value:''})
			.html('Add new contact...')
			.appendTo('#contactselect');
	}
	jQuery('#contactselect').val( user.Contacts[first].Id.value ).trigger('change');
}

function contactSelectionChange() {
	if ( ! user.Contacts ) {
		return;
	}
	var id = jQuery('#contactselect').val();
	jQuery('[form-object=Contact]')
		.attr('form-record-id', id)
		.addClass('needs-reload');
	updateForms();
}

function contactData(id) {
	for(c in user.Contacts) {
		if ( id == user.Contacts[c].Id.value ) {
			return user.Contacts[c];
		}
	}
	return null;
}

function updateAccountBar() {
	jQuery('#account-bar').find('[form-data-field]').each(function(x,f){
		var d = jQuery(f).attr('form-data-source');
		if(!d || !user.hasOwnProperty(d)) return;
		d = user[d];
		var i = jQuery(f).attr('form-data-field'), h='';
		if(d.hasOwnProperty(i)) {
			h = d[i].value;
		}
		jQuery(f).html(h);
	});
}

function newCardItem(y,i) {
	var c=jQuery('<div></div>').addClass('card-item card-item-'+y),
		f=jQuery('<form></form>'),
		p='carditem-'+getIdFromObject(i),
		z=[],x;
	switch(y) {
		case 'passport':
			f.attr('form-object',"PassportVisa");
			z = [
			     'Id',
			     'Contact__c',
			     'RecordTypeId',
			     'Country__c',
			     'Legal_Name__c',
			     'Number__c',
			     'Expiry_Date__c'
		    ];
			break;
			
		case 'freq':
			f.attr('form-object',"FrequentTravel");
			z = [
			     'Id',
			     'Contact__c',
			     'Name',
			     'Carrier_Hotel_Operator__c',
			     'Frequent_Traveler_Program__c',
			     'Frequent_Flyer_Number__c',
			     'Assistant_Email__c',
			     'Assistant_Name__c',
			     'Assistant_Phone__c'
		    ];
			break;
	}
	for(x in i) {
		if(jQuery.inArray(x,z) >= 0)
			f.append(makeInput(p,i[x],false));
	}
	f.find('input[name="Contact__c"]').val( jQuery('#contactselect').val() );
	
	jQuery('<a></a>').attr('href','#').html('Delete Item').addClass('card-delete-button card-button-large').appendTo(f);
	jQuery('<a></a>').attr('href','#').html('Save Item').addClass('card-save-button card-button-large').appendTo(f);
	f.appendTo(c);
	
	return c;
}

function getIdFromObject(i){
	var x,d;
	for(x in i) {
		if(i[x]['name']=='Id') {
			return (i[x]['value']?i[x]['value']:'noid');
		}
	}
}

function createNewContact(f) {
	var postdata = {
		action: 'idj-newcontact',
		fn: f.find('[name="FirstName"]').val(),
		ln: f.find('[name="LastName"]').val(),
		phone: f.find('[name="Phone"]').val(),
		email: f.find('[name="Email"]').val(),
		username: f.find('[name="Username"]').val(),
		password: f.find('[name="Password"]').val()
	};
	
	jQuery.ajax("/wp-admin/admin-ajax.php", {
		method: "POST",
		data : postdata
	}).done(function(result) {
		if(progressDialog) {
			progressDialog.close();
		}
		if(!result.success) {
			jQuery.alert({
				title:'Creation failed',
				content:result.data[0]
			});
			return false;
		}
		
		user.Contacts.push(result.data);
		updateContacts();
		jQuery('#contactselect').val( result.data.Id.value ).trigger('change');
	});
}

function updateForms() {
	jQuery('[form-fields] .auto-created-field').remove();
	jQuery('[form-fields]').each(function(x,f){
		var i, 
			p = user.Account.IsPrimaryContact__x,
			d = null,
			y = jQuery(f).attr('id'),
			z = jQuery(f).attr('form-fields'),
			a = jQuery(f).attr('form-object');
		if(!y||!y.length) return;
		switch(a) {
			case 'Contact':
				d = contactData(jQuery(f).attr('form-record-id'));
				p = p || d.IsCurrentUser__x.value;
				
				jQuery('#profile-passports').empty();
				
				if(!!d && d['Passport_Visa__x']) {
					for ( i in d['Passport_Visa__x'] ) {
						jQuery('#profile-passports').append(newCardItem('passport',d['Passport_Visa__x'][i]));
					}
				}
				
				if(!!d && d['Frequent_Travel__x']) {
					for ( i in d['Frequent_Travel__x'] ) {
						jQuery('#profile-passports').append(newCardItem('freq',d['Frequent_Travel__x'][i]));
					}
				}
				
				break;
				
			case 'Account':
				d = user.Account;
				break;
				
			case 'Membership':
				d = user.Membership;
				break;
		}
		z = new String(z).split(',');
		jQuery(f).find('.loading').remove();
		jQuery(f).parent().find('.hide-until-load').removeClass('hide-until-load');
		if(!d) return;
		for(i in z) {
			var k = jQuery.trim(z[i]);
			if(d.hasOwnProperty(k)) {
				jQuery(f).find('#field-'+d[k].name).remove();
				jQuery(f).find('.card-save-button').before(makeInput(y,d[k],!p));
			}
		}
		
		/*
		jQuery('<button></button>').attr({
			'type':'submit',
			'value':'Submit'
		}).html('Submit').appendTo(f);
		*/
		
		jQuery(f).removeClass('needs-reload');
	});
	
	jQuery.scrollTo('#account-bar',300,{offset:-25});
}

/**
 * Returns a form element for a given field object and prefix
 * @param p string. ID prefix
 * @param l Object. The field to create an form element for
 * @param t Boolean.  Whether or not the field should be text only and not changable.
 * @returns The form element.
 */
function makeInput(p,l,t) {
	var i=1,y,s,o,e,d=p+'-'+l['name'],f;

	while(jQuery('#'+d).length>0) {
		i++;
		d = p + '-' + l['name']+'-'+i;
	}
	i = 1;
	f = 'field-' + d;
	while(jQuery('#'+f).length>0) {
		i++;
		f = 'field-' + d + '-' + i;
	}

	var neverShow = ['Id','Contact__c','AccountId'];
	
	if ( jQuery.inArray( l['name'], neverShow ) >= 0 ) {
		// Never show ID fields
		l['type'] = 'hidden';
	}
	
	o = jQuery('<div></div>').addClass('input-field field clearfix auto-created-field').attr('id',f);

	e = l['type'];
	if(t) {
		switch(e) {
			case 'select':
			case 'country':
			case 'state':
			case 'multipicklist':
			case 'picklist':
				e = 'text';
		}
	}
	
	switch(e) {
		case 'boolean':
			s = jQuery('<input></input>')
				.prop({
					'disabled':( ! l['updateable'] ),
					'checked':l['value']
				})
				.attr({
					'id':d,
					'name':l['name'],
					'maxlength':l['length'],
					'type':'checkbox'
			});
			if(t) {
				s.prop('disabled',true);
			}
			break;
			
		case 'textarea':
			s = jQuery('<textarea></textarea>')
			.prop('disabled',( ! l['updateable'] ) )
			.attr({
				'id':d,
				'name':l['name'],
				'maxlength':l['length']
			});
			
			if(l['value']) {
				jQuery(s).val(l['value']);
			} else if (l['defaultValue']) {
				jQuery(s).val(l['defaultValue']);
			}
			
			if(t) {
				s.prop({'disabled':true,'readonly':true});
			}
			
			break;
			
		case 'multipicklist':
			s = jQuery('<div></div>')
				.addClass('checkboxes clearfix')
				.attr({'id':d});
			
			for ( i in l['picklistValues'] ) {
				var w = jQuery('<div></div>').addClass('checkbox-item');
				y = l['picklistValues'][i];
				if ( ! y['active'] ) 
					continue;
				
				var chkbox = jQuery('<input></input>').attr({
					'name':l['name'],
					'type':'checkbox'
				});
				
				if(l['value'] == y['value']) {
					chkbox.prop({'checked':true});
				}
				
				var label = jQuery('<div></div>')
					.addClass('checkbox-label')
					.html(y['label']);
				
				jQuery('<label></label>')
					.append(chkbox)
					.append(label)
					.addClass('checkbox')
					.attr({
						'value':y['value'],
						'validfor':y['validFor']
					})
					.appendTo(w);
				w.appendTo(s);
			}
			break;
		
		case 'select':
		case 'country':
		case 'picklist':
			s = jQuery('<select></select>')
				.attr({
					'id':d,
					'name':l['name']
				})
				.prop('disabled',( ! l['updateable'] ) );
			
			if(l['controllingField']) {
				s.attr('controlfield',l['controllingField']);
			}
			
			if ( l['nillable'] ) {
				jQuery('<option></option>').html('Choose an option...').attr('value','').appendTo(s);
			}
			
			for ( i in l['picklistValues'] ) {
				y = l['picklistValues'][i];
				if ( ! y['active'] ) 
					continue;
				
				jQuery('<option></option>')
					.html(y['label'])
					.attr({
						'value':y['value'],
						'validfor':y['validFor']
					})
					.appendTo(s);
			}
			
			if(l['value']) {
				jQuery(s).val(l['value']);
			} else if (l['defaultValue']) {
				jQuery(s).val(l['defaultValue']);
			}

			break;
			
		case 'date':
			s = jQuery('<input></input>')
				.prop('disabled',( ! l['updateable'] ) )
				.attr({
					'id':d,
					'name':l['name'],
					'type':(l['type']=='hidden'?'hidden':'text'),
					'picker':'date'
			});
		
			if(l['value']) {
				if(Array.isArray(l['value'])) {
					jQuery(s).val(l['value'][0]);
				} else {
					jQuery(s).val(l['value']);
				}
			} else if (l['defaultValue']) {
				jQuery(s).val(l['defaultValue']);
			}

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			} else {
				var args = {
						dateFormat:"yy-mm-dd",
						changeYear:true,
						changeMonth:true
				};
				if(l['name'] == 'Birthdate') {
					var year = new Date().getFullYear();
					var minyear = Math.max(1900,year - 125);
					args.yearRange = ''+minyear+':'+year;
				}
				if(jQuery(s).val()) {
					args.defaultDate = new Date(jQuery(s).val());
				}
				jQuery(s).datepicker(args);
			}

			break;
			
		default:
			s = jQuery('<input></input>')
				.prop('disabled',( ! l['updateable'] ) )
				.attr({
					'id':d,
					'name':l['name'],
					'maxlength':l['length'],
					'type':(l['type']=='hidden'?'hidden':'text')
			});
		
			if(l['value']) {
				if(Array.isArray(l['value'])) {
					jQuery(s).val(l['value'].join(', '));
				} else {
					jQuery(s).val(l['value']);
				}
			} else if (l['defaultValue']) {
				jQuery(s).val(l['defaultValue']);
			}

			if(t) {
				s.prop({'disabled':true,'readonly':true});
			}

			break;
	}
	
	jQuery(s).appendTo(o);
	if( l['type'] != 'hidden' ) {
		jQuery('<label></label>').html(l['label']).attr({
			'for':d
		}).appendTo(o);
	} else {
		o.addClass('hidden');
	}
	return o;
}