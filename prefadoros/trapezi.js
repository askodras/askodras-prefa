var Trapezi = new function() {
	this.html = '';

	this.processDedomena = function(dedomena) {
		if (isSet(dedomena.trapezi)) {
			trapezi = dedomena.trapezi;
			Trapezi.updateHTML();
			return;
		}

		var trapezi1 = [];
		var ixos = null;

		// Αν έχει επιστραφεί array "trapeziNew", τότε πρόκειται για νέες
		// εγγραφές τις οποίες θα εμφανίσω πρώτες.
		if (isSet(dedomena.trapeziNew)) {
			ixos = 'pop';
			for (var i = 0; i < dedomena.trapeziNew.length; i++) {
				trapezi1[trapezi1.length] = dedomena.trapeziNew[i];
			}
		}

		// Διατρέχω το παλιό array "trapezi" και ελέγχω αν κάποιες από τις
		// εγγραφές του έχουν τροποποιηθεί ή διαγραφεί. Για τις εγγραφές
		// που εμφανίζονται να έχουν τροποποιηθεί (array "trapeziMod") περνάω
		// στο νέο array τα νέα δεδομένα, ενώ τις εγγραφές που εμφανίζονται
		// να έχουν διαγραφεί τις αγνοώ· τις υπόλοιπες εγγραφές απλώς τις
		// αντιγράφω στο νέο array.
		for (var i = 0; i < trapezi.length; i++) {
			if (isSet(dedomena.trapeziDel) &&
				(('t' + trapezi[i].k) in dedomena.trapeziDel)) {
				continue;
			}

			if (isSet(dedomena.trapeziMod) &&
				(('t' + trapezi[i].k) in dedomena.trapeziMod)) {
				trapezi1[trapezi1.length] = dedomena.trapeziMod['t' + trapezi[i].k];
				continue;
			}

			trapezi1[trapezi1.length] = trapezi[i];
		}

		trapezi = trapezi1;
		delete trapezi1;
		Trapezi.updateHTML();

		if (notSet(ixos) && isSet(dedomena.trapeziDel)) {
			ixos = 'blioup';
		}
		if (isSet(ixos) && (Prefadoros.show == 'kafenio')) { playSound(ixos); }
	};

	this.updateHTML = function() {
		Trapezi.html = '<div class="kafenio">';
		if (notPartida()) { Trapezi.html += Tools.miaPrefaHTML(true); }
		if (rebelos.length > 0) {
			Trapezi.html += '<div class="kafenioRebels">';
			for (var i = 0; i < rebelos.length; i++) {
				if (notSet(rebelos[i].t)) {
					Trapezi.html += Trapezi.rebelosHTML(rebelos[i].l);
				}
			}
			Trapezi.html += '</div>';
		}

		for (var i = 0; i < trapezi.length; i++) {
			Trapezi.html += Trapezi.trapeziHTML(trapezi[i]);
			var protos = '<div class="kafenioRebels" style="margin-top: 0.2cm;">';
			for (var j = 0; j < rebelos.length; j++) {
				if (isSet(rebelos[j].t) && (rebelos[j].t == trapezi[i].k)) {
					Trapezi.html += protos;
					protos = '';
					Trapezi.html += Trapezi.rebelosHTML(rebelos[j].l, true);
				}
			}
			if (protos === '') { Trapezi.html += '</div>'; }
		}
		Trapezi.html += '</div>';
	};

	this.trapeziHTML = function(t) {
		var theatis = (isTheatis() && (t.k == partida.k));
		var html = '';
		html += '<hr class="kafenioTrapeziLine" />';
		html += '<div class="kafenioTrapezi';
		if (t.r) { html += ' kafenioTrapeziPrive'; }
		if (theatis) { html += ' kafenioTrapeziTheatis'; }
		html += '">';
		html += '<div class="kafenioTrapeziInfo';
		if (theatis) { html += ' kafenioTrapeziInfoTheatis'; }
		html += '"';
		if (isSet(t.k) && isSet(t.s)) {
			html += ' onmouseover="Trapezi.fotise(this);"';
			html += ' onmouseout="Trapezi.xefotise(this);"';
			html += ' style="cursor: pointer;"';
			html += ' title="';
			if (theatis) {
				html += 'Αποχώρηση ως θεατής από το τραπέζι ' + t.k;
			}
			else {
				html += 'Θεατής στο τραπέζι ' + t.k;
			}
			html += '"';
			html += ' onclick="Trapezi.theatis(' + t.k + ');"';
		}
		html += '>';
		if (isSet(t.k) && isSet(t.s)) {
			html += (t.k + '#' + t.s);
		}
		else {
			html += Tools.xromataHTML('0.5cm');
		}
		html += '</div>';
		for (var i = 1; i <= 3; i++) {
			var p = eval('t.p' + i);
			html += '<div class="kafenioPektis';
			if (theatis) { html += ' theatis'; }
			if (notSet(eval('t.o' + i))) { html += ' offline'; }
			else if (isSet(eval('t.a' + i)) &&
				(eval('t.a' + i) != 1)) { html += ' oxiApodoxi'; }
			if (isPektis() && (p == pektis.login)) { html += ' ego'; }
			html += '"';
			if (p) { html += Trapezi.permesHTML(p); }
			html += '>';
			html += eval('t.p' + i) ? eval('t.p' + i) : '&nbsp;';
			html += '</div>';
		}
		html += '</div>';
		return html;
	};

	this.permesHTML = function(p) {
		var html = '';
		html += ' onmouseover="Trapezi.fotise(this);"';
		html += ' onmouseout="Trapezi.xefotise(this);"';
		html += ' onclick="Sxesi.permesWindow(\'' + p +
			'\', \'Γεια χαρά!\');"';
		html += ' title="Προσωπικό μήνυμα στο χρήστη &quot;' +
			p + '&quot;"';
		html += ' style="cursor: pointer;"';
		return html;
	};

	this.fotise = function(div) {
		if (notSet(div)) { return; }
		div.oldClass = div.getAttribute('class');
		if (notSet(div.oldClass)) { return; }
		div.setAttribute('class', div.oldClass + ' kafenioFotismeno');
	};

	this.xefotise = function(div) {
		if (notSet(div) || notSet(div.oldClass)) { return; }
		div.setAttribute('class', div.oldClass);
	};

	this.theatis = function(t) {
		var ico = getelid('controlPanelIcon');
		if (notSet(ico)) { return; }
		ico.src = globals.server + 'images/working.gif';

		var req = new Request('trapezi/theatis');
		req.xhr.onreadystatechange = function() {
			Trapezi.theatisCheck(req, ico);
		};

		req.send('trapezi=' + t);
	};

	this.theatisCheck = function(req, ico) {
		if (req.xhr.readyState != 4) { return; }
		ico.src = globals.server + 'images/controlPanel/4Balls.png';
		var rsp = req.getResponse();
		if (rsp == 'partida') {
			Prefadoros.showPartida();
		}
		else if (rsp) {
			mainFyi(rsp);
			errorIcon(ico);
			playSound('beep');
		}
	};

	this.rebelosHTML = function(t, theatis) {
		var html = '<div class="kafenioPektis rebelos';
		if (isSet(theatis)) { html += ' theatis'; }
		if (isPektis() && (t == pektis.login)) { html += ' ego'; }
		html += '"';
		html += Trapezi.permesHTML(t);
		html += '>';
		html += t;
		html += '</div>';
		return html;
	};

	this.adio = function() {
		Trapezi.html = '<div class="kafenio">';
		Trapezi.html += '<div style="padding: 0.4cm;">' +
			Tools.miaPrefaHTML() + '</div>';
		var trapezi = {k:null,s:null,p1:null,p2:null,p3:null};
		for (var i = 0; i < 6; i++) {
			Trapezi.html += Trapezi.trapeziHTML(trapezi);
		}
		Trapezi.html += '</div>';
	};
};

