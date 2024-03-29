var sinedria = {};	// η συνεδρία που αφορά στην επίσκεψη του παίκτη
var sxesi = [];		// οι σχετιζόμενοι και οι αναζητούμενοι
var permes = [];	// τα PMs του χρήστη
var prosklisi = [];	// οι προσκλήσεις που αφορούν στον χρήστη

var partida = {};	// το τραπέζι στο οποίο συμμετέχει ο παίκτης
var dianomi = [];	// οι διανομές του τραπεζιού
var kinisi = [];	// οι κινήσεις της διανομής

var trapezi = [];	// τα ενεργά τραπέζια
var rebelos = [];	// περιφερόμενοι παίκτες

var pexnidi = {};	// Το παιχνίδι που αντιστοιχεί στην παρτίδα

window.onload = function() {
	init();
	Emoticons.setup();
	motdSetup();
	diafimisiSetup();
	Dumprsp.setup();
	Dedomena.setup();
	Sizitisi.sxolioFocus();
	Pexnidi.reset();
	mainFyi('Απενεργοποιήστε το <span style="color: ' + globals.color.error +
		';">torrent</span> όσο είστε στον Πρεφαδόρο!', 7000);
};

window.onunload = function() {
	try { controlPanel.funchatClose(); } catch(e) {};
	try { controlPanel.kitapiClose(); } catch(e) {};
	try { Dumprsp.close(); } catch(e) {};
	try { offline(); } catch(e) {};
};

function motdSetup() {
	setTimeout(function() {
		var x = getelid('motd');
		if (notSet(x)) { return; }
		if (isSet(x.pineza) && x.pineza) {return; }
		sviseNode(x, 500);
	}, globals.duration.motd);
}

function diafimisiSetup() {
	setTimeout(function() {
		var x = getelid('diafimisi');
		if (notSet(x)) { return; }
		if (isSet(x.pineza) && x.pineza) {return; }
		sviseNode(x, 500);
	}, globals.duration.diafimisi);
}

