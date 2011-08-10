var sinedria = {};	// η συνεδρία που αφορά στην επίσκεψη του παίκτη
var sxesi = [];		// οι σχετιζόμενοι και οι αναζητούμενοι
var permes = [];	// τα PMs του χρήστη
var prosklisi = [];	// οι προσκλήσεις που αφορούν στον χρήστη

var partida = {};	// το τραπέζι στο οποίο συμμετέχει ο παίκτης
var dianomi = [];	// οι διανομές του τραπεζιού
var kinisi = [];	// οι κινήσεις της διανομής

var trapezi = [];	// τα ενεργά τραπέζια
var rebelos = [];	// περιφερόμενοι παίκτες

var pexnidi = {};

window.onload = function() {
	init();
	Emoticons.setup();
	motdSetup();
	diafimisiSetup();
	Dumprsp.setup();
	Dedomena.setup();
	Sizitisi.sxolioFocus();
};

window.onunload = function() {
	try { controlPanel.funchatClose(); } catch(e) {};
	try { Dumprsp.close(); } catch(e) {};
	try { offline(); } catch(e) {};
};

function motdSetup() {
	setTimeout(function() {
		var x = getelid('motd');
		if (notSet(x)) { return; }
		if (isSet(x.pineza) && x.pineza) {return; }
		sviseNode(x, 1200);
	}, globals.duration.motd);
}

function diafimisiSetup() {
	setTimeout(function() {
		var x = getelid('diafimisi');
		if (notSet(x)) { return; }
		if (isSet(x.pineza) && x.pineza) {return; }
		sviseNode(x, 1200);
	}, globals.duration.diafimisi);
}

var Prefadoros = new function() {
	this.show = null;

	this.display = function() {
		if (notSet(this.show)) {
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
		x.style.overflowY = 'hidden';
		x.innerHTML = Partida.HTML;
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

		if (isSet(fs) && fs) { Sizitisi.sxolioFocus(); }
		return false;
	};

	this.showKafenio = function(fs) {
		var x = getelid('prefadoros');
		if (notSet(x)) { return false; }

		var s = getelid('sizitisiTrapezi');
		if (isSet(s)) { s.style.display = 'none'; }

		this.show = 'kafenio';
		x.style.overflowY = 'auto';
		x.innerHTML = Trapezi.HTML;
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

		if (isSet(fs) && fs) { Sizitisi.sxolioFocus(); }
		return false;
	};

	this.sizitisiControls = function() {
		var x = getelid('sizitisiControls');
		if (notSet(x)) { return; }
		x.innerHTML = Sizitisi.controlsHTML();
	};

	this.sviseBikeTora = function(img) {
		setTimeout(function() {
			Prefadoros.sviseBikeTora2(img);
		}, 3000);
	};

	this.sviseBikeTora2 = function(img) {
		var x = parseFloat(img.style.width);
		if (x < 0.4) {
			img.parentNode.removeChild(img);
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
	return(isSet(window.partida) && isSet(partida.t) && (partida.t == 1));
}

function notTheatis() {
	return(!isTheatis());
}

function isPartida() {
	return(isSet(window.partida) && isSet(partida.k));
}

function notPartida() {
	return(!isPartida());
}

function isPrive() {
	return(isSet(window.partida) && isSet(partida.p) && (partida.p == 1));
}

function isPublic() {
	return(!isPrive());
}

function isKlisto() {
	return(isSet(window.partida) && isSet(partida.b) && (partida.b == 1));
}

function isAnikto() {
	return(!isKlisto());
}

function isDianomi() {
	return(isSet(window.dianomi) && isSet(dianomi.length) && (dianomi.length > 0));
}

function notDianomi() {
	return(!isDianomi());
}

function isApodoxi(thesi) {
	if (notSet(window.partida)) { return false; }
	var apodoxi = eval('partida.a' + thesi);
	if (notSet(apodoxi)) { return true; }
	return(apodoxi != 0);
}

function notApodoxi(thesi) {
	return(!isApodoxi(thesi));
}

function isKeniThesi() {
	if (isSet(window.partida)) {
		for (var i = 1; i <= 3; i++) {
			if (eval('partida.p' + i) == '') {
				return true;
			}
		}
	}
	return false;
}

function notKeniThesi() {
	return(!isKeniThesi());
}

function isProsklisi() {
	if (notPartida()) { return false; }
	if (notPektis()) { return false; }
	if (notSet(window.prosklisi)) { return false; }
	for (var i = 0; i < prosklisi.length; i++) {
		if (prosklisi[i].p != pektis.login) { continue; }
		if (prosklisi[i].t != partida.k) { continue; }
		return true;
	}
	return false;
}

function notProsklisi() {
	return(!isProsklisi());
}

// Δέχεται μια θέση όπως εμφανίζεται στον client και επιστρέφει την
// πραγματική θέση. Η αντιστοίχιση γίνεται με βάση τη θέση του παίκτη
// (partida.h) ή με βάση τη θέση που περνάμε ως δεύτερη παράμετρο.
// Δηλαδή, ως δεύτερη παράμετρο, μπορούμε να περάσουμε έναν αριθμό
// θέσης που εμφανίζεται ως 1 (νότος) στον client.

function mapThesi(thesi, ena) {
	if (notSet(ena)) {
		if (notPartida()) {
			alert('mapThesi: ακαθόριστη παρτίδα');
			ena = 1;
		}
		else {
			ena = partida.h;
		}
	}
			
	switch (ena) {
	case 1:		break;
	case 2:		thesi += 1; break;
	case 3:		thesi += 2; break;
	default:
		alert('mapThesi: ακαθόριστη θέση');
		return 1;
	}

	while (thesi > 3) { thesi -= 3; }
	return thesi;
}
