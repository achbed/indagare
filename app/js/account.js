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
	user.isPrimaryContact = null;
}
var getting = {};

/*
this.tabber1 = new Yetii({
        id: 'tab-container',
        class: 'tab'
});
*/

jQuery(document).ready(function(){
	jQuery(document).on('change','#contactselect',function(){
		contactSelectionChange();
	});
	jQuery(document).on('click','.expand-button>a',function(e){
		var p = jQuery(e.target).parents('.bar-element,.detail-element'),
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
			jQuery.scrollTo('#account-bar');
		} else {
			p.addClass('collapsed');
			if(a) {
				jQuery(a).addClass('collapsed');
			}
		}
	});
	
	getAccount();
});

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
		updateForms();
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
	jQuery('#contactselect').val( user.Contacts[first].Id.value ).trigger('change');
}

function isPrimaryContact() {
	if(user.isPrimaryContact) {
		return true;
	}
	if(user.isPrimaryContact === false) {
		return false;
	}
	var c;
	for(c in user.Contacts) {
		if(user.Contacts[c].IsCurrentUser__x.value) {
			user.isPrimaryContact = user.Contacts[c].Primary_Contact__c.value;
			return user.isPrimaryContact;
		}
	}
	return null;
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


function updateForms() {
	jQuery('[form-fields]').filter('.needs-reload').each(function(x,f){
		var i, 
			p = isPrimaryContact(),
			d = null,
			y = jQuery(f).attr('id'),
			z = jQuery(f).attr('form-fields'),
			a = jQuery(f).attr('form-object');
		if(!y||!y.length) return;
		switch(a) {
			case 'Contact':
				d = contactData(jQuery(f).attr('form-record-id'));
				p = p || d.IsCurrentUser__x.value;
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
		jQuery(f).find('.hide-until-load').removeClass('hide-until-load');
		if(!d) return;
		for(i in z) {
			var k = jQuery.trim(z[i]);
			if(d.hasOwnProperty(k)) {
				jQuery(f).find('#field-'+d[k].name).remove();
				jQuery(f).append(makeInput(y,d[k],!p));
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
}

/**
 * Returns a form element for a given field object and prefix
 * @param p string. ID prefix
 * @param l Object. The field to create an form element for
 * @param t Boolean.  Whether or not the field should be text only and not changable.
 * @returns The form element.
 */
function makeInput(p,l,t) {
	var i,y,s,o,e;
	
	if ( l['name'] == 'Id' ) {
		// Never show ID fields
		l['type'] = 'hidden';
	}
	
	o = jQuery('<div></div>').addClass('input-field field clearfix').attr('id','field-'+l['name']);
	if( l['type'] != 'hidden' ) {
		jQuery('<label></label>').html(l['label']).attr({
			'for':p+l['name']
		}).appendTo(o);
	} else {
		o.addClass('hidden');
	}

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
					'id':p+l['name'],
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
				'id':p+l['name'],
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
		
		case 'select':
		case 'country':
		case 'multipicklist':
		case 'picklist':
			s = jQuery('<select></select>')
				.prop('disabled',( ! l['updateable'] ) )
				.attr({
					'id':p+l['name'],
					'name':l['name']
			});
			if(l['controllingField']) {
				s.attr('controlfield',l['controllingField']);
			}
			
			if(l['type'] == 'multipicklist') {
				s.prop('multiple', true);
			}
			
			if(l['nillable'] && (l['type'] != 'multipicklist')) {
				jQuery('<option></option>').html('Choose an option...').attr('value','').appendTo(s);
			}
			for ( i in l['picklistValues'] ) {
				y = l['picklistValues'][i];
				if ( ! y['active'] ) 
					continue;
				jQuery('<option></option>').html(y['label']).attr({
					'value':y['value'],
					'validfor':y['validFor']
				}).appendTo(s);
			}
			
			if(l['value']) {
				jQuery(s).val(l['value']);
			} else if (l['defaultValue']) {
				jQuery(s).val(l['defaultValue']);
			}

			break;
			
		default:
			s = jQuery('<input></input>')
				.prop('disabled',( ! l['updateable'] ) )
				.attr({
					'id':p+l['name'],
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
	return o;
}