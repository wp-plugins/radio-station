/* ------------------ */
/* Radio Clock Script */
/* ------------------ */

/* Convert Date Time to Time String */
function radio_time_string(datetime, format, seconds) {

	h = datetime.getHours();
	m = datetime.getMinutes();
	if (m < 10) {m = '0'+m;}

	if (seconds) {
		s = datetime.getSeconds();
		if (s < 10) {s = '0'+s;}
	}

	if (format == 12) {
		if ( h < 12 ) {mer = radio.units.am;}
		if ( h == 0 ) {h = '12';}
		if ( h > 11 ) {mer = radio.units.pm;}
		if ( h > 12 ) {h = h - 12;}
	} else {
		mer = '';
		if ( h < 10 ) {h = '0'+h;}
	}

	timestring = '<span class="rs-hour">'+h+'</span>';
	timestring += '<span class="rs-sep">:</span>';
	timestring += '<span class="rs-minutes">'+m+'</span>';
	if (seconds) {
		timestring += '<span class="rs-sep">:</span>';
		timestring += '<span class="rs-seconds">'+s+'</span>';
	}
	if (mer != '') {timestring += ' <span class="rs-meridiem">'+mer+'</span>';}
	return timestring;
}

/* Convert Date Time to Date String */
function radio_date_string(datetime, day, date, month) {
	datestring = '';
	if (day != '') {
		d = datetime.getDay();
		if (day == 'short') {datestring = radio.labels.sdays[d];}
		else {datestring += radio.labels.days[d];}
	}
	if (date) {
		datestring += ' '+datetime.getDate();
	}
	if (month != '') {
		m = datetime.getMonth();
		if (month == 'short') {datestring += ' '+radio.labels.smonths[m];}
		else {datestring += ' '+radio.labels.months[m];}
	}
	return datestring;
}

/* Update Current Time Clock */
function radio_clock_date_time(init) {

	/* user datetime / timezone */
	userdatetime = new Date();
	if (typeof jstz == 'function') {userzone = jstz.determine().name();}
	else {userzone = Intl.DateTimeFormat().resolvedOptions().timeZone;}
	userzone = userzone.replace('/',', ');
	userzone = userzone.replace('_',' ');

	/* user timezone offset */
	useroffset  = -(userdatetime.getTimezoneOffset());
	houroffset = parseInt(useroffset);
	if (houroffset == 0) {userzone += ' [UTC]';}
	else {
		houroffset = houroffset / 60;
		if (houroffset > 0) {userzone += ' [UTC+'+houroffset+']';}
		else {userzone += ' [UTC'+houroffset+']';}
	}

	/* server datetime / offset */
	serverdatetime = new Date();
	serveroffset = ( -(useroffset) * 60) + radio.timezone.offset;
	serverdatetime.setTime(userdatetime.getTime() + (serveroffset * 1000) );

	/* server timezone */
	serverzone = '';
	if (typeof radio.timezone.location != 'undefined') {
		serverzone = radio.timezone.location;
		serverzone = serverzone.replace('/',', ');
		serverzone = serverzone.replace('_',' ');
	}
	if (typeof radio.timezone.code != 'undefined') {
		serverzone += ' ['+radio.timezone.code+']';
	}

	/* loop clock instances */
	clock = document.getElementsByClassName('radio-station-clock');
	for (i = 0; i < clock.length; i++) {
		if (clock[i]) {
			classes = clock[i].className;
			seconds = false; day = ''; date = false; month = ''; zone = false;
			if (classes.indexOf('format-24') > -1) {format = 24;} else {format = 12;}
			if (classes.indexOf('seconds') > -1) {seconds = true;}
			if (classes.indexOf('day') > -1) {
				if (classes.indexOf('day-short') > -1) {day = 'short';} else {day = 'full';}
			}
			if (classes.indexOf('date') > -1) {date = true;}
			if (classes.indexOf('month') > -1) {
				if (classes.indexOf('month-short') > -1) {month = 'short';} else {month = 'full';}
			}
			if (classes.indexOf('zone') > -1) {zone = true;}
			servertime = radio_time_string(serverdatetime, format, seconds);
			serverdate = radio_date_string(serverdatetime, day, date, month);
			usertime = radio_time_string(userdatetime, format, seconds);
			userdate = radio_date_string(userdatetime, day, date, month);

			/* loop server / user clocks */
			clocks = clock[i].children;
			for (j = 0; j < clocks.length; j++) {
				if (clocks[j]) {
					classes = clocks[j].className;

					/* update server clock */
					if (classes.indexOf('radio-station-server-clock') > -1) {
						divs = clocks[j].children;
						for (k = 0; k < divs.length; k++) {
							if (divs[k].className == 'radio-server-time') {divs[k].innerHTML = servertime;}
							if (divs[k].className == 'radio-server-date') {divs[k].innerHTML = serverdate;}
							if (init && zone && (divs[k].className == 'radio-server-zone') ) {divs[k].innerHTML = serverzone;}
						}
					}

					/* update user clock */
					if (classes.indexOf('radio-station-user-clock') > -1) {
						divs = clocks[j].children;
						for (k = 0; k < divs.length; k++) {
							if (divs[k].className == 'radio-user-time') {divs[k].innerHTML = usertime;}
							if (divs[k].className == 'radio-user-date') {divs[k].innerHTML = userdate;}
							if (init && zone && (divs[k].className == 'radio-user-zone') ) {divs[k].innerHTML = userzone;}
						}
					}
				}
			}
		}
	}

	/* clock loop */
	setTimeout('radio_clock_date_time();', 1000);
	return true;
}

/* Start the Clock */
setTimeout('radio_clock_date_time(true);', 1000);