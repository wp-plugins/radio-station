/* --------------------- */
/* Radio Station ScriptS */
/* --------------------- */

/* Smooth Scrolling */
function radio_scroll_to(id) {
	elem = document.getElementById(id);
	var jump = parseInt((elem.getBoundingClientRect().top - 50) * .2);
	document.body.scrollTop += jump;
	document.documentElement.scrollTop += jump;
	if (!elem.lastjump || elem.lastjump > Math.abs(jump)) {
		elem.lastjump = Math.abs(jump);
		setTimeout(function() { radio_scroll_to(id);}, 100);
	} else {elem.lastjump = null;}
}

/* Cookie Value Function */
/* since @2.3.2 */
radio_cookie = {
	set: function (name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = '; expires=' + date.toUTCString();
		} else {var expires = '';}
		document.cookie = 'radio_' + name + '=' + JSON.stringify(value) + expires + '; path=/';
	},
	get : function(name) {
		var nameeq = 'radio_' + name + '=', ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1,c.length);
				if (c.indexOf(nameeq) == 0) {
					return JSON.parse(c.substring(nameeq.length, c.length));
				}
			}
		}	
		return null;
	},
	delete : function(name) {
		setCookie('radio_' + name, "", -1);
	}
}

/* Debounce Delay Callback */
var radio_resize_debounce = (function () {
	var debounce_timers = {};
	return function (callback, ms, uniqueId) {
		if (!uniqueId) {uniqueId = "nonuniqueid";}
		if (debounce_timers[uniqueId]) {clearTimeout (debounce_timers[uniqueId]);}
		debounce_timers[uniqueId] = setTimeout(callback, ms);
	};
})();