var Prefadoros = new function() {
	this.show = null;
	this.egineExodos = false;

	this.display = function() {
		if (this.egineExodos) {
			this.show = isPartida() ? 'partida' : 'kafenio';
			this.egineExodos = false;
		}
		else if (notSet(this.show)) {
			this.show = isPartida() ? 'partida' : 'kafenio';
		}

		if (this.show === 'partida') {
			Prefadoros.showPartida();
		}
		else {
			Prefadoros.showKafenio();
		}
		Prefadoros.clearBikeNeos();
		controlPanel.display();
		monitor.displayStats();
	};

	this.clearBikeNeos = function() {
		if (notSet(partida)) { return; }
		for (i = 1; i <= 3; i++) {
			var n = 'n' + i;
			if (n in partida) { delete partida[n]; }
		}
	};

	this.showPartida = function(fs) {
		var x = getelid('prefadoros');
		if (notSet(x)) { return false; }

		var s = getelid('sizitisiKafenio');
		if (isSet(s)) { s.style.display = 'none'; }

		this.show = 'partida';
		if (Partida.HTML != x.innerHTML) {
			x.style.overflowY = 'visible';
			x.innerHTML = Partida.HTML;
			if (pexnidi.epomenos == 1) {
				setTimeout(Dekada.setControls, 100);
			}
		}
		Prefadoros.prosklisiControls();
		Prefadoros.sizitisiControls();

		var s = getelid('sizitisiTrapezi');
		if (isSet(s)) {
			Sizitisi.scrollBottom();
			s.style.display = 'inline';
		}

		x = getelid('partidaKafenio');
		if (isSet(x)) {
			x.innerHTML = '[&nbsp;<a href="#" ' +
				'onclick="return Prefadoros.showKafenio(true);" ' +
				'class="data" title ="Εμφάνιση τραπεζιού">' +
				'Καφενείο</a>&nbsp;]';
		}

		if (isSet(fs) && fs) {
			Sizitisi.sigasiVideo();
			Sizitisi.sxolioFocus();
		}

		// Εδώ υπάρχει ένα λεπτό σημείο του προγράμματος. Πρόκειται
		// για διαλόγους επιβεβαίωσης είτε της αγοράς, είτε του claim.
		// Αυτοί οι διάλογοι εμφανίζονται για επικύρωση των επιλογών
		// του τζογαδόρου. Όταν έρχονται, όμως, νέα δεδομένα και πρέπει
		// να γίνει επανασχεδιασμός (π.χ. από σχόλια που γράφει ένας
		// άλλος παίκτης), τότε αυτοί οι διάλογοι εξαφανίζονται. Για
		// το λόγο αυτό χρησιμοποιούνται κάποιες properties που
		// καταδεικνύουν ότι κάποιος διάλογος βρίσκεται σε εξέλιξη,
		// ώστε σε περίπτωση επανασχεδιασμού να εμφανιστεί ο τρέχων
		// διάλογος στην νέα εικόνα.

		if (isSet(Pexnidi) && isSet(Pexnidi.agoraData.agora)) {
			if (isSet(Pexnidi.agoraData.solo)) {
				Pexnidi.confirmSolo();
			}
			else {
				Pexnidi.confirmAgora();
			}
		}
		else if (isSet(controlPanel) && controlPanel.claimConfirmation) {
			controlPanel.confirmClaim();
		}

		Prefadoros.setOptiki(isPartida() ? partida.kodikos : -1);
		return false;
	};

	this.showKafenio = function(fs) {
		var x = getelid('prefadoros');
		if (notSet(x)) { return false; }

		var s = getelid('sizitisiTrapezi');
		if (isSet(s)) { s.style.display = 'none'; }

		// Αν είμαστε σε mode παρτίδας και πάμε να γυρίσουμε σε
		// mode καφενείου, τότε κρατάμε το inner HTML της παρτίδας
		// όπως έχει αυτή ακριβώς τη στιγμή, ώστε να το έχουμε
		// ενημερωμένο όταν ξαναγυρίσουμε σε mode παρτίδας.
		if (isSet(this.show) && (this.show == 'partida') &&
			isSet(Partida) && isSet(x.innerHTML)) {
			Partida.HTML = x.innerHTML;
		}

		this.show = 'kafenio';
		if (Trapezi.HTML != x.innerHTML) {
			x.style.overflowY = 'auto';
			x.innerHTML = Trapezi.HTML;
		}
		Prefadoros.prosklisiControls();
		Prefadoros.sizitisiControls();

		var s = getelid('sizitisiKafenio');
		if (isSet(s)) {
			Sizitisi.scrollBottom();
			s.style.display = 'inline';
		}

		x = getelid('partidaKafenio');
		if (isSet(x)) {
			x.innerHTML = '[&nbsp;<a href="#" ' +
				'onclick="return Prefadoros.showPartida(true);" ' +
				'class="data" title="Εμφάνιση καφενείου">' +
				'Τραπέζι</a>&nbsp;]';
		}

		if (isSet(fs) && fs) {
			var x = getelid('galleryPhoto');
			if (isSet(x)) {
				x.src = globals.server + 'images/gallery/' + Trapezi.randomPhoto();
			}
			Sizitisi.sigasiVideo();
			Sizitisi.sxolioFocus();
		}

		Prefadoros.setOptiki(0);
		return false;
	};

	this.setOptiki = function(trapezi) {
		var req = new Request('prefadoros/setOptiki');
		req.xhr.onreadystatechange = function() {
			Prefadoros.setOptikiCheck(req);
		};

		var params = 'sinedria=' + uri(sinedria.kodikos);
		params += '&trapezi=' + trapezi;
		req.send(params);
	};

	this.setOptikiCheck = function(req) {
		if (req.xhr.readyState != 4) { return; }
		var rsp = req.getResponse();
		if (rsp) { mainFyi(rsp); }
	};

	this.prosklisiControls = function() {
		var x = getelid('prosklisiControls');
		if (notSet(x)) { return; }

		// Επανεμφανίζουμε το πλήκτρο αποστολής προσκλήσεων σε θεατές
		// ανάλογα με τις προσβάσεις του χρήστη, χωρίς να μεταφέρουμε
		// το focus στο πεδίο των σχολίων.
		x.innerHTML = Prosklisi.controlsHTML();
	};

	this.sizitisiControls = function() {
		var x = getelid('sizitisiControls');
		if (notSet(x)) { return; }

		// Επανεμφανίζουμε τα πλήκτρα αποστολής και διαγραφής μηνυμάτων
		// ανάλογα με τις προσβάσεις του χρήστης, αλλά δεν μεταφέρουμε
		// το focus στο πεδίο των σχολίων.
		x.innerHTML = Sizitisi.controlsHTML(false);
	};

	this.sviseBikeTora = function(img) {
		setTimeout(function() {
			Prefadoros.sviseBikeTora2(img);
		}, 3000);
	};

	this.sviseBikeTora2 = function(img) {
		var x = parseFloat(img.style.width);
		if (x < 0.4) {
			try { img.parentNode.removeChild(img); } catch(e) {};
			return;
		}

		img.style.width = (x - 0.2) + 'cm';

		x = parseFloat(img.style.top);
		img.style.top = (x + 0.1) + 'cm';

		x = parseFloat(img.style.right);
		img.style.right = (x + 0.2) + 'cm';
		setTimeout(function() {
			Prefadoros.sviseBikeTora2(img);
		}, 50);
	};
}

