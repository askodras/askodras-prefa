var pexnidi = {};
var Astra = new function() {
	this.setHeight = function() {
		var wh = diathesimosXoros();
		if ((typeof(wh.h) != 'number') || (wh.h < 600)) { return; }

		var x = getelid('astraArea');
		if (notSet(x)) { return; }

		var h = (wh.h - 130) + 'px';
		x.style.height = h;
		x.style.minHeight = h;
		x.style.maxHeight =h;

		var x = getelid('dataArea');
		if (notSet(x)) { return; }

		var h = (wh.h - 180) + 'px';
		x.style.height = h;
		x.style.maxHeight =h;
	};

	var protiFora = true;

	this.getData = function() {
		var pektis = getelid('pektis');
		if (notSet(pektis)) {
			mainFyi('pektis: misssing input field');
			return false;
		}

		var apo = getelid('apo');
		if (notSet(apo)) {
			mainFyi('apo: misssing input field');
			return false;
		}

		var eos = getelid('eos');
		if (notSet(eos)) {
			mainFyi('eos: misssing input field');
			return false;
		}

		var partida = getelid('partida');
		if (notSet(partida)) {
			mainFyi('partida: misssing input field');
			return false;
		}

		var ico = getelid('searchIcon');
		if (ico) { ico.style.visibility = 'visible'; }

		var req = new Request('astra/getData');
		req.xhr.onreadystatechange = function() {
			getDataCheck(req, ico);
		};

		var params = '';
		if (protiFora) {
			protiFora = false;
		}
		else {
			params +=  'pektis=' + uri(pektis.value.trim());
			params +=  '&apo=' + uri(apo.value.trim());
			params +=  '&eos=' + uri(eos.value.trim());
			params +=  '&partida=' + uri(partida.value.trim());
		}

		req.send(params);
		return false;
	};

	function getDataCheck(req, ico) {
		if (req.xhr.readyState != 4) { return; }

		ico.style.visibility = 'hidden';
		rsp = req.getResponse();
		try {
			var dedomena = eval('(' + rsp + ')');
		} catch(e) {
			mainFyi(rsp);
		}

		if (notSet(dedomena) || notSet(dedomena.ok) ||
			notSet(dedomena.partida) || isSet(dedomena.error)) {
			if (isSet(dedomena) && isSet(dedomena.error)) { rsp = dedomena.error; }
			mainFyi('Λανθασμένα δεδομένα: ' + rsp);
			return;
		}

		var html = '';
		for (var i = 0; i < dedomena.partida.length; i++) {
			html += Astra.partidaHTML(dedomena.partida[i], i);
		}

		var x = getelid('dataArea');
		if (notSet(x)) { return; }
		x.innerHTML = html;
	};

	this.pektisHTML = function(pektis, kapikia) {
		var html = '<div class="astraPartidaPektis">';
		html += '<div class="astraOnoma">';
		html += (pektis != '' ? pektis : '&#8203;');
		html += '</div>';
		html += '<div class="astraKapikia';
		if (kapikia < 0) { html += ' astraMion'; }
		html += '">';
		html += (kapikia != 0 ? kapikia : '&#8203;');
		html += '</div>';
		html += '</div>';
		return html;
	};

	this.xronosHTML = function(xronos) {
		var html = '<div class="astraPartidaXronos">';
		html += xronos;
		html += '</div>';
		return html;
	};

	this.partidaHTML = function(partida, i) {
		var html = '';
		html += '<div class="astraPartida zebra' + (i % 2) +
			'" onclick="Astra.dianomiOnOff(' + partida.t + ');" ' +
			'title="Κλικ για εμφάνιση/απόκρυψη διανομών" ' +
			'onmouseover="Astra.epilogiPartidas(this);" ' +
			'onmouseout="Astra.apoepilogiPartidas(this);">';
		html += '<div class="astraPartidaKodikos">' + partida.t + '</div>'
		html += Astra.pektisHTML(partida.p1, partida.k1);
		html += Astra.pektisHTML(partida.p2, partida.k2);
		html += Astra.pektisHTML(partida.p3, partida.k3);
		html += Astra.xronosHTML(partida.x);
		html += '</div>';
		html += '<div id="t' + partida.t + '"></div>';
		return html;
	};

	this.epilogiPartidas = function(div) {
		div.OBC = div.style.backgroundColor;
		div.style.backgroundColor = '#FFFF33';
		div.style.fontWeight = 'bold';
	};

	this.apoepilogiPartidas = function(div) {
		div.style.backgroundColor = div.OBC;
		div.style.fontWeight = 'normal';
	};

	this.dianomiOnOff = function(trapezi) {
		var x = getelid('t' + trapezi);
		if (notSet(x)) { return; }

		if (x.innerHTML != '') {
			x.innerHTML = '';
			return;
		}

		var ico = getelid('searchIcon');
		if (ico) { ico.style.visibility = 'visible'; }

		var req = new Request('astra/getDianomi');
		req.xhr.onreadystatechange = function() {
			getDianomiCheck(req, ico, x, trapezi);
		};

		var params = 'trapezi=' + uri(trapezi);
		req.send(params);
		return false;
	};

	function getDianomiCheck(req, ico, div, trapezi) {
		if (req.xhr.readyState != 4) { return; }

		ico.style.visibility = 'hidden';
		rsp = req.getResponse();
		try {
			var dedomena = eval('(' + rsp + ')');
		} catch(e) {
			mainFyi(rsp);
		}

		if (notSet(dedomena) || notSet(dedomena.ok) ||
			notSet(dedomena.dianomi) || isSet(dedomena.error)) {
			if (isSet(dedomena) && isSet(dedomena.error)) { rsp = dedomena.error; }
			mainFyi('Λανθασμένα δεδομένα: ' + rsp);
			return;
		}

		var html = '';
		for (var i = 0; i < dedomena.dianomi.length; i++) {
			html += Astra.dianomiHTML(dedomena.dianomi[i], i);
		}
		html += '<div class="astraDianomi">';
		html += '<div class="astraKlisimo" ' +
			'onclick="Astra.dianomiOnOff(' + trapezi + ');">';
		html += 'Κλείσιμο';
		html += '</div>';
		html += '</div>';

		div.innerHTML = html;
	};

	this.agoraHTML = function(axb, ekane) {
		var asoi = axb.substr(0,1);
		var xroma = axb.substr(1,1);
		var bazes = axb.substr(2,1);

		var klasi = 'astraAgora';
		var mesa = bazes - ekane;
		if (mesa > 1) { klasi += ' astraSolo'; }
		else if (mesa > 0) { klasi += ' astraMesa'; }

		var html = '';
		html += '<div class="' + klasi + '">';
		html += '<span class="astraAgoraBazes">' + bazes + '</span>';
		html += '<img class="astraAgoraXroma" src= "' + globals.server +
			'images/trapoula/xroma' + xroma + '.png" alt="" />';
		if (asoi == 'Y') {
			html += '&nbsp;+&nbsp;';
			html += '<img class="astraAgoraAsoi" src= "' + globals.server +
			'images/trapoula/asoi.png" title="Και οι άσοι!" alt="" />';
		}
		html += '</div>';
		return html;
	};

	this.dilosiHTML = function(dilosi, mesa) {
		var html = '';
		html += '<div class="astraDilosi';
		if (mesa > 1) { html += ' astraSolo'; }
		else if (mesa > 0) { html += ' astraMesa'; }
		html += '">';
		if (dilosi == 'DTG') {
			html += 'Άμα μείνουν';
		}
		else if (dilosi != '') {
			var exo = dilosi.substr(0, 1);
			var xroma = dilosi.substr(1, 1);
			var bazes = dilosi.substr(2, 1);
			if (exo == 'E') { html += 'Έχω '; }
			html += bazes;
			html += '<img class="astraDilosiXroma" src= "' + globals.server +
				'images/trapoula/xroma' + xroma + '.png" alt="" />';
		}
		else {
			html += '&nbsp;';
		}
		html += '</div>';
		return html;
	};

	var simetoxiDecode = {
		'P':	'ΠΑΙΖΩ',
		'S':	'ΠΑΣΟ',
		'M':	'ΜΑΖΙ',
		'N':	'ΜΟΝΟΣ',
		'V':	'ΒΟΗΘΑΩ',
		'?':	'????'
	};

	this.simetoxiHTML = function(simetoxi) {
		var html = '';
		html += '<div class="astraSimetoxi astraSimetoxi' + simetoxi;
		if (simetoxi == 'S') { html += ' astraPaso'; }
		html += '">';
		html += simetoxiDecode[simetoxi];
		html += '</div>';
		return html;
	};

	var RB = [ 'R', 'R', 'R', 'B', 'B' , 'B', 'R', 'R', 'R', 'B' ];

	this.bazesHTML = function(bazes) {
		var html = '';
		if (bazes <= 0) { return html; }

		html += '<div class="astraBazes">';
		for (var i = 0; i < bazes; i++) {
			html += '<img class="astraBazaIcon" src="' + globals.server +
				'images/trapoula/' + RB[i] + 'V.png" alt="" />';
		}
		html += '</div>';
		return html;
	};

	// Εδώ θα δημιουργήσουμε array στο object "dianomi" με στοιχεία
	// που αφορούν στην κατάσταση των αμυνομένων και αντιστοιχούν στις
	// θέσεις των παικτών (1-based) όπου:
	//
	//	0: σημαίνει βγήκε ή πάσο
	//	1: μέσα απλά
	//	2: μέσα σόλο

	this.mesaExo = function(dianomi) {
		dianomi.mesa = [ 0, 0, 0, 0 ];
		if (notSet(dianomi.a) || notSet(dianomi.t) ||
			notSet(dianomi.s) || notSet(dianomi.b)) {
			return;
		}

		var tzogadoros = dianomi.t;
		switch (tzogadoros) {
		case 1:
			var protos = 2;
			var defteros = 3;
			break;
		case 2:
			protos = 3;
			defteros = 1;
			break;
		case 3:
			protos = 1;
			defteros = 2;
			break;
		default:
			return;
		}
		if ((dianomi.s[protos] == 'S') && (dianomi.s[defteros] == 'S')) {
			return;
		}

		switch (parseInt(dianomi.a.substr(2,1))) {
		case 6:
			var protosPrepi = 2;
			var defterosPrepi = 2;
			break;
		case 7:
			protosPrepi = 2;
			defterosPrepi = 1;
			break;
		case 8:
			protosPrepi = 1;
			defterosPrepi = 1;
			break;
		case 9:
			protosPrepi = 1;
			defterosPrepi = 0;
			break;
		default:
			return;
		}

		// Αν κάποιος από τους αμυνόμενους πήγε πάσο
		// τον μηδενίζω και αλλάζω τον πρώτο.

		if (dianomi.s[protos] == 'S') {
			protos = defteros;
			defteros = 0;
		}

		if (dianomi.s[defteros] == 'S') {
			defteros = 0;
		}

		var aminaPrepi = 0;
		if (protos != 0) { aminaPrepi += protosPrepi; }
		if (defteros != 0) { aminaPrepi += defterosPrepi; }
		var piran = 10 - dianomi.b[tzogadoros];
		if (piran >= aminaPrepi) { return; }

		// Χρησιμοποιώ ένα τοπικό array με τις μπάζες των
		// αμυνομένων, στο οποίο όμως επιχειρώ ένα "ζύγισμα"
		// των μπαζών, δηλαδή τυχόν πλεόνασμα από τον έναν
		// αμυνόμενο το μεταφέρω στον άλλο.

		bazes = [ 0, 0, 0, 0 ]
		bazes[protos] = dianomi.b[protos];
		bazes[defteros] = dianomi.b[defteros];

		if ((protos != 0) && (defteros != 0)) {
			var dif = bazes[protos] - protosPrepi;
			if (dif > 0) {
				bazes[protos] -= dif;
				bazes[defteros] += dif;
			}

			dif = bazes[defteros] - defterosPrepi;
			if (dif > 0) {
				bazes[defteros] -= dif;
				bazes[protos] += dif;
			}
		}

		if (protos != 0) {
			var dif = protosPrepi - bazes[protos];
			if (dif > 1) { dianomi.mesa[protos] = 2; }
			else if (dif > 0) { dianomi.mesa[protos] = 1; }
		}

		if (defteros != 0) {
			dif = defterosPrepi - bazes[defteros];
			if (dif > 1) { dianomi.mesa[defteros] = 2; }
			else if (dif > 0) { dianomi.mesa[defteros] = 1; }
		}

		if (dianomi.s[protos] == 'V') {
			dianomi.mesa[defteros] += dianomi.mesa[protos];
			dianomi.mesa[protos] = 0;
		}

		if (dianomi.s[defteros] == 'V') {
			dianomi.mesa[protos] += dianomi.mesa[defteros];
			dianomi.mesa[defteros] = 0;
		}
	};

	this.dianomiPektisHTML = function(thesi, dianomi) {
		var paso = (notSet(dianomi.a) || notSet(dianomi.t));
		Astra.mesaExo(dianomi);

		var html = '';
		var klasi = 'astraDianomiPektis';
		if (paso) { klasi += ' astraPaso'; }
		html += '<div class="' + klasi + '" style="text-align: center;">';

		if (paso) {
			html += 'ΠΑΣΟ';
		}
		else {
			if (thesi == dianomi.t) {
				var bazes = isSet(dianomi.b) ? dianomi.b[dianomi.t] : 10;
				html += Astra.agoraHTML(dianomi.a, bazes);
			}
			else {
				html += Astra.dilosiHTML(dianomi.o[thesi], dianomi.mesa[thesi]);
				html += Astra.simetoxiHTML(dianomi.s[thesi]);
			}
		}

		if (thesi == dianomi.l) {
			html += '<img class="astraDealer" src= "' + globals.server +
				'images/dealer.png" title="Dealer" alt="" />';
		}

		if (isSet(dianomi.b)) {
			html += Astra.bazesHTML(dianomi.b[thesi]);
		}

		html += '</div>';
		return html;
	};

	this.dianomiHTML = function(dianomi, i) {
		var html = '';
		html += '<div class="astraDianomi astraDianomiZebra' + (i % 2) +
			'" onclick="Astra.kinisiOnOff(' + dianomi.d + ');" ' +
			'title="Κλικ για εμφάνιση/απόκρυψη κινήσεων" ' +
			'onmouseover="Astra.epilogiDianomis(this);" ' +
			'onmouseout="Astra.apoepilogiDianomis(this);">';
		for (var j = 1; j <= 3; j++) {
			html += Astra.dianomiPektisHTML(j, dianomi);
		}
		html += '</div>';
		html += '<div id="d' + dianomi.d + '"></div>';
		return html;
	};

	this.epilogiDianomis = function(div) {
		div.OBC = div.style.backgroundColor;
		div.style.backgroundColor = '#FF99C2';
	};

	this.apoepilogiDianomis = function(div) {
		div.style.backgroundColor = div.OBC;
	};

	this.kinisiOnOff = function(dianomi) {
		alert(dianomi);
	};
};

window.onload = function() {
	init();
	Astra.setHeight();
	Astra.getData();
};
