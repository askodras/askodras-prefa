<?php
// Αρχικά θέτουμε το content type σε plain/text καθώς μπορεί να
// παρουσιαστούν σφάλματα τα οποία θα τυπωθούν ως απλό κείμενο.
// Αργότερα και ακριβώς πριν την αποστολή των data σε JSON format
// θα αλλάξουμε το content-type σε application/json.
header('Content-type: text/plain; charset=utf-8');

// Δεν θέλουμε session σε αυτό το πρόγραμμα, καθώς κάτι τέτοιο θα
// μπλοκάρει όλα τα υπόλοιπα requests που χρησιμοποιούν session
// cookies. Αυτό συμβαίνει διότι το παρόν πρόγραμμα τρέχει για
// αρκετά μεγάλο χρονικό διάστημα (αρκετά δευτερόλεπτα, ίσως και
// λεπτά) εκτελώντας συνεχείς επαναλαμβανόμενους ελέγχους στην
// database με μικρές ενδιάμεσες διακοπές της τάξης των 300 περίπου
// milliseconds. Εφόσον βρεθούν αλλαγές επιστρέφονται τα σχετικά
// δεδομένα και δρομολογείται σχεδόν αμέσως νέος κύκλος ελέγχου,
// ενώ αν δεν βρεθούν αλλαγές, το πρόγραμμα τερματίζει όταν φτάσει
// στον maximum χρόνο που έχει οριστεί και επαδρομολογείται πάλι
// νέος κύκλος ελέγχου.
global $no_session;
$no_session = TRUE;

require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../trapezi/trapezi.php';
require_once '../prefadoros/partida.php';
require_once '../prefadoros/prosklisi.php';
require_once '../prefadoros/sxesi.php';
require_once '../prefadoros/permes.php';
require_once '../prefadoros/trapezi.php';
require_once '../prefadoros/rebelos.php';
require_once '../prefadoros/prefadoros.php';
set_globals();

global $sinedria;
$sinedria = Globals::perastike_check('sinedria');

global $id;
$id = Globals::perastike_check('id');

// Στο σημείο αυτό θα προσπελάσουμε τον παίκτη με βάση το login
// name που περνάμε ως παράμετρο και θα ενημερώσουμε τόσο το
// πεδίο "poll" του παίκτη, που δείχνει πότε ο παίκτης έκανε
// την τελευταία κλήση για δεδομένα στον server, όσο και το
// πεδίο "id" της συνεδρίας, που δείχνει τον τελευταίο κύκλο
// ελέγχου σταπλαίσια της τρέχουσας συνεδρίας.
Prefadoros::pektis_check(Globals::perastike_check('login'));
$globals->pektis->poll_update($sinedria, $id);
Prefadoros::set_trapezi();

// Αν έχει περαστεί παράμετρος "feska", τότε ζητάμε όλα τα δεδομένα
// χωρίς να μπούμε στη διαδικασία της σύγκρισης με προηγούμενα
// δεδομένα της ίδιας συνεδρίας, οπότε μαζεύουμε τα τρέχοντα
// δεδομένα και τα επιστρέφουμε στον client.
if (Globals::perastike('freska')) {
	freska_dedomena(torina_dedomena());
	die(0);
}

// Στο σημείο αυτό πρόκειται για κλασικό αίτημα αποστολής δεδομένων
// όπου θα πρέπει να μαζευτούν τα δεδομένα, να συγκριθούν με τα
// προηγούμενα δεδομένα της τρέχουσας συνεδρίας και, εφόσον υπάρχουν
// αλλαγές, να επιστραφούν τα νέα δεδομένα. Αν, παρ' όλα αυτά δεν
// μπορέσουμε να διαβάσουμε τα προηγούμενα δεδομένα από το σχετικό
// file, τότε επιστρέφουμε όλα τα δεδομένα, όπως ακριβώς κάναμε και
// στην περίπτωση αιτήματος "φρέσκων" δεδομένων. Τα αρχεία καταγραφής
// δεδομένων βρίσκονται στο directory "dedomena" και φέρουν το login
// name του παίκτη.
$prev = new Dedomena();
if (!$prev->diavase()) {
	freska_dedomena(torina_dedomena());
	die(0);
}

// Είμαστε στη φάση που έχουμε διαβάσει επιτυχώς από το σχετικό αρχείο
// τα δεδομένα της προηγούμενης αποστολής της τρέχουσας συνεδρίας και
// ξεκινάμε τον κύκλο ελέγχου με τα τρέχοντα στοιχεία της database.
$ekinisi = time();
do {
	unset($globals->trapezi);
	Prefadoros::set_trapezi();
	$curr = torina_dedomena();
	if ($curr != $prev) {
		diaforetika_dedomena($curr, $prev);
		die(0);
	}

	// Εφόσον δεν υπάρχουν διαφορές, αφήνουμε ένα μικρό
	// χρονικό διάστημα και ξεκινάμε νέο μάζεμα δεδομένων
	// και συνακόλουθο έλεγχο. Εαν, όμως, ο συνολικός χρόνος
	// του τρέχοντος κύκλου ελέγχου/αποστολής έχει υπερβεί το
	// καθορισμένο maximum, τότε διακόπτουμε και επιστρέφουμε
	// δεδομένα που δείχνουν ότι τα δεδομένα παρέμειναν ίδια.
	// Ο client θα δρομολογήσει νέο κύκλο ελέγχου/αποστολής
	// στοιχείων.
	if ((time() - $ekinisi) > XRONOS_DEDOMENA_MAX) {
		print_epikefalida();
		print ",s:1}";
		die(0);
	}

	usleep(XRONOS_DEDOMENA_TIC);

	// Πριν προχωρήσουμε στο μάζεμα των στοιχείων και στον
	// συνακόλουθο έλεγχο, ελέγχουμε μήπως έχει δρομολογηθεί
	// ήδη νεότερος κύκλος ελέγχου/αποστολής στα πλαίσια
	// της τρέχουσας συνεδρίας. Αν όντως συμβαίνει κάτι
	// τέτοιο, τότε το πρόγραμμα απλώς τερματίζει επιστρέφοντας
	// σχετικά στοιχεία τερματισμού στον client, ώστε αυτός να
	// αγνοήσει τη συγκεκριμένη απάντηση.
	check_neotero_id();
} while (TRUE);