function isPektis() {
	return(isSet(window.pektis) && isSet(pektis.login));
}

function notPektis() {
	return(!isPektis());
}

function isTheatis() {
	return(isSet(window.partida) && isSet(partida.theatis) && partida.theatis);
}

function notTheatis() {
	return(!isTheatis());
}

function isDiathesimos() {
	if (notPektis()) { return false; }
	if (notSet(pektis.available)) { return false; }
	return pektis.available;
}

function notDiathesimos() {
	return(!isDiathesimos());
}

function isPartida() {
	return(isSet(window.partida) && isSet(partida.kodikos));
}

function notPartida() {
	return(!isPartida());
}

function isIdioktito() {
	return(isSet(window.partida) && isSet(partida.idioktito) && partida.idioktito);
}

function notIdioktito() {
	return(!isIdioktito());
}

function dikeomaRithmisis() {
	if (notPartida()) { return false; }
	if (notIdioktito()) { return true; }
	return (partida.thesi == 1);
}

function isPrive() {
	return(isSet(window.partida) && isSet(partida.prive) && partida.prive);
}

function isPublic() {
	return(!isPrive());
}

function isKlisto() {
	return(isSet(window.partida) && isSet(partida.klisto) && partida.klisto);
}

function isAnikto() {
	return(!isKlisto());
}

function isPasoPasoPaso() {
	return(isSet(window.partida) && isSet(partida.ppp) && partida.ppp);
}

function notPasoPasoPaso() {
	return(!isPasoPasoPaso());
}

function isAsoiKolos() {
	return(isSet(window.partida) && isSet(partida.asoi) && partida.asoi);
}

function notAsoiKolos() {
	return(!isAsoiKolos());
}

function isPostel() {
	return(isSet(window.partida) && isSet(partida.postel) && (partida.postel != 0));
}

function isLearner() {
	return(isSet(window.partida) && isSet(partida.learner) && partida.learner);
}

function notLearner() {
	return(!isLearner());
}

function denPezoun() {
	var paso = 0;
	for (var i = 1; i <= 3; i++) {
		if (pexnidi.simetoxi[i] == 'ΠΑΣΟ') {
			paso++;
		}
	}
	return (paso > 1);
}

function isDianomi() {
	return(isSet(window.dianomi) && isSet(dianomi.length) && (dianomi.length > 0));
}

function notDianomi() {
	return(!isDianomi());
}

function isKinisi() {
	return(isSet(window.kinisi) && isSet(kinisi.length) && (kinisi.length > 0));
}

function notKinisi() {
	return(!isKinisi());
}

function isApodoxi(thesi) {
	if (notSet(window.partida)) { return false; }
	return partida.apodoxi[thesi];
}

function notApodoxi(thesi) {
	return(!isApodoxi(thesi));
}

function isKeniThesi() {
	if (isSet(window.partida)) {
		for (var i = 1; i <= 3; i++) {
			if (partida.pektis[i] == '') { return true; }
		}
	}
	return false;
}

function notKeniThesi() {
	return(!isKeniThesi());
}

function isVoithao(thesi) {
	if (notSet(window.pexnidi)) { return false; }
	if (notSet(pexnidi.simetoxi)) { return false; }
	return(pexnidi.simetoxi[thesi] == 'ΒΟΗΘΑΩ')
}

function denPezi(thesi) {
	if (notSet(window.pexnidi)) { return false; }
	if (notSet(pexnidi.simetoxi)) { return false; }
	return((pexnidi.fasi != 'ΣΥΜΜΕΤΟΧΗ') && (pexnidi.simetoxi[thesi] == 'ΠΑΣΟ'))
}

function isProsklisi() {
	if (notPartida()) { return false; }
	if (notPektis()) { return false; }
	if (notSet(window.prosklisi)) { return false; }
	for (var i = 0; i < prosklisi.length; i++) {
		if (prosklisi[i].p != pektis.login) { continue; }
		if (prosklisi[i].t != partida.kodikos) { continue; }
		return true;
	}
	return false;
}

function dikeomaProsklisis() {
	if (notPartida()) { return false; }
	if (notPektis()) { return false; }
	return dikeomaRithmisis();
}

