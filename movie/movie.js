if (notSet(window.Movie)) { var Movie = {}; }null;
var pexnidi = {};

function isPektis() {
	return(isSet(window.pektis) && isSet(pektis.login));
}

Movie.nextFrame = null;

Movie.clearNextFrame = function() {
	if (isSet(Movie.nextFrame)) {
		clearTimeout(Movie.nextFrame);
	}
	Movie.nextFrame = null;
	Movie.playing(false);
};

Movie.selectDianomi = function(d) {
	Movie.clearNextFrame();
	if (notSet(Movie.trapezi)) { return; }
	if (isSet(Movie.dianomi) && isSet(d) && (Movie.dianomi == d)) {
		Movie.pexeDianomi();
		return;
	}

	var href = globals.server + 'movie/index.php?trapezi=' + uri(Movie.trapezi);
	if (Movie.dbg) { href += '&debug=yes'; }
	if (isSet(d)) {
		if (d > 0) { href += '&dianomi=' + uri(d); }
		else { href += '&enarxi=yes'; }
	}
	window.location.href = href;
};

Movie.pexeDianomi = function() {
	if (notSet(Movie.dianomi)) { return; }
	Movie.Controls.play();
};

Movie.tzogosDefault = true;

Movie.tzogosDefaultOnOff = function(img) {
	Movie.tzogosDefault = !Movie.tzogosDefault;
	Movie.showTzogosDefault(img);
	Movie.keepMovieSettings();
};

Movie.showTzogosDefault = function(img) {
	if (notSet(img)) { img = getelid('tzogosDflt'); }
	if (notSet(img)) { return; }
	img.style.display = 'inline';
	if (Movie.tzogosDefault) {
		img.src = globals.server + 'images/trapoula/tzogos.png';
		img.title = 'Τζόγος κλειστός';
	}
	else {
		img.src = globals.server + 'images/movie/tzogosAniktos.png';
		img.title = 'Τζόγος ανοικτός';
	}
};

Movie.tzogosAniktos = null;

Movie.tzogosOnOff = function(div, fila) {
	if (notSet(div)) { return; }
	if (notSet(Movie.tzogosAniktos)) { return; }
	if (isSet(fila)) {
		div.filaHTML = '<img class="movieFilaSiraIcon" src="' + globals.server +
			'images/trapoula/' + fila.substr(0, 2) + '.png" alt="" ' +
			'style="left: 2.1cm; top: 0.5cm;" />' +
			'<img class="movieFilaSiraIcon" src="' + globals.server +
			'images/trapoula/' + fila.substr(2, 2) + '.png" alt="" ' +
			'style="left: 4.2cm; top: 0.5cm; padding-left: 0.2cm;" />';
	}
	if (Movie.tzogosAniktos) {
		var html = div.filaHTML;
		div.title = 'Κλείσιμο τζόγου';
	}
	else {
		html = '<img class="movieTzogosIcon" src="' + globals.server +
			'images/trapoula/tzogos.png" alt="" />';
		div.title = 'Άνοιγμα τζόγου';
	}
	div.innerHTML = html;
	Movie.tzogosAniktos = !Movie.tzogosAniktos;
};

Movie.dioxeTzogo = function() {
	sviseNode(getelid('tzogos'));
	Movie.tzogosAniktos = null;
};

Movie.miraseFila = function() {
	Movie.cursor = null;
	Movie.reset();
	if (notSet(Movie.kinisi)) { return; }
	if (Movie.kinisi.length <= 0) { return; }
	if (Movie.kinisi[0].idos != 'ΔΙΑΝΟΜΗ') { return; }
	var x = Movie.kinisi[0].data.split(':');
	if (x.length != 4) {
		mainFyi(Movie.kinisi[0].data + ': λανθασμένα στοιχεία διανομής');
		return;
	}

	for (var i = 1; i <= 3; i++) {
		var p = getelid('filaArea' + i);
		if (isSet(p)) {
			var html = Movie.filaHTML(Pexnidi.spaseFila(x[i]));
			p.innerHTML = html;
		}
	}

	Movie.tzogosAniktos = !Movie.tzogosDefault;
	Movie.tzogosOnOff(getelid('tzogos'), x[0]);

	Movie.cursor = 0;
	Movie.displayEpomenos();
};

Movie.mavroKokino = {
	'S':	'M',
	'C':	'M',
	'D':	'K',
	'H':	'K'
};

Movie.filaHTML = function(fila) {
	var html = '';
	if (fila.length <= 0) { return html; }

	html = '<div class="movieFilaXeri">';
	var tzogos = (fila.length > 10);
	var proto = ' style="margin-left: 0px;"';
	var prevXroma = '';
	var prevMavroKokino = '';
	for (var i = 0; i < fila.length; i++) {
		html += '<div class="movieFilaSira';
		if (tzogos) { html += ' movieFilaSiraSteno movieFiloSteno'; }
		html += '"' + proto + '>';
		proto = '';
		html += '<img class="movieFilaSiraIcon';
		if (tzogos) { html += ' movieFiloSteno'; }
		var curXroma = fila[i].substr(0, 1);
		if (curXroma != prevXroma) {
			prevXroma = curXroma;
			var curMavroKokino = Movie.mavroKokino[curXroma];
			if (curMavroKokino == prevMavroKokino) {
				html += ' movieFiloDiaxor' + curMavroKokino;
			}
			else {
				prevMavroKokino = curMavroKokino;
			}
		}
		html += '" src="' + globals.server + 'images/trapoula/' +
			fila[i] + '.png" alt="" />';
		html += '</div>';
	}
	html += '</div>';
	return html;
};

Movie.ipopsifiaDianomi = function(div, nai) {
	if (nai) {
		div.prevWeight = div.style.fontWeight;
		div.style.fontWeight = 'bold';
	}
	else {
		try {
			div.style.fontWeight = div.prevWeight;
		} catch (e) {
			div.style.fontWeight = 'normal';
		}
	}
};

Movie.realTime = true;
Movie.timeScale = 1000;

Movie.setRealTime = function(yes) {
	Movie.realTime = yes;
	Movie.timeScale = 1000;
	Movie.showTimeSettings();
	Movie.keepMovieSettings();
Movie.debug((Movie.realTime ? 'Real time' : 'Fixed time') + ', step = ' + Movie.timeScale);
};

Movie.calcTimeStep = function() {
	if (Movie.timeScale > 2000) { return 500; }
	if (Movie.timeScale > 1000) { return 200; }
	if (Movie.timeScale > 500) { return 100; }
	return 50;
};

Movie.slower = function() {
	if ((Movie.timeScale += Movie.calcTimeStep()) >= 3000) {
		Movie.timeScale = 3000;
	}
	Movie.showTimeSettings();
	Movie.keepMovieSettings();
Movie.debug((Movie.realTime ? 'Real time' : 'Fixed time') + ', step = ' + Movie.timeScale);
};

Movie.faster = function() {
	if ((Movie.timeScale -= Movie.calcTimeStep()) < 50) {
		Movie.timeScale = 50;
	}
	Movie.showTimeSettings();
	Movie.keepMovieSettings();
Movie.debug((Movie.realTime ? 'Real time' : 'Fixed time') + ', step = ' + Movie.timeScale);
};

Movie.keepMovieSettings = function() {
	var req = new Request('pektis/setMovie');
	req.xhr.onreadystatechange = function() {
		Movie.keepMovieSettingsCheck(req);
	};
	var params = 'time=' + (Movie.realTime ? 'REAL' : 'METRONOME');
	params += '&scale=' + Movie.timeScale;
	params += '&tzogos=' + (Movie.tzogosDefault ? 'CLOSED' : 'OPEN');
	req.send(params);
};

Movie.keepMovieSettingsCheck = function(req) {
	if (req.xhr.readyState != 4) { return; }
	var rsp = req.getResponse();
	mainFyi(rsp);
};

Movie.getMovieSettings = function() {
	var req = new Request('pektis/getMovie', false);
	req.send();
	if (req.xhr.readyState != 4) { return; }
	var rsp = req.getResponse();
	try {
		eval(rsp);
		Movie.showTimeSettings();
		Movie.showTzogosDefault();
	} catch (e) {
		mainFyi(rsp);
	}
};

Movie.entopiseDianomi = function() {
	for (var i = 0; i < Movie.dianomes.length; i++) {
		if (Movie.dianomi == Movie.dianomes[i]) {
			return(i);
		}
	}
	return null;
};

Movie.debug = function(s, newline) {
	var x = getelid('debugArea');
	if (notSet(x)) { return; }
	if (notSet(newline)) { newline = true; }
	var y = document.createElement(newline ? 'div' : 'span');
	y.innerHTML = s;
	x.appendChild(y);
	if (isSet(x.scrollHeight)) { x.scrollTop = x.scrollHeight; }
};

Movie.playing = function(yes) {
	var x = getelid('playing');
	if (notSet(x) || notSet(x.style) || notSet(x.style.display)) { return; }
	if (yes) {
		x.style.display = 'inline';
	}
	else {
		x.style.display = 'none';
	}
};

Movie.showTimeSettings = function() {
	if (Movie.realTime) {
		var on = getelid('roloi');
		var off = getelid('metronomos');
	}
	else {
		off = getelid('roloi');
		on = getelid('metronomos');
	}
	if (isSet(on)) { on.style.borderColor = '#FF6666'; }
	if (isSet(off)) { off.style.borderColor = '#8CAA88'; }

	off = getelid('slow');
	if (isSet(off)) { off.style.borderColor = '#8CAA88'; }
	off = getelid('fast');
	if (isSet(off)) { off.style.borderColor = '#8CAA88'; }
	if (Movie.timeScale < 350) {
		on = getelid('fast');
		var color = '#FFFF33';
	}
	else if (Movie.timeScale < 550) {
		on = getelid('fast');
		color = '#FF6666';
	}
	else if (Movie.timeScale < 950) {
		on = getelid('fast');
		color = '#335C33';
	}
	else if (Movie.timeScale > 2250) {
		on = getelid('slow');
		color = '#FFFF33';
	}
	else if (Movie.timeScale > 1550) {
		on = getelid('slow');
		color = '#FF6666';
	}
	else if (Movie.timeScale > 1050) {
		on = getelid('slow');
		color = '#335C33';
	}
	else {
		on = null;
	}
	if (isSet(on)) { on.style.borderColor = color; }
};

Movie.Controls = {};

Movie.Controls.play = function(vima) {
	Movie.clearNextFrame();
	if (notSet(Movie.dianomi)) {
		Movie.Controls.dianomi(1);
		return;
	}

	if (notSet(Movie.cursor)) {
		Movie.miraseFila();
		return;
	}

	if (notSet(vima)) {
		vima = 1;
		var auto = true;
	}
	else {
		auto = false;
	}

	Movie.cursor += vima;
	if (Movie.cursor >= Movie.kinisi.length) {
		Movie.Controls.dianomi(1);
		return;
	}
	if (Movie.cursor < 0) {
		Movie.Controls.dianomi(-1);
		return;
	}

	Movie.display();

	if (Movie.cursor >= Movie.kinisi.length - 1) {
		return;
	}

	var pause = Movie.showEpomeno();
	if (notSet(auto)) { auto = true; }
	if (auto) {
		Movie.nextFrame = setTimeout(function() {
			Movie.Controls.play();
		}, pause);
		Movie.playing(true);
	}
};

Movie.display = function() {
	Movie.reset();
	for (var i = 0; i <= Movie.cursor; i++) {
		var thesi = Movie.kinisi[i].pektis;
		var idos = Movie.kinisi[i].idos;
		var data = Movie.kinisi[i].data;
		Movie.debugKinisi(i, idos, thesi, data);
		switch (Movie.kinisi[i].idos) {
		case 'ΔΙΑΝΟΜΗ':
			Movie.processDianomi(i, thesi, data);
			break;
		case 'ΔΗΛΩΣΗ':
			Movie.processDilosi(i, thesi, data);
			break;
		case 'ΤΖΟΓΟΣ':
			Movie.processTzogos(i, thesi, data);
			break;
		case 'ΑΓΟΡΑ':
			Movie.processAgora(i, thesi, data);
			break;
		case 'ΠΛΗΡΩΜΗ':
			Movie.processPliromi(i, thesi, data);
			break;
		}
	}

	Movie.displayTzogos();
	for (i = 1; i <= 3; i++) {
		Movie.displayPektis(i);
		Movie.displayKapikia(i);
	}
	Movie.displayIpolipo();
	Movie.displayEpomenos();
};

Movie.displayPektis = function(thesi) {
	var x = getelid('filaArea' + thesi);
	if (isSet(x)) {
		var html = Movie.filaHTML(Pexnidi.spaseFila(Movie.Partida.fila[thesi]));
		x.innerHTML = html;
	}

	var x = getelid('dilosi' + thesi);
	if (notSet(x)) { return; }

	if (thesi == Movie.Partida.tzogadoros) {
		Movie.displayAgora(x);
		return;
	}

	var html = Movie.Partida.paso[thesi];
	if (isSet(x)) {
		x.setAttribute('class', 'movieDilosi movieDilosi' + thesi);
		switch (Movie.Partida.dilosi[thesi]) {
		case '':
			break;
		default:
			if (html != '') {
				x.setAttribute('class', x.getAttribute('class') +
					' protasiAgoraOxi');
			}
			html = Pexnidi.xromaBazesHTML(Movie.Partida.dilosi[thesi]);
			break;
		}
	}
	x.innerHTML = html;
};

