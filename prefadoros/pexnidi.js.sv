// Όλη η διαδικασία του παιχνιδιού κυριαρχείται από τα
// αντικείμενα "pexnidi", "dianomi" και "kinisi".

var Pexnidi = new function() {
	// Η μεταβλητή "anamoniKinisis" δείχνει αν περιμένουμε κάποια
	// συγκεκριμένη κίνηση από τον server και μάλιστα δείχνει τον
	// κωδικό της κίνησης αυτής. Πρόκειται για την τελευταία κίνηση
	// που στείλαμε και μας επεστράφη ο κωδικός κατά την εισαγωγή.
	// Αν η μεταβλητή έχει τιμή μηδέν, τότε το πεδίο είναι καθαρό
	// και μπορούμε να στείλουμε καινούρια κίνηση. Αν έχε θετική
	// τιμή, πρόκειται για τον κωδικό της αναμενόμενης κίνησης,
	// ενώ αν έχει αρτνητική τιμή, βρισκόμαστε στο μικροδιάστημα
	// μεταξύ αποστολής νέας κίνησης και παραλαβής του κωδικού
	// της νεοεισαχθείσης κίνησης.

	var anamoniKinisis = 0;

	// Οι παρακάτω ιδιότητες, μέθοδοι και μεταβλητές, σκοπό έχουν
	// κυρίως την αποφυγή της επανάληψης ενδείξεων σημαντικότητας
	// κατά την επαναδιαμόρφωση της εικόνας. Π.χ., όταν κάποιος
	// δηλώνει αγορά, εμφανίζεται σχετική ένδειξη σημαντικότητας
	// στην περιοχή του. Το ίδιο συμβαίνει και όταν κάποιος κερδίζει
	// τον τζόγο, κλπ. Αυτά πυροδοτούνται από την έλευση των
	// σχετικών κινήσεων. Πιο συγκεκριμένα, αν η τελευταία κίνηση
	// που παρελήφθη είναι κάποια κίνηση που απαιτεί ένδειξη
	// σημαντικότητας, τότε γίνεται η ένδειξη, παράλληλα όμως
	// μαρκάρουμε τις κινήσεις αυτές, ώστε να μην έχουμε
	// ανεπιθύμητες επαναλήψεις. Το array δεικοδοτείται με
	// tags που εμπεριέχουν τους κωδικούς των σχετικών
	// κινήσεων και "καθαρίζει" σε κάθε νέα διανομή.

	this.spotList = [];
	var spotListDianomi = 0;

	this.spotListPush = function(idx) {
		if (notDianomi()) { return; }
		var d = dianomi[dianomi.length - 1].k;
		if (d != spotListDianomi) {
			Pexnidi.spotList = [];
			spotListDianomi = d;
		}
		Pexnidi.spotList[idx] = true;
	};

	this.reset = function() {
		pexnidi.ipolipo = 0;
		pexnidi.kapikia = [ 0, 0, 0, 0 ];
		pexnidi.elima = 0;

		pexnidi.fasi = '';
		pexnidi.epomenos = 0;

		pexnidi.dealer = 0;
		pexnidi.fila = [ [], [], [], [] ];

		pexnidi.dilosiCount = 0;
		pexnidi.dilosi = [ '', '', '', '' ];
		pexnidi.curdil = '';

		pexnidi.pasoCount = 0;
		pexnidi.paso = [ false, false, false, false ];

		pexnidi.tzogadoros = 0;
		pexnidi.agora = '';
		pexnidi.xromaAgoras = '';
		pexnidi.xromaBazes = 0;
		pexnidi.asoi = false;

		pexnidi.simetoxi = [ '', '', '', '' ];
		Pexnidi.resetBaza();
		Dodekada.reset();
		Dekada.reset();
	};

	this.resetBaza = function() {
		pexnidi.bazaCount = 0;

		pexnidi.bazaFilo = [];
		pexnidi.bazaPektis = [];

		pexnidi.lastBazaFilo = [];
		pexnidi.lastBazaPektis = [];
	};

	// Η function "processDedomena" καλείται κάθε φορά που έχουμε νέα δεδομένα
	// και σκοπό έχει να θέσει τα στοιχεία του παιχνιδιού στα νέα
	// δεδομένα.

	this.processDedomena = function() {
		var errmsg = 'Pexnidi::processDedomena: ';

		// Αρχικά μηδενίζουμε τα πάντα.
		Pexnidi.reset();
		if (notPartida()) { return;}

		pexnidi.fasi = 'ΣΤΗΣΙΜΟ';

		// Κάνουμε αντιστοίχιση των θέσεων, καθώς από τον
		// server μας έρχονται δεδομένα με τις πραγματικές
		// θέσεις, ενώ στον client ο παίκτης που βρίσκεται
		// στο νότο έχει θέση 1.
		Pexnidi.dianomiMap();
		Pexnidi.kinisiMap();

		// Κανονίζουμε τα οικονομικά των παικτών και της
		// παρτίδας γενικότερα.
		for (var i = 1; i <= 3; i++) {
			pexnidi.kapikia[i] = -(partida.kasa * 10);
		}

		// Τώρα θα διαχειριστούμε τις διανομές της παρτίδας
		// με σκοπό κυρίως τη διαμόρφωση των οικονομικών
		// και τον καθορισμό του dealer.
		pexnidi.ipolipo = 30 * partida.kasa;
		for (var i = 0; i < dianomi.length; i++) {
			pexnidi.dealer = dianomi[i].dealer;
			for (var j = 1; j <= 3; j++) {
				pexnidi.ipolipo -= dianomi[i].kasa[j];
				pexnidi.kapikia[j] += dianomi[i].kasa[j];
				pexnidi.kapikia[j] += dianomi[i].kapikia[j];
			}
		}

		var x = pexnidi.ipolipo / 3;
		pexnidi.ipolipo = parseInt(pexnidi.ipolipo / 10);

		for (var i = 1; i <= 3; i++) {
			pexnidi.kapikia[i] = parseInt(pexnidi.kapikia[i] + x);
		}

		var x = pexnidi.kapikia[partida.pam[2]] + pexnidi.kapikia[partida.pam[3]];
		pexnidi.elima = pexnidi.kapikia[partida.pam[1]] + x;
		pexnidi.kapikia[partida.pam[1]] = -x;

		// Ήρθε η στιγμή να διαχειριστούμε τις κινήσεις της τελευταίας
		// διανομής. Πρέπει να υπάρχει τουλάχιστον μια, καθώς με τη
		// δημιουργία της διανομής, δημιουργείται και κίνηση τύπου
		// "ΔΙΑΝΟΜΗ" που περιέχει τα φύλλα του κάθε παίκτη και τα
		// φύλλα του τζόγου.
		for (var i = 0; i < kinisi.length; i++) {
			// Κάνουμε batch process τη φάση που βρισκόμαστε τώρα
			// και διαχειριζόμαστε την επόμενη κίνηση, η οποία
			// πιθανότατα θα μας περάσει σε νέα φάση.
			Pexnidi.processFasi(true);

			if (kinisi[i].k == anamoniKinisis) { anamoniKinisis = 0; }

			switch (kinisi[i].i) {
			case 'ΔΙΑΝΟΜΗ':
				Pexnidi.processKinisiDianomi(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΔΗΛΩΣΗ':
				Pexnidi.processKinisiDilosi(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΤΖΟΓΟΣ':
				Pexnidi.processKinisiTzogos(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΑΓΟΡΑ':
				Pexnidi.processKinisiAgora(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΣΥΜΜΕΤΟΧΗ':
				Pexnidi.processKinisiSimetoxi(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΦΥΛΛΟ':
				Pexnidi.processKinisiFilo(kinisi[i].thesi, kinisi[i].d);
				break;
			case 'ΜΠΑΖΑ':
				Pexnidi.processKinisiBaza(kinisi[i].thesi);
				break;
			default:
				fatalError(errmsg + kinisi[i].i + ': άγνωστο είδος κίνησης');
				break;
			}
		}
	};

	this.processKinisiDianomi = function(thesi, data) {
		var x = data.split(':');
		for (var i = 1; i <= 3; i++) {
			pexnidi.fila[i] = Pexnidi.spaseFila(x[partida.map[i]]);
		}
	};

	this.processKinisiDilosi = function(thesi, data) {
		pexnidi.dilosiCount++;
		pexnidi.dilosi[thesi] = data;
		if (data.match(/^P/)) {
			pexnidi.paso[thesi] = true;
			pexnidi.pasoCount++;
			if (pexnidi.pasoCount > 2) {
				if (isPPP()) {
					pexnidi.agora = 'NNN';
					pexnidi.tzogadoros = 0;
					for (var i = 1; i <= 3; i++) {
						pexnidi.simetoxi[i] = 'ΠΑΙΖΩ';
					}
					pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
					return;
				}
				pexnidi.fasi = 'ΠΑΣΟ ΠΑΣΟ ΠΑΣΟ';
			}

			pexnidi.fasi = 'ΔΗΛΩΣΗ':
			Pexnidi.setEpomenosDilosi(thesi);
			if (pexnidi.epomenos == 0) {
				pexnidi.fasi = 'ΑΓΟΡΑ';
				pexnidi.epomenos = pexnidi.dealer + 1;
				if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }
			return;
		}

		pexnidi.fasi = 'ΔΗΛΩΣΗ';
		pexnidi.dilosi[thesi] = data;

		// Έχω δήλωση. Αν υπήρχε δήλωση "τα γράφω" βάζω τον
		// παίκτη που τα είχε γράψει στο πάσο.
		for (var i = 1; i <= 3; i++) {
			if (pexnidi.dilosi[i] == 'DTG') {
				pexnidi.paso[i] = true;
			}
		}

		// Αν η δήλωση είναι "τα γράφω", τότε θέτω επόμενη
		// δήλωση τα 6 μπαστούνια, αλλιώς θα υπολογίσω την
		// επόμενη δήλωση αμέσως παρακάτω.
		if (data == 'DTG') {
			pexnidi.curdil = 'DS6';
			return;
		}

		// Είχα κανονική δήλωση και θα υπολογίσω την επόμενη
		// που πρέπει να προταθεί. Παίζει ρόλο το πρώτο γράμμα
		// που είναι "D" για κανονικές δηλώσεις, ή "E" για
		// δηλώσεις "έχω".
		var de = data.substr(0, 1);
		var xroma = data.substr(1, 1);
		var bazes = data.substr(2, 1);

		// Αν έχει κλείσει γύρος, τότε αρχίζουν να παίζουν
		// δηλώσεις "έχω".
		if ((pexnidi.dilosiCount >= 3) && (de == 'D')) {
			pexnidi.curdil = 'E' + xroma + bazes;
		}
		else {
			bazes = Pexnidi.bazesDecode(bazes);
			switch (xroma) {
			case 'S':	xroma = 'C'; break;
			case 'C':	xroma = 'D'; break;
			case 'D':	xroma = 'H'; break;
			case 'H':	xroma = 'N'; break;
			case 'N':	xroma = 'S'; bazes++; break;
			}
			pexnidi.curdil = 'D' + xroma + Pexnidi.bazesEncode(bazes);
		}
	};

	this.processKinisiTzogos = function(thesi, data) {
		pexnidi.tzogadoros = thesi;
		fila = pexnidi.fila[thesi];
		fila.push(data.substr(0, 2));
		fila.push(data.substr(2, 2));

		pexnidi.fila[thesi] = Pexnidi.spaseFila(Pexnidi.deseFila(fila));
		pexnidi.fasi = 'ΤΖΟΓΟΣ';
		pexnidi.epomenos = thesi;
	};

	this.processKinisiAgora = function(thesi, data) {
		pexnidi.tzogadoros = thesi;
		var x = data.split(':');
		if ((x.length != 2) || (!x[0].match(/^[YN][SCDHN][6789T]$/)) ||
			(!x[1].match(/^([SCDHBR][6789TJQKAV]){10}$/))) {
			fatalError(data + ': λάθος δεδομένα κίνησης αγοράς');
		}

		pexnidi.agora = x[0];
		pexnidi.asoi = (pexnidi.agora).match(/^Y/);
		pexnidi.xromaAgoras = (pexnidi.agora).substr(1, 1);
		var bazes = (pexnidi.agora).substr(2, 1);
		pexnidi.bazesAgoras = bazes == 'T' ? 10 : parseInt(bazes);

		pexnidi.fila[thesi] = Pexnidi.spaseFila(x[1]);
		pexnidi.fasi = 'ΣΥΜΜΕΤΟΧΗ';
		pexnidi.epomenos = thesi + 1;
		if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }

		Pexnidi.resetBaza();
	};

	this.processKinisiSimetoxi = function(thesi, data) {
		var errmsg = 'Pexnidi::processKinisiSimetoxi: ';

		switch (pexnidi.tzogadoros) {
		case 1: var ena = 2; var dio = 3; break;
		case 2: ena = 3; dio = 1; break;
		case 3: ena = 1; dio = 2; break;
		default: fatalError(errmsg + 'δεν βρέθηκε τζογαδόρος'); break;
		}

		switch (pexnidi.simetoxi[thesi] = data) {
		case 'ΠΑΙΖΩ':
			if (thesi == ena) {
				pexnidi.epomenos = dio;
			}
			else if (pexnidi.simetoxi[ena] != 'ΠΑΣΟ') {
				pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
			}
			else {
				pexnidi.epomenos = ena;
			}
			break;
		case 'ΜΟΝΟΣ':
			pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
			break;
		case 'ΠΑΣΟ':
			if (thesi == dio) {
				if (pexnidi.simetoxi[ena] == 'ΠΑΙΖΩ') {
					pexnidi.epomenos = ena;
				}
				else if (pexnidi.simetoxi[ena] == 'ΠΑΣΟ') {
					pexnidi.fasi = 'ΠΑΣΟ ΠΑΣΟ';
				}
			}
			else if (pexnidi.simetoxi[dio] == '') {
				pexnidi.epomenos = dio;
			}
			else {
				pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
			}
			break;
		case 'ΜΑΖΙ':
			for (var i = 1; i <= 3; i++) {
				if (pexnidi.simetoxi[i] == 'ΠΑΣΟ') {
					pexnidi.simetoxi[i] = 'ΒΟΗΘΑΩ';
				}
			}
			pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
			break;
		default:
			fatalError('Pexnidi::processKinisiSimetoxi: ' +
				data + ': invalid data');
			break;
		}
	};

	this.processKinisiFilo = function(thesi, data) {
		pexnidi.bazaFilo.push(data);
		pexnidi.bazaPektis.push(thesi);

		var fila = Pexnidi.deseFila(pexnidi.fila[thesi]);
		fila = fila.replace(data, '');
		pexnidi.fila[thesi] = Pexnidi.spaseFila(fila);
	};

	this.processKinisiBaza = function(thesi) {
		pexnidi.bazaCount++;
		pexnidi.fasi = 'ΜΠΑΖΑ';
	};

	this.bazesDecode = function(s) {
		return s == 'T' ? 10 : parseInt(s);
	};

	this.bazesEncode = function(b) {
		return b == 10 ? 'T' : b;
	};

	this.setEpomenosDilosi = function(thesi) {
		epomenos = thesi;
		for (var i = 0; i < 2; i++) {
			epomenos++;
			if (epomenos > 3) { epomenos = 1; }
			if ((!paso[epomenos]) &&
				(pexnidi.dilosi[epomenos] != 'DTG')) {
				pexnidi.epomenos = epomenos;
				return;
			}
		}
		pexnidi.epomenos = 0;
	};

	this.dianomiMap = function() {
		for (var i = 0; i < dianomi.length; i++) {
			dianomi[i].dealer = partida.pam[dianomi[i].d];
			dianomi[i].kasa = [ 0,
				eval('dianomi[' + i + '].k' + partida.map[1]),
				eval('dianomi[' + i + '].k' + partida.map[2]),
				eval('dianomi[' + i + '].k' + partida.map[3])
			];
			dianomi[i].kapikia = [ 0,
				eval('dianomi[' + i + '].m' + partida.map[1]),
				eval('dianomi[' + i + '].m' + partida.map[2]),
				eval('dianomi[' + i + '].m' + partida.map[3])
			];
		}
	};

	this.kinisiMap = function() {
		for (var i = 0; i < kinisi.length; i++) {
			kinisi[i].thesi = partida.pam[kinisi[i].p];
		}
	};

	this.spaseFila = function(s) {
		var fila = [];
		if (notSet(s) || (s.length < 2)) { return fila; }

		var alif = [];

		var pikes = 0;
		var kara = 0;
		var spathia = 0;
		var koupes = 0;
		var ble = 0;
		var red = 0;

		var n = parseInt(s.length / 2)
		for (var i = 0; i < n; i++) {
			var l = i * 2;
			alif[i] = s.substring(l, l + 2);
			if (alif[i].match(/^S/)) { pikes++; }
			else if (alif[i].match(/^D/)) { kara++; }
			else if (alif[i].match(/^C/)) { spathia++; }
			else if (alif[i].match(/^H/)) { koupes++; }
			else if (alif[i].match(/^B/)) { ble++; }
			else if (alif[i].match(/^R/)) { red++; }
		}

		if (pikes > 0) {
			if (spathia == 0) { var sira = [ 'D', 'S', 'H', 'B', 'R' ]; }
			else if (kara > 0) { sira = [ 'S', 'D', 'C', 'H', 'B', 'R' ]; }
			else { sira = [ 'S', 'H', 'C', 'B', 'R' ]; }
		}
		else if (spathia > 0) { var sira = [ 'D', 'C', 'H', 'B', 'R' ]; }
		else { var sira = [ 'D', 'H', 'B', 'R' ]; }

		var idx = 0;

		for (var i = 0; i < sira.length; i++) {
			for (var j = 0; j < alif.length; j++) {
				if (alif[j].match('^' + sira[i])) {
					fila[idx++] = alif[j];
				}
			}
		}

		var more = false;
		do {
			more = false;
			for (var i = 1; i < fila.length; i++) {
				if (fila[i].substr(0, 1) == fila[i - 1].substr(0, 1)) {
					var f1 = fila[i - 1].substr(1, 1);
					var f2 = fila[i].substr(1, 1);
					if (globals.rankFila[f1] > globals.rankFila[f2]) {
						var t = fila[i];
						fila[i] = fila[i - 1];
						fila[i - 1] = t;
						more = true;
					}
				}
			}
		} while (more);

		return fila;
	};

	this.deseFila = function(fila) {
		var s = '';
		for (var i = 0; i < fila.length; i++) { s += fila[i]; }
		return s;
	}

	this.xromaBazesHTML = function(dilosi, bc, xc, spot) {
		switch (pexnidi.fasi) {
		case 'ΠΑΙΧΝΙΔΙ':
			spot = false;
		}
		var html = '';
		if (notSet(bc)) { bc = 'protasiBazes'; }
		if (notSet(xc)) { xc = 'protasiXroma'; }

		if (dilosi == 'DTG') {
			var de = '';
			var bazes = '<span style="font-size: 70%;">Άμα μείνουν</span>';
			var xroma = null;
		}
		else {
			var de = dilosi.substr(0, 1);
			var xroma = dilosi.substr(1, 1);
			var bazes = Pexnidi.bazesDecode(dilosi.substr(2, 1));
		}

		html += '<div class="' + bc + '">';
		if (de == 'E') { html += '<span style="font-size: 80%;">Έχω </span>'; }
		html += bazes;
		html += '</div>';
		if (spot) {
			html += '<img class="' + xc + '" src="' + globals.server +
				'images/svisimo.gif" alt="" onload="Tools.metalagi(this, ';
			if (isSet(xroma)) {
				html += '\'' + globals.server +
					'images/trapoula/xroma' + xroma + '.png\'';
			}
			else {
				html += 'null';
			}
			html += ', 400);" />';
		}
		else if (isSet(xroma)) {
			html += '<img class="' + xc + '" src="' + globals.server +
				'images/trapoula/xroma' + xroma + '.png" alt="" />';
		}
		if (de == 'Y') {
			html += '&nbsp;+&nbsp;';
			html += '<img class="' + xc + '" src="' + globals.server +
				'images/trapoula/asoi.png" style="width: 0.8cm; height: 0.8cm;" alt="" />';
		}
		return html;
	};

	this.epilogiDilosis = function(div, dilosi) {
		var cls = div.getAttribute('class');
		cls += ' epilogiAnamoni';
		div.setAttribute('class', cls);
		if (notSet(dilosi)) { dilosi = pexnidi.curdil; }
		Pexnidi.addKinisi('ΔΗΛΩΣΗ', dilosi);
	};

	this.dilosiPaso = function(div) {
		var cls = div.getAttribute('class');
		cls += ' epilogiAnamoni';
		div.setAttribute('class', cls);
		Pexnidi.addKinisi('ΔΗΛΩΣΗ', 'P' + pexnidi.curdil.substr(1, 2));
	};

	this.addKinisi = function(idos, data) {
		if (anamoniKinisis) {
			playSound('beep');
			mainFyi('Αναμένεται κίνηση από τον server');
			return;
		}

		anamoniKinisis = -1;
		var req = new Request('pexnidi/addKinisi');
		req.xhr.onreadystatechange = function() {
			Pexnidi.addKinisiCheck(req);
		};

		var params = 'thesi=' + uri(partida.map[1]);
		params += '&idos=' + uri(idos);
		params += '&data=' + uri(data);
		req.send(params);
	};

	this.addKinisiCheck = function(req) {
		if (req.xhr.readyState != 4) { return; }
		var rsp = req.getResponse();
		if (rsp.match(/^OK@/)) {
			anamoniKinisis = parseInt(rsp.replace(/^OK/));
		}
		else {
			mainFyi(rsp);
			playSound('beep');
			anamoniKinisis = 0;
		}
	};

	this.isAsoi = function() {
		if (pexnidi.tzogadoros != 1) { return false; }
		var fila = pexnidi.fila[1];
		var count = 0;
		for (var i = 0; i < fila.length; i++) {
			if (fila[i].match(/^.A/)) { count++; }
		}
		return (count > 3);
	};

	this.piosPektis = function(thesi, ena, dio) {
		if (notSet(ena)) { ena = 'Ο παίκτης που παρακολουθείτε '; }
		else { ena += ' '; }
		if (notSet(dio)) { dio = ''; }
		else { dio + ' '; }
		switch (thesi) {
		case 1:		return ena;
		case 2:		return 'Ο παίκτης στα δεξιά σας ' + dio;
		case 3:		return 'Ο παίκτης στα αριστερά σας ' + dio;
		}

		fatalError('Αδυναμία προσανατολισμού (ακαθόριστη θέση)');
	};

	var aedpTime = 4000;

	this.processFasi = function(batch) {
		var errmsg = 'Pexnidi::processFasi: ';

		if (notSet(batch)) { batch = false; }
		switch (pexnidi.fasi) {
		case 'ΔΗΛΩΣΗ':
			Pexnidi.processFasiDilosi(batch);
			break;
		case 'ΠΑΣΟ ΠΑΣΟ ΠΑΣΟ':
			Pexnidi.processFasiPasoPasoPaso(batch);
			break;
		case 'ΠΑΣΟ ΠΑΣΟ':
			pexnidi.epomenos = 0;
			if (notTheatis() && (pexnidi.dealer == 1)) {
				setTimeout(function() {
					Pliromi.pliromiBazon();
					Pexnidi.dianomi();
				}, aedpTime);
			}
			if (aedpTime >= 1200) { aedpTime -= 700 }
			break;
		case 'ΠΑΙΧΝΙΔΙ':
			Pexnidi.setEpomenosPexnidi();
			var telkin = kinisi[kinisi.length - 1];
			switch (telkin.i) {
			case 'ΣΥΜΜΕΤΟΧΗ':
				if (pexnidi.epomenos == 1) {
					pexnidi.epomenos = 0;
					setTimeout(function() {
						Pexnidi.setEpomenosPexnidi();
						Partida.updateHTML();
						Prefadoros.display();
					}, 1500);
				}
				break;
			}
			break;
		case 'ΜΠΑΖΑ':
			if (batch) {
				pexnidi.bazaFilo = [];
				pexnidi.bazaPektis = [];
				if (pexnidi.bazaCount > 9) {
					pexnidi.fasi = 'ΔΙΑΝΟΜΗ';
				}
				else {
					pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
				}
				return;
			}

			pexnidi.epomenos = 0;
			if (notTheatis() && (pexnidi.bazaCount > 9) && (pexnidi.dealer == 1)) {
				setTimeout(function() {
					Pliromi.pliromiBazon();
					Pexnidi.dianomi();
				}, aedpTime);
				if (aedpTime >= 1200) { aedpTime -= 700 }
				return;
			}

			setTimeout(function() {
				var telkin = kinisi[kinisi.length - 1];
				pexnidi.epomenos = telkin.thesi;
				pexnidi.bazaFilo = [];
				pexnidi.bazaPektis = [];
				pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
				Partida.updateHTML();
				Prefadoros.display();
			}, 1200);
			break;
		case 'ΣΤΗΣΙΜΟ':
		case 'ΣΥΜΜΕΤΟΧΗ':
		case 'ΤΖΟΓΟΣ':
			break;
		default:
			fatalError(errmsg + pexnidi.fasi + ': άγνωστη φάση');
			break;
		}
	};

	this.processFasiDilosi = function(batch) {
		if (pexnidi.dilosiCount <= 0) {
			pexnidi.curdil = 'DTG';
			pexnidi.epomenos = pexnidi.dealer + 1;
			if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }
			return;
		}

		for (epomenos = pexnidi.epomenos + 1;; epomenos++) {
			if (epomenos > 3) { epomenos = 1; }
			if (pexnidi.paso[epomenos]) { continue; }
			if (epomenos != pexnidi.epomenos) {
				pexnidi.epomenos = epomenos;
				break;
			}
			pexnidi.tzogadoros = pexnidi.epomenos;
			pexnidi.fasi = 'ΑΓΟΡΑ';
			return;
		}

		if ((pexnidi.dilosiCount == 3) && (pexnidi.curdil == "DD6")) {
			pexnidi.curdil = "EC6";
		}
	};

	var pppDelay = 3000;

	this.processFasiPasoPasoPaso = function(batch) {
		if (isPPP()) {
			if (batch) {
				Pexnidi.setDataPasoPasoPaso();
				return;
			}

			pexnidi.epomenos = 0;
			setTimeout(function() {
				Pexnidi.setDataPasoPasoPaso();
				Partida.updateHTML();
				Prefadoros.display();
			}, pppDelay);
			Pexnidi.miosiPppDelay();
			return;
		}

		pexnidi.epomenos = 0;
		pexnidi.dealer++;
		if (pexnidi.dealer > 3) { pexnidi.dealer = 1; }
		if ((!batch) && notTheatis() && (pexnidi.dealer == 1)) {
			setTimeout(Pexnidi.dianomi, pppDelay);
			Pexnidi.miosiPppDelay();
		}
	};

	this.miosiPppDelay = function() {
		pppDelay -= 700;
		if (pppDelay < 800) { pppDelay = 800; }
	};

	this.setDataPasoPasoPaso = function() {
		Pexnidi.resetBaza();
		pexnidi.agora = 'NNN';
		pexnidi.fasi = 'ΠΑΙΧΝΙΔΙ';
		for (var i = 1; i <= 3; i++) { pexnidi.simetoxi[i] = 'ΠΑΙΖΩ'; }
		pexnidi.epomenos = pexnidi.dealer + 1;
		if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }
	};

	this.setEpomenosPexnidi = function() {
		// Αν παίζεται το πάσο πάσο πάσο, τότε δεν υπάρχει τζογαδόρος
		// και επόμενος είναι ο επόμενος παίκτης.
		if (pexnidi.tzogadoros == 0) {
			pexnidi.epomenos++;
			if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }
			return;
		}

		var telkin = kinisi[kinisi.length - 1];
		if (telkin.i == 'ΜΠΑΖΑ') {
			pexnidi.epomenos = telkin.thesi;
			return;
		}

		if (telkin.i == 'ΣΥΜΜΕΤΟΧΗ') {
			pexnidi.epomenos = pexnidi.dealer + 1;
		}
		else if (telkin.i == 'ΦΥΛΛΟ') {
			pexnidi.epomenos = telkin.thesi + 1;
		}
		for (var i = 0; i < 3; i++) {
			if (pexnidi.epomenos > 3) { pexnidi.epomenos = 1; }
			if ((pexnidi.epomenos == pexnidi.tzogadoros) ||
				(pexnidi.simetoxi[pexnidi.epomenos] != 'ΠΑΣΟ')) {
				return;	
			}
			pexnidi.epomenos++;
		}

		fatalError('αδυναμία καθορισμού επόμενου παίκτη ' +
			'κατά τη φάση εκκίνησης του παιχνιδιού');
	};

	this.dianomi = function() {
		mainFyi('Γίνεται διανομή. Παρακαλώ περιμένετε…');
		var req = new Request('trapezi/apodoxi');
		req.xhr.onreadystatechange = function() {
			Pexnidi.dianomiCheck(req);
		};

		params = 'dianomi=yes';
		params += '&thesi=' + partida.map[1];
		req.send(params);
	};

	this.dianomiCheck = function(req) {
		if (req.xhr.readyState != 4) { return; }
		var rsp = req.getResponse();
		mainFyi(rsp);
		if (rsp) {
			playSound('beep');
		}
	};

	this.epilogiAgoras = function(div, dxb) {
		var de = dxb.substr(0, 1);
		var xroma = dxb.substr(1, 1);
		var bazes = dxb.substr(2, 1);
		var agora = ((Pexnidi.isAsoi() && confirm('Θα δηλώσετε τους τέσσερις άσους;')) ?
			'Y' : 'N') + xroma + bazes;

		var fila = pexnidi.fila[1];
		var neaFila = '';
		for (var i = 0; i < 12; i++) {
			if ((i in Dodekada.klidomeno) && (Dodekada.klidomeno[i])) { continue; }
			neaFila += fila[i];
		}

		if (confirm('Να γίνει αγορά ' + Tools.decodeAgora(agora) + ';')) {
			var cls = div.getAttribute('class');
			cls += ' epilogiAnamoni';
			div.setAttribute('class', cls);
			Pexnidi.addKinisi('ΑΓΟΡΑ', agora + ':' + neaFila);
		}
	};

	this.epilogiSimetoxi = function(div, data) {
		Pexnidi.addKinisi('ΣΥΜΜΕΤΟΧΗ', data);
	};
}