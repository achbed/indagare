/*
Yetii - Yet (E)Another Tab Interface Implementation
version 1.8
http://www.kminek.pl/lab/yetii/
Copyright (c) Grzegorz Wojcik
Code licensed under the BSD License:
http://www.kminek.pl/bsdlicense.txt
*/

function Yetii() {
	var self = this;

	self.defaults = {
        id: null,
        active: 1,
        interval: null,
        wait: null,
        persist: null,
        tabclass: 'tab',
        activeclass: 'active',
        callback: null,
        leavecallback: null
    };

	self.activebackup = null;
	self.changingHash = false;
	self.initComplete = false;

    self.getTabs = function() {
        var retnode = [];
        var elem = document.getElementById(self.defaults.id).getElementsByTagName('*');
        var regexp = new RegExp("(^|\\s)" + self.defaults.tabclass.replace(/\-/g, "\\-") + "(\\s|$)");
        for (var i = 0; i < elem.length; i++) {
            if (regexp.test(elem[i].className)) retnode.push(elem[i]);
        }
        return retnode;
    };

    self.show = function(t) {
    	if(self.changingHash) { self.changingHash = false; return; }
    	self.changingHash = false;
    	if(!t) {
    		self.defaults.active = (self.parseurl()) ? self.parseurl() : self.defaults.active;
    		if(!self.initComplete) {
	        	self.activebackup = self.defaults.active;
	    		t = self.defaults.active;
	    		self.initComplete = true;
    		}
    	}
        t = self.getTabNum(t);
        self.show_core(t);
    };
    
    self.show_core = function(number) {
        for (var i = 0; i < self.tabs.length; i++) {
            self.tabs[i].style.display = ((i+1)==number) ? 'block' : 'none';
            if ((i+1)==number) {
                self.addClass(self.links[i], self.defaults.activeclass);
                self.addClass(self.listitems[i], self.defaults.activeclass + 'li');
            } else {
                self.removeClass(self.links[i], self.defaults.activeclass);
                self.removeClass(self.listitems[i], self.defaults.activeclass + 'li');
            }
        }
        if (self.defaults.leavecallback && (number != self.activebackup)) self.defaults.leavecallback(self.defaults.active);
        self.activebackup = number;
        self.defaults.active = number;
        self.pushHistory(number);
        if (self.defaults.callback) self.defaults.callback(number);
    };
    
    self.pushHistory = function(n){
        var id = '#'+self.tabs[n-1].id+'-tab',
        	l=window.location.toString();
        if(window.location.hash == id) return;
        l = l.split('#')[0] + id
        if(history.pushState) {
            history.pushState(null, null, l);
        } else {
        	window.location.hash = id;
        }
        return;
    };

    self.rotate = function(interval) {
        self.show(self.defaults.active);
        self.defaults.active++;
        if (self.defaults.active > self.tabs.length) self.defaults.active = 1;
        if (self.defaults.wait) clearTimeout(self.timer2);
        self.timer1 = setTimeout(function(){
            self.rotate(interval);
        }, interval*1000);
    };

    self.next = function() {
        self.defaults.active++;
        if(self.defaults.active>self.tabs.length) self.defaults.active = 1;
        self.show(self.defaults.active);
    };

    self.previous = function() {
        self.defaults.active--;
        if(!self.defaults.active) self.defaults.active = self.tabs.length;
        self.show(self.defaults.active);
    };

    self.gup = function(name) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(window.location.href);
        if (results == null) return null;
        else return results[1];
    };
    
    self.guh = function() {
        var regexS = "#([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec(window.location.hash);
        if (results == null) return null;
        else return results[1];
    };

    self.parseurl = function() {
        var i=0,result = self.gup(self.defaults.id);
        if (result == null) result = self.guh();
        if (result == null) return null;
        return self.getTabNum(result);
    };
    
    self.getTabNum = function(name) {
    	var i=0,l=self.tabs.length;
        if (parseInt(name)) return parseInt(name);
        name=name.replace(/-tab$/,'');
        if (document.getElementById(name)) {
            for (;i<l;i++) {
                if (self.tabs[i].id == name) { 
                    return (i+1); 
                }
            }
        }
        return null;
    };

    self.createCookie = function(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    };

    self.readCookie = function() {
        var nameEQ = self.defaults.id + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    };

    self.hasClass = function(el, className){
        var classes = el.className.split(' ');
        for (var i = 0; i < classes.length; i++) {
            if (classes[i] == className) return true;
        }
        return false;
    };

    self.addClass = function(el, className){
        if (!self.hasClass(el, className)) el.className = (el.className + ' ' + className).replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');
    };

    self.removeClass = function(el, className){
        el.className = el.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1');
        el.className.replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');
    };
    
    self.getListItems = function() {
    	self.listitems = [];
    	self.links = [];
        var l = document.getElementById(self.defaults.id + '-nav').getElementsByTagName('li');
    	for(var i=0;i<l.length;i++) {
    		if(self.hasClass(l[i],'tab-nav-item')) {
    			self.listitems.push(l[i]);
    		}
    	}
    	for(var i=0;i<self.listitems.length;i++) {
    		var y = self.listitems[i].getElementsByTagName('a')[0];
			self.links.push( y );
    	}
    }
    

    /* init */
    for (var n in arguments[0]) {
    	self.defaults[n]=arguments[0][n];
    };

    self.getListItems();
    /*
    self.links = document.getElementById(self.defaults.id + '-nav').getElementsByTagName('a');
    self.listitems = document.getElementById(self.defaults.id + '-nav').getElementsByTagName('li');
    for(var i=0;i<self.listitems.)
    */
    
    self.tabs = self.getTabs();
    
    // Only do this at load time.
    if (self.defaults.persist && self.readCookie()) self.defaults.active = self.readCookie();
    
    // Doa a one-time change
    self.show();
    
    // Set up the event handling
    jQuery(window).on('hashchange',function(e){
    	var h = window.location.hash;
        var tab = h.replace(/.*#/,'').replace(/-tab$/,'');
    	self.show(tab);
    });
    
    if(!!Hammer) {
		var el = document.getElementById(self.defaults.id);
		
		var mc = new Hammer(el);
		mc.on("swipeleft", function(ev) {
			if ( jQuery(ev.target).is('select,input,textarea') ) {
				if( jQuery(ev.target).closest('form').hasClass('editing') ) {
					return;
				}
			}
		    self.next();
		}).on("swiperight", function(ev) {
			if ( jQuery(ev.target).is('select,input,textarea') ) {
				if( jQuery(ev.target).closest('form').hasClass('editing') ) {
					return;
				}
			}
		    self.previous();
		});
    }

    for (var i = 0; i < self.links.length; i++) {
        self.links[i].customindex = i+1;
        self.links[i].onclick = function(e){

            if (self.timer1) clearTimeout(self.timer1);
            if (self.timer2) clearTimeout(self.timer2);

            var t = e.target;
            var h = t.href;
            var tab = h.replace(/.*#/,'');
            self.show(tab);
            
            if (self.defaults.persist) self.createCookie(self.defaults.id, self.customindex, 0);

            if (self.defaults.wait) self.timer2 = setTimeout(function(){
                self.rotate(self.defaults.interval);
            }, self.defaults.wait*1000);

            return false;
        };
    }

    if (self.defaults.interval) self.rotate(self.defaults.interval);
};