function check_neotero_id() {
	global $globals;
	global $sinedria;
	global $id;

	$query = "SELECT `ενημέρωση` FROM `συνεδρία` WHERE `κωδικός` = " . $sinedria;
	$result = $globals->sql_query($query);
	$row = mysqli_fetch_array($result, MYSQLI_NUM);
	if (!$row) {
		Globals::fatal('ακαθόριστη συνεδρία');
	}

	mysqli_free_result($result);
	if ($row[0] != $id) {
		print_epikefalida($sinedria, $id);
		print "}";
		die(0);
	}
}

function print_epikefalida() {
	global $globals;
	global $sinedria;
	global $id;

	// Για να έχουμε ενημερωμένα στοιχεία σχετικά με την κατάσταση
	// του παίκτη επαναπροσπελαύνουμε τον παίκτη πριν την επιστροφή.
	unset($globals->pektis);
	Prefadoros::pektis_check();

	header('Content-type: application/json; charset=utf-8');
	print "sinedria:{k:{$sinedria},i:{$id}";
	if ($globals->pektis->kapikia != 'YES') { print ",p:0"; }
	if ($globals->pektis->katastasi != 'AVAILABLE') { print ",b:0"; }
}

class Dedomena {
	public $partida;
	public $prosklisi;
	public $sxesi;
	public $permes;
	public $trapezi;
	public $rebelos;

	public function __construct() {
		$this->partida = NULL;
		$this->prosklisi = array();
		$this->sxesi = array();
		$this->permes = array();
		$this->trapezi = array();
		$this->rebelos = array();
	}

	public function diavase() {
		global $globals;

		if (!$globals->klidoma($globals->pektis->login)) {
			Globals::fatal('cannot lock in order to write data file');
		}

		$fh = self::open_file('r');
		if (!$fh) {
			$globals->xeklidoma($globals->pektis->login);
			return(FALSE);
		}

		while ($line = Globals::get_line($fh)) {
			switch ($line) {
			case '@PARTIDA@':	Partida::diavase($fh, $this->partida); break;
			case '@PROSKLISI@':	Prosklisi::diavase($fh, $this->prosklisi); break;
			case '@SXESI@':		Sxesi::diavase($fh, $this->sxesi); break;
			case '@PERMES@':	Permes::diavase($fh, $this->permes); break;
			case '@TRAPEZI@':	Kafenio::diavase($fh, $this->trapezi); break;
			case '@REBELOS@':	Rebelos::diavase($fh, $this->rebelos); break;
			}
		}

		fclose($fh);
		$globals->xeklidoma($globals->pektis->login);
		return(TRUE);
	}

	public function grapse() {
		global $globals;

		if (!$globals->klidoma($globals->pektis->login)) {
			Globals::fatal('cannot lock in order to write data file');
		}

		$fh = self::open_file('w');
		if (!$fh) {
			$globals->xeklidoma($globals->pektis->login);
			Globals::fatal('cannot write data file');
		}

		Partida::grapse($fh, $this->partida);
		Prosklisi::grapse($fh, $this->prosklisi);
		Sxesi::grapse($fh, $this->sxesi);
		Permes::grapse($fh, $this->permes);
		Kafenio::grapse($fh, $this->trapezi);
		Rebelos::grapse($fh, $this->rebelos);

		fclose($fh);
		$globals->xeklidoma($globals->pektis->login);
	}

	private static function open_file($rw) {
		global $globals;

		$fname = '../dedomena/' . $globals->pektis->login;
		$fh = @fopen($fname, $rw);
		return($fh);
	}
}

function torina_dedomena() {
	$dedomena = new Dedomena();
	$dedomena->partida = Partida::process();
	$dedomena->prosklisi = Prosklisi::process();
	$dedomena->sxesi = Sxesi::process();
	$dedomena->permes = Permes::process();
	$dedomena->trapezi = Kafenio::process();
	$dedomena->rebelos = Rebelos::process();
	return($dedomena);
}

function freska_dedomena($dedomena) {
	$dedomena->grapse();
	print_epikefalida();
	print ",f:1}";

	Partida::set_thesi_map($dedomena->partida);
	Partida::print_json_data($dedomena->partida);
	Prosklisi::print_json_data($dedomena->prosklisi);
	Sxesi::print_json_data($dedomena->sxesi);
	Permes::print_json_data($dedomena->permes);
	Kafenio::print_json_data($dedomena->trapezi);
	Rebelos::print_json_data($dedomena->rebelos);
}

function diaforetika_dedomena($curr, $prev) {
	$curr->grapse();
	print_epikefalida();
	print "}";

	Partida::set_thesi_map($curr->partida);
	Partida::print_json_data($curr->partida, $prev->partida);
	Prosklisi::print_json_data($curr->prosklisi, $prev->prosklisi);
	Sxesi::print_json_data($curr->sxesi, $prev->sxesi);
	Permes::print_json_data($curr->permes, $prev->permes);
	Kafenio::print_json_data($curr->trapezi, $prev->trapezi);
	Rebelos::print_json_data($curr->rebelos, $prev->rebelos);
}
?>