Movie.displayTzogos = function() {
	var x = getelid('tzogos');
	if (notSet(x)) { return; }
	if (Movie.Partida.tzogos) {
		x.style.display = 'inline';
	}
	else {
		x.style.display = 'none';
	}
};

Movie.displayIpolipo = function() {
	var x = getelid('ipolipo');
	if (notSet(x)) { return; }
	x.innerHTML = Movie.ipolipo - (Movie.Partida.ipolipo / 10);
};

Movie.displayEpomenos = function() {
	for (var i = 1; i <= 3; i++) {
		var x = getelid('pektisMain' + i);
		if (notSet(x)) { continue; }
		x.setAttribute('class', 'moviePektisMain moviePektisMain' + i);
	}

	var epomenos = null;
	for (i = Movie.cursor + 1; i < Movie.kinisi.length; i++) {
		switch (Movie.kinisi[i].idos) {
		case 'ΔΗΛΩΣΗ':
		case 'ΣΥΜΜΕΤΟΧΗ':
		case 'ΦΥΛΛΟ':
		case 'ΤΖΟΓΟΣ':
		case 'ΑΓΟΡΑ':
		case 'ΜΠΑΖΑ':
		case 'CLAIM':
			epomenos = Movie.kinisi[i].pektis;
			break;
		}
		if (isSet(epomenos)) {
			break;
		}
	}
	if (notSet(epomenos)) { return;}
	var x = getelid('pektisMain' + epomenos);
	if (notSet(x)) { return; }
	x.setAttribute('class', 'moviePektisMain moviePektisMain' + epomenos + ' epomenos');
};

Movie.displayAgora = function(div) {
	div.setAttribute('class', div.getAttribute('class') + ' dilosiAgora');
	var html = Pexnidi.xromaBazesHTML(Movie.Partida.agora);
	div.innerHTML = html;
};

Movie.reset = function() {
	Movie.Partida = {};
	Movie.Partida.agora = null;
	Movie.Partida.tzogadoros = 0;
	Movie.Partida.fila = [];
	Movie.Partida.tzogos = true;
	Movie.Partida.dilosi = ['', '', '', ''];
	Movie.Partida.paso = ['', '', '', ''];
	Movie.Partida.simetoxi = ['', '', '', ''];
	Movie.Partida.bazes = [0, 0, 0, 0];
	Movie.Partida.pliromi = [0, 0, 0, 0];
	Movie.Partida.ipolipo = 0;

	Movie.debug('<hr />');
	for (var i = 0; i <= 3; i++) {
		var p = getelid('dilosi' + i);
		if (isSet(p)) {
			p.setAttribute('class', 'movieDilosi movieDilosi' + i);
			p.innerHTML = '';
		}
	}
};

Movie.debugKinisi = function(i, idos, thesi, data) {
	Movie.debug(i + '. ' + idos + ': Θέση: ' + thesi +
		'<div class="movieDebugData">' + data + '</span>');
};

Movie.processDianomi = function(i, thesi, data) {
	Movie.Partida.fila = data.split(':');
};

Movie.processDilosi = function(i, thesi, data) {
	if (data.match(/^P/)) {
		Movie.Partida.paso[thesi] = 'ΠΑΣΟ';
	}
	else {
		Movie.Partida.dilosi[thesi] = data;
	}
};

Movie.processTzogos = function(thesi, data) {
	Movie.Partida.tzogos = false;
};

Movie.processAgora = function(i, thesi, data) {
	var x = data.split(':');
	if (x.length != 2) { return; }
	Movie.Partida.tzogadoros = thesi;
	Movie.Partida.agora = x[0];
};

Movie.processPliromi = function(i, thesi, data) {
	var x = data.split(':');
	if (x.length != 7) { return; }

	for (var i = 1; i <= 3; i++) {
		var kasa = parseInt(x[(2 * i) - 1]);
		var kapikia = parseInt(x[2 * i]);
		if (kasa != 0) {
			Movie.Partida.ipolipo += kasa;
			kasa = parseInt(kasa / 3);
			Movie.Partida.pliromi[i] += kasa * 2;
			for (var j = 1; j <= 3; j++) {
				if (j != i) {
					Movie.Partida.pliromi[j] -= kasa;
				}
			}
		}
		Movie.Partida.pliromi[i] += parseInt(kapikia);
	}
};