function notProsklisi() {
	return(!isProsklisi());
}

function isTzogadoros(thesi) {
	if (notSet(window.pexnidi)) { return false; }
	if (notSet(pexnidi.tzogadoros)) { return false; }

	if (notSet(thesi)) { thesi = 1; }
	return (thesi == pexnidi.tzogadoros);
}

function notTzogadoros(thesi) {
	return (!isTzogadoros(thesi));
}

function isEpomenos(thesi) {
	if (notSet(window.pexnidi)) { return false; }
	if (notSet(pexnidi.epomenos)) { return false; }

	if (notSet(thesi)) { thesi = 1; }
	return (thesi == pexnidi.epomenos);
}

function notEpomenos(thesi) {
	return (!isEpomenos(thesi));
}

var Trattr = {};

Trattr.iconHTML = function(css, src, tit, inf) {
	var html = '';
	html = '<img class="' + css + '" alt="" src="' + src + '"';
	if (isSet(tit)) { html += ' title="' + tit + '"'; }
	if (isSet(inf)) { html += ' onclick="Motto.dixe({text:\'' + inf + '\'});"'; }
	html += ' />';
	return html;
};

Trattr.idioktitoHTML = function(css) {
	return Trattr.iconHTML(css, globals.server + 'images/controlPanel/idioktitoKafenio.png',
		'Ιδιόκτητο τραπέζι', 'Μόνο ο παίκτης που δημιούργησε το τραπέζι έχει δικαίωμα<br />' +
		'ρύθμισης και πρόσκλησης παικτών στο τραπέζι.');
};

Trattr.klistoHTML = function(css) {
	return Trattr.iconHTML(css, globals.server + 'images/controlPanel/klisto.png',
		'Κλειστό τραπέζι', 'Οι θεατές δεν βλέπουν τα φύλλα του παίκτη που<br />' +
		'παρακολουθούν, παρά μόνο τα φύλλα που παίζονται.');
};

Trattr.pasoPasoPasoHTML = function(css) {
	return Trattr.iconHTML(css, globals.server + 'images/controlPanel/ppp.png',
		'Παίζεται το πάσο, πάσο, πάσο', 'Οι διανομές στις οποίες δεν αγοράζει κανείς<br />' +
		'παίζονται αχρωμάτιστες χωρίς τα κάτω φύλλα.');
};

Trattr.oxiAsoiHTML = function(css) {
	return Trattr.iconHTML(css, globals.server + 'images/trapoula/asoi.png',
		'Δεν πριμοδοτούνται οι άσοι', 'Δεν δηλώνονται, ούτε πριμοδοτούνται<br />' +
		'οι τέσσερις άσοι στην αγορά.');
};

Trattr.postelIconHTML = function(css, extra, postel) {
	var html = '';
	if (notSet(postel)) {
		if (notSet(window.partida)) { return html; }
		if (notSet(partida.postel)) { return html; }
		postel = partida.postel;
	}

	var inf = null;
	switch (postel) {
	case 1:
		var ico = 'anisoropo';
		var tit = 'Ανισόρροπη πληρωμή τελευταίας αγοράς';
		inf = 'Η αξία της τελευταίας αγοράς προσαρμόζεται στο υπόλοιπο της<br />' +
			'κάσας, εκτός και αν η αγορά μπει μέσα οπότε πληρώνεται κανονικά.';
		break;
	case 2:
		ico = 'dikeo';
		tit = 'Δίκαιη πληρωμή τελευταίας αγοράς';
		inf = 'Αν το υπόλοιπο της κάσας δεν επαρκεί για την πληρωμή της<br />' +
			'τελευταίας αγοράς, ενισχύεται η κάσα ώστε η αγορά να<br />' +
			'πληρωθεί με την κανονική της αξία.';
		break;
	default:
		ico = 'kanoniko';
		tit = 'Κανονική πληρωμή τελευταίας αγοράς';
		break;
	}
	html = '<img class="' + css + '" alt="" src="' + globals.server +
		'images/postel/' + ico + '.png" title="' + tit + '"';

	if (isSet(extra)) { html += ' ' + extra; }
	else if (isSet(inf)) { html += ' onclick="Motto.dixe({text:\'' + inf + '\'});"'; }

	html += ' />';
	return html;
};

Trattr.learnerIconHTML = function(css) {
	return Trattr.iconHTML(css, globals.server + 'images/controlPanel/learner.png',
		'Εκπαιδευτική', 'Εκπαιδευτική παρτίδα (δεν προσμετράται στη βαθμολογία)');
};