var Rebelos = new function() {
	this.processDedomena = function(dedomena) {
		if (isSet(dedomena.rebelos)) {
			rebelos = dedomena.rebelos;
			return;
		}

		var rebelos1 = [];
		var ixos = null;

		// Αν έχει επιστραφεί array "rebelosNew", τότε πρόκειται για νέες
		// εγγραφές τις οποίες θα εμφανίσω πρώτες.
		if (isSet(dedomena.rebelosNew)) {
			ixos = 'pop';
			for (var i = 0; i < dedomena.rebelosNew.length; i++) {
				rebelos1[rebelos1.length] = dedomena.rebelosNew[i];
			}
		}

		// Διατρέχω το παλιό array "rebelos" και ελέγχω αν κάποιες από τις
		// εγγραφές του έχουν τροποποιηθεί ή διαγραφεί. Για τις εγγραφές
		// που εμφανίζονται να έχουν τροποποιηθεί (array "rebelosMod") περνάω
		// στο νέο array τα νέα δεδομένα, ενώ τις εγγραφές που εμφανίζονται
		// να έχουν διαγραφεί τις αγνοώ· τις υπόλοιπες εγγραφές απλώς τις
		// αντιγράφω στο νέο array.
		for (var i = 0; i < rebelos.length; i++) {
			if (isSet(dedomena.rebelosDel) &&
				(rebelos[i].l in dedomena.rebelosDel)) {
				continue;
			}

			if (isSet(dedomena.rebelosMod) &&
				(rebelos[i].l in dedomena.rebelosMod)) {
				rebelos1[rebelos1.length] = dedomena.rebelosMod[rebelos[i].l];
				continue;
			}

			rebelos1[rebelos1.length] = rebelos[i];
		}

		rebelos = rebelos1;
		delete rebelos1;

		if (notSet(ixos) && isSet(dedomena.rebelosDel)) {
			ixos = 'blioup';
		}
		if (isSet(ixos) && (Prefadoros.show == 'kafenio')) { playSound(ixos); }
	};
};

Trapezi.adio();