Movie.displayKapikia = function(thesi) {
	var x = getelid('kapikia' + thesi);
	if (notSet(x)) { return; }
	var kapikia = Movie.kapikia[thesi] + Movie.Partida.pliromi[thesi];
	if (kapikia < 0) {
		x.setAttribute('class', 'kapikiaMion');
		x.style.paddingLeft = '0.2cm';
		x.innerHTML = kapikia;
	}
	else if (kapikia > 0) {
		x.setAttribute('class', 'kapikiaSin');
		x.style.paddingLeft = '0.2cm';
		x.innerHTML = '+' + kapikia;
	}
	else {
		x.setAttribute('class', '');
		x.style.paddingLeft = '0px';
		x.innerHTML = '';
	}
};

Movie.showEpomeno = function() {
	if (notSet(Movie.cursor) || (Movie.cursor >= (Movie.kinisi.length - 1))) {
		return 100;
	}

	var xronos = Movie.kinisi[Movie.cursor + 1].pote - Movie.kinisi[Movie.cursor].pote;
	if (Movie.realTime) {
		var pause = xronos * Movie.timeScale;
	}
	else {
		pause = Movie.timeScale;
	}

	var x = getelid('epomeno');
	if (notSet(x)) { return pause; }

	var n = Movie.kinisi.length - 2;
	while (n < 0) { n++; }

	var html = '';
	if (isSet(Movie.cursor) && (Movie.cursor > 0)) {
		html += '<span class="partidaInfoData" title="Τρέχουσα κίνηση">' +
			Movie.cursor + '</span> # ';
	}
	html += '<span class="partidaInfoData" title="Πλήθος κινήσεων">' + n + '</span>, ' +
		'<span class="partidaInfoData" title="Πραγματικός επόμενης κίνησης">' +
			xronos + '</span> ~ ' +
		'<span class="partidaInfoData" title="Χρόνος επόμενου frame">' +
			(pause / 1000) + '</span> sec';
	x.innerHTML = html;
	return pause;
};

Movie.Controls.pause = function() {
	if (isSet(Movie.nextFrame)) {
		Movie.clearNextFrame();
	}
	else {
		Movie.Controls.play();
	}
};

Movie.Controls.stop = function() {
	Movie.clearNextFrame();
};

Movie.Controls.prevDianomi = function() {
	Movie.clearNextFrame();
	if (isSet(Movie.cursor) && (Movie.cursor > 0)) {
		Movie.miraseFila();
	}
	else {
		Movie.Controls.dianomi(-1);
	}
	Movie.showEpomeno();
};

Movie.Controls.nextDianomi = function() {
	Movie.clearNextFrame();
	if (isSet(Movie.cursor) && (Movie.cursor > 0) &&
		(Movie.cursor < (Movie.kinisi.length -1))) {
		Movie.cursor = Movie.kinisi.length - 2;
		if (Movie.cursor > 0) { Movie.Controls.play(); }
		else { Movie.miraseFila(); }
	}
	else {
		Movie.Controls.dianomi(1);
	}
	Movie.showEpomeno();
};

Movie.Controls.dianomi = function(vima) {
	if (Movie.dianomes.length <= 0) {
		mainFyi('Δεν υπάρχουν διανομές!');
		return;
	}

	if (notSet(Movie.dianomi)) {
		if (isSet(vima) && (vima > 0)) {
			if (Movie.enarxi) {
				Movie.selectDianomi(Movie.dianomes[0]);
			}
			else {
				Movie.selectDianomi(0);
			}
		}
		else if (Movie.enarxi) {
			Movie.selectDianomi();
		}
		else {
			Movie.selectDianomi(Movie.dianomes[Movie.dianomes.length - 1]);
		}
		return;
	}

	var i = Movie.entopiseDianomi();
	if (notSet(i)) {
		mainFyi('Ακαθόριστη διανομή!');
		return;
	}

	if (isSet(vima) && (vima > 0)) {
		i++;
		if (i >= Movie.dianomes.length) {
			Movie.selectDianomi();
			return;
		}
	}
	else {
		i--;
		if (i < 0) {
			Movie.selectDianomi(0);
			return;
		}
	}
	Movie.selectDianomi(Movie.dianomes[i]);
};

window.onload = function() {
	init();
	if (isSet(Movie.dianomiSpot)) {
		window.location.hash = '#dianomi' + Movie.dianomiSpot;
	}

	Movie.getMovieSettings();
	if (isSet(Movie.dianomi)) {
		Movie.miraseFila();
	}

	for (var i = 1; i <= 3; i++) {
		var x = getelid('pektis' + i);
		if (isSet(x)) {
			x.title = "Αλλαγή θέσης"
		}
	}

	Movie.showEpomeno();
};
