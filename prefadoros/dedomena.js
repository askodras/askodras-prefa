var Dedomena = new function() {
	var lastDataTS = 0;
	var sessionAliveTS = 0;
	var galleryPhotoTS = 0;
	var polivoloTS1 = 0;
	var polivolo1 = 0;
	var polivoloTS2 = 0;
	var polivolo2 = 0;
	var reschedule = true;

	this.kafenioApo = 0;

	this.setup = function() {
		lastDataTS = currentTimestamp();
		sessionAliveTS = lastDataTS;
		galleryPhotoTS = lastDataTS;
		setTimeout(function() { Dedomena.neaDedomena(true); }, 200);
		setTimeout(Dedomena.checkAlive, 5000);
		Dedomena.kafenioApo = 0;
	};

	this.schedule = function(freska, dd) {
		if (!reschedule) { return; }
		if (notSet(freska)) { freska = false; }
		if (notSet(dd)) { dd = 0; }

		if (monitor.successiveErrors < 1) { var delay = 200; }
		else if (monitor.successiveErrors < 3) { delay = 500; }
		else { delay = 1000; }

		// Όταν έχουμε animation, τότε υπάρχει έτσι κι αλλιώς καθυστέρηση
		// την οποία φροντίζουμε να περάσουμε στην παρούσα, ώστε να αφαιρεθεί.
		delay -= dd;
		if (delay < 10) { delay = 10; }

		if (monitor.successiveErrors > 2) {
			mainFyi('Παρουσιάστηκαν διαδοχικά σφάλματα επικοινωνίας ' +
				'(εφαρμόστηκε καθυστέρηση ' + delay + 'ms)');
		}
		setTimeout(function() { Dedomena.neaDedomena(freska); }, delay);
	};

	// Η μέθοδος "keepAlive" τρέχει σε τακτά χρονικά διαστήματα και ελέγχει
	// αν η επικοινωνία μας με τον server είναι ζωντανή. Πράγματι, κάθε φορά
	// που επιστρέφονται δεδομένα από τον server κρατάμε το χρόνο στη
	// μεταβλητή "lastDataTS" (σε milliseconds). Η "keepAlive" ελέγχει αν
	// ο χρόνος που έχει παρέλθει από την τελευταία παραλαβή, έχει υπερβεί
	// τον μέγιστο επιτρεπτό χρόνο απραξίας ("parameters.noAnswerMax"), και
	// αν όντως έχει συμβεί αυτό, επαναδρομολογεί νέο αίτημα ενημέρωσης.
	// Παράλληλα, ανανεώνεται το session, καθώς μπορεί ο χρήστης μπορεί
	// να μην έχει επικοινωνία με τον server για μεγάλα χρονικά διαστήματα,
	// με αποτέλεσμα να απενεργοποιείται το session, εφόσον τα αιτήματα
	// ενημέρωσης λειτουργούν εκτός session.

	this.checkAlive = function() {
		var tora = currentTimestamp();
		var elapsed = tora - lastDataTS;
		if (elapsed > parameters.noAnswerMax) {
			monitor.lathos();
			mainFyi('regular polling cycle recycled');
			Dedomena.schedule(true);
		}
		var epomeno = parameters.noAnswerMax - elapsed;
		if (epomeno < 1000) { epomeno = 1000 }
		setTimeout(Dedomena.checkAlive, epomeno);

		// Κάθε 5 λεπτά, ανανεώνουμε το session.
		if ((tora - sessionAliveTS) > 300000) {
			Dedomena.sessionAlive();
			sessionAliveTS = tora;
		}

		// Κάθε 120 δευτερόλεπτα, ανανεώνουμε τη φωτογραφία
		// του καφενείου. Ο χρόνος δεν είναι ασφαλής, καθώς
		// ο κύκλος ελέγχου μπορεί να είναι μεγαλύτερος.
		if ((tora - galleryPhotoTS) > 120000) {
			Trapezi.randomPhoto();
			galleryPhotoTS = tora;
		}
	};

	this.neaDedomena = function(freska) {
		if (notSet(freska)) { freska = false; }
		var req = new Request('prefadoros/neaDedomena');
		req.xhr.onreadystatechange = function() {
			Dedomena.neaDedomenaCheck(req);
		};

		var params = 'login=' + pektis.login;

		sinedria.id++;
		params += '&sinedria=' + sinedria.kodikos;
		params += '&id=' + sinedria.id;

		if (freska) {
			Dedomena.kafenioApo = 0;
			params += '&freska=yes';
		}

		if (notSet(Dedomena.kafenioApo)) { Dedomena.kafenioApo = 0; }
		params += '&kafenioApo=' + uri(Dedomena.kafenioApo);

		req.send(params);
	};

	var refreshErrorCount = 0;

	this.neaDedomenaCheck = function(req) {
		if (req.xhr.readyState != 4) { return; }
		rsp = req.getResponse();
		Dumprsp.dump(rsp);
		try {
			var dedomena = eval('({' + rsp + '})');
			if (notSet(dedomena) || notSet(dedomena.s)) {
				dedomena = { s: { fatalError: 'Ακαθόριστη συνεδρία!' }};
			}
			if (isSet(dedomena.s.debug)) {
				mainFyi(dedomena.s.debug);
			}
			if (isSet(dedomena.s.fatalError)) {
				reschedule = false;
				mainFyi(dedomena.s.fatalError, -1);
				playSound('beep');
				return;
			}
		} catch(e) {
			monitor.lathos();
			Dumprsp.lathos();

			// Κατά την έξοδο, ή κατά το refresh παραλαμβάνονται ελλιπή
			// δεδομένα. Αυτά δεν πρέπει να τα δείξω στον παίκτη, εκτός
			// και αν επαναληφθούν.
			var refreshError = (rsp == 'prefadoros/neaDedomena (status = 0)');
			if (!refreshError) { var showError = true; }
			else if (refreshErrorCount++ > 0) { showError = true; }
			else { showError = false; }

			if (showError) {
				mainFyi(rsp + ': λανθασμένα δεδομένα (' + e + ')');
			}
			Dedomena.schedule(true);
			return;
		}

		if ((dedomena.s.k < sinedria.kodikos) || (dedomena.s.i < sinedria.id)) {
			monitor.ignore();
			Dumprsp.ignore();
			return;
		}

		var tora = currentTimestamp();
		lastDataTS = tora;

		if (polivoloTS1 == 0) {
			polivoloTS1 = tora;
		}
		else if ((tora - polivoloTS1) < parameters.xronosPolivolo1) {
			if (polivolo1++ > parameters.maxPolivolo1) {
				if (!confirm('ΠΡΟΣΟΧΗ: επαναλαμβανόμενες κλήσεις. ' +
					'Να συνεχίσω τις προσπάθειες;')) {
					location.href = globals.server + 'error.php?minima=' +
						uri('Επαναλαμβανόμενες κλήσεις!');
					return;
				}
				polivoloTS1 = currentTimestamp();
				polivolo1 = 0;
				
			}
		}
		else {
			polivoloTS1 = tora;
			polivolo1 = 0;
		}

		if (polivoloTS2 == 0) {
			polivoloTS2 = tora;
		}
		else if ((tora - polivoloTS2) < parameters.xronosPolivolo2) {
			if (polivolo2++ > parameters.maxPolivolo2) {
				if (!confirm('ΠΡΟΣΟΧΗ: επαναλαμβανόμενες κλήσεις σε ευρύτερο ' +
					'διάστημα. Να συνεχίσω τις προσπάθειες;')) {
					location.href = globals.server + 'error.php?minima=' +
						uri('Επαναλαμβανόμενες κλήσεις σε ευρύτερο διάστημα!');
					return;
				}
				polivoloTS2 = currentTimestamp();
				polivolo2 = 0;
				
			}
		}
		else {
			polivoloTS2 = tora;
			polivolo2 = 0;
		}

		if (isSet(dedomena) && isSet(dedomena.s)) {
			pektis.kapikia = (!(isSet(dedomena.s.p) && (dedomena.s.p == 0)));
			pektis.available = (!(isSet(dedomena.s.b) && (dedomena.s.b == 0)));
			pektis.blockImage = (isSet(dedomena.s.x) && dedomena.s.x);
		}

		if (isSet(dedomena.s.l)) {
			sinedria.load = dedomena.s.l;
		}

		if (isSet(dedomena.s.s)) {
			monitor.idia();
			Dedomena.schedule();
			return;
		}

		if (!Dedomena.checkPartidaPektis(dedomena)) {
			monitor.lathos();
			Dedomena.schedule(true);
			return;
		}

		monitor.freska();
		Partida.processDedomena(dedomena);
		Dianomi.processDedomena(dedomena);
		Kinisi.processDedomena(dedomena);
		Prosklisi.processDedomena(dedomena);
		Sxesi.processDedomena(dedomena);
		Permes.processDedomena(dedomena);
		Rebelos.processDedomena(dedomena);
		Trapezi.processDedomena(dedomena);
		Sizitisi.processDedomena(dedomena);
		Kafenio.processDedomena(dedomena);

		Pexnidi.processDedomena();
		this.setLastFilo(dedomena);
		Partida.updateHTML();
		Trapezi.updateHTML();
		Prefadoros.display();
		if (pexnidi.lastFilo != 0) {
			this.processLastFilo();
		}
		else {
			Pexnidi.processFasi();
			Dedomena.schedule();
		}
	};

	this.setLastFilo = function(dedomena) {
		pexnidi.lastFilo = 0;
		if (pexnidi.akirosi) { return; }
		if (kinisi.length <= 0) { return; }
		if (notSet(dedomena.k) && notSet(dedomena.kn)) { return; }
		if (kinisi[kinisi.length - 1].i != 'ΦΥΛΛΟ') { return; }

		var p = partida.pam[kinisi[kinisi.length - 1].p];
		if (p != 1) {
			pexnidi.lastFilo = p;
			return;
		}

		// Αν ο χρήστης είναι στο νότο τότε κρατάμε το τελευταίο
		// φύλλο μόνο για τους θεατές καθώς ο παίκτης έχει το
		// δικό του animation.
		if (notTheatis()) { return; }

		pexnidi.lastFilo = 1;

		// Τώρα θα εντοπίσουμε το φύλλο με βάση το source της εικόνας,
		// εφόσον γνωρίζουμε ποιο φύλλο είναι. Αυτό όμως αποτυγχάνει
		// στην περίπτωση των κλειστών φύλλων, οπότε βάζουμε κάποια
		// default τιμή. Σκοπός είναι να κρατήσουμε κάπου τη θέση του
		// παιζόμενου (από το νότο) φύλλου, ώστε να μπορέσουμε μετά
		// να ξεκινήσουμε το animation από όσο το δυνατόν ορθότερη θέση.
		pexnidi.lastFiloLeft = '3.6cm';
		var filo = kinisi[kinisi.length - 1].d;
		var re = new RegExp(filo + '.png$');
		$('.fila1Area img').each(function() {
			if (this.src.match(re)) {
				pexnidi.lastFiloLeft = $(this).parent().position().left + 'px';
				return false;
			}
		});
	};

	this.processLastFilo = function() {
		var x = $('#bazaFilo' + pexnidi.lastFilo).find('img').get(0);
		if (notSet(x)) {
			Pexnidi.processFasi();
			Dedomena.schedule();
			return;
		}

		var to_tl = $(x).position();
		switch (pexnidi.lastFilo) {
		case 1:
			x.style.top = '5.0cm';
			x.style.left = pexnidi.lastFiloLeft;
			break;
		case 2:
			x.style.top = '-2.6cm';
			x.style.left = '5.6cm';
			break;
		case 3:
			x.style.top = '-2.6cm';
			x.style.left = '-1.6cm';
			break;
		}
		var from_tl = $(x).position();

		var delay = 200;
		x.style.visibility = 'visible';
		$(x).animate({
			top: '+=' + (to_tl.top - from_tl.top),
			left: '+=' + (to_tl.left - from_tl.left)
		}, delay, function() {
			Pexnidi.processFasi();
			Dedomena.schedule(false, delay);
		});
	};

	this.checkPartidaPektis = function(dedomena) {
		if (notSet(dedomena.partida)) { return true; }
		if (notSet(dedomena.partida.k)) { return true; }
		if (notPektis()) {
			mainFyi('Επεστράφη παρτίδα, ενώ ο παίκτης είναι ακαθόριστος');
			return false;
		}

		// Αν ο παίκτης συμμετέχει ως θεατής, τότε δεν πειράζει
		// τυχόν λάθος θέση.
		if (isSet(dedomena.partida.t) && (dedomena.partida.t != 0)) {
			return true;
		}

		if (notSet(dedomena.partida.h) ||
			(eval('dedomena.partida.p' + dedomena.partida.h) != pektis.login)) {
			mainFyi('Ο παίκτης δεν βρίσκεται στην πρώτη θέση');
			return false;
		}

		return true;
	};

	this.sessionAlive = function() {
		var req = new Request('prefadoros/sessionAlive');
		req.send();
		mainFyi('session recharged');
	};
};

function isFreska(dedomena) {
	return(isSet(dedomena) && isSet(dedomena.s) &&
		isSet(dedomena.s.f) && (dedomena.s.f == 1));
}

function notFreska(dedomena) {
	return(!isFreska(dedomena));
}
