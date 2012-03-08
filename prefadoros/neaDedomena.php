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
require_once '../prefadoros/dianomi.php';
require_once '../prefadoros/kinisi.php';
require_once '../prefadoros/prosklisi.php';
require_once '../prefadoros/sxesi.php';
require_once '../prefadoros/permes.php';
require_once '../prefadoros/trapezi.php';
require_once '../prefadoros/rebelos.php';
require_once '../prefadoros/sizitisi.php';
require_once '../prefadoros/prefadoros.php';

global $monitor_fh;
open_monitor_file();

set_globals();
$globals->time_dif = Globals::perastike_check('timeDif');

global $sinedria;
$sinedria = new Sinedria();
$sinedria->kodikos = Globals::perastike_check('sinedria');

global $id;
$id = Globals::perastike_check('id');

// Όσον αφορά στη δημόσια συζήτηση (ΔΣ), φροντίζουμε να μας στέλνει ο client
// τον κωδικό του παλαιότερου σχολίου που είναι το πρώτο από αυτά που
// παρέλαβε την πρώτη φορά που ζήτησε δεδομένα στην τρέχουσα συνεδρία.
// Έτσι, μπορούμε να περιοριζόμαστε στα σχόλια της ΔΣ που είναι νεότερα
// από αυτό το αυτό το σχόλιο (συμπεριλαμβανομένου και αυτού).
//
// Την πρώτη φορά, δηλαδή κατά την εκκίνηση μιας συνεδρίας, ο client
// στέλνει κωδικό μηδέν, οπότε θα μαζευτούν τα τρέχοντα σχόλια της ΔΣ
// σύμφωνα με τη σταθερά "KAFENIO_TREXONTA_SXOLIA", ενώ μετά την πρώτη
// παραλαβή ο client θα εντοπίσει το παλαιότερο από τα σχόλια της ΔΣ
// αυτής της (πρώτης) παραλαβής και θα μας στέλνει έτοιμο τον κωδικό
// του παλαιοτέρου σχολίου, ώστε να ελέγχεται η ΔΣ από εκείνο το
// σχόλιο και ύστερα.
global $kafenio_apo;
$kafenio_apo = Globals::perastike_check('kafenioApo');

// Η global μεταβλητή "kiklos" μετράει τους κύκλους ελέγχου των
// δεδομένων. Είναι global καθώς την ελέγχω και μέσα από κάποιες
// από τις επιμέρους διαδικασίες.

global $kiklos;
$kiklos = 0;

// Στο σημείο αυτό θα προσπελάσουμε τον παίκτη με βάση το login
// name που περνάμε ως παράμετρο και θα ενημερώσουμε τόσο το
// πεδίο "poll" του παίκτη, που δείχνει πότε ο παίκτης έκανε
// την τελευταία κλήση για δεδομένα στον server, όσο και το
// πεδίο "id" της συνεδρίας, που δείχνει τον τελευταίο κύκλο
// ελέγχου σταπλαίσια της τρέχουσας συνεδρίας.
Prefadoros::pektis_check(Globals::perastike_check('login'));
Prefadoros::set_trapezi();
$globals->pektis->poll_update($sinedria->kodikos, $id);
check_neotero_id();

// Αν έχει περαστεί παράμετρος "freska", τότε ζητάμε όλα τα δεδομένα
// χωρίς να μπούμε στη διαδικασία της σύγκρισης με προηγούμενα
// δεδομένα της ίδιας συνεδρίας, οπότε μαζεύουμε τα τρέχοντα
// δεδομένα και τα επιστρέφουμε στον client.
monitor_write("freska");
if (Globals::perastike("freska")) {
	freska_dedomena(torina_dedomena());
	monitor_write("exit");
	$globals->klise_fige();
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
monitor_write("read");
$prev = new Dedomena();
if (!$prev->diavase()) {
	freska_dedomena(torina_dedomena());
	monitor_write("exit");
	$globals->klise_fige();
}

// Είμαστε στη φάση που έχουμε διαβάσει επιτυχώς από το σχετικό αρχείο
// τα δεδομένα της προηγούμενης αποστολής της τρέχουσας συνεδρίας και
// ξεκινάμε τον κύκλο ελέγχου με τα τρέχοντα στοιχεία της database.

$ekinisi = time();
usleep(XRONOS_DEDOMENA_TIC);
do {
	// Πριν προχωρήσουμε στο μάζεμα των στοιχείων και στον
	// συνακόλουθο έλεγχο, ελέγχουμε μήπως έχει δρομολογηθεί
	// ήδη νεότερος κύκλος ελέγχου/αποστολής στα πλαίσια
	// της τρέχουσας συνεδρίας. Αν όντως συμβαίνει κάτι
	// τέτοιο, τότε το πρόγραμμα απλώς τερματίζει επιστρέφοντας
	// σχετικά στοιχεία τερματισμού στον client, ώστε αυτός να
	// αγνοήσει τη συγκεκριμένη απάντηση.
	check_neotero_id();

	unset($globals->trapezi);
	Prefadoros::set_trapezi();
	$globals->pektis->check_dirty();
	$curr = torina_dedomena($prev);
	monitor_write("compare");
	if ($curr != $prev) {
		// Αποφεύγουμε κινήσεις τύπου "ΦΥΛΛΟ" και "ΠΛΗΡΩΜΗ" μετά
		// από κίνηση τύπου "ΜΠΑΖΑ" μαζί στην ίδια αποστολή.
		$curr->kinisi = Kinisi::fix_baza_filo($curr->kinisi, $prev->kinisi);
		diaforetika_dedomena($curr, $prev);
		monitor_write("exit");
		$globals->klise_fige();
	}

	// Εφόσον δεν υπάρχουν διαφορές, αφήνουμε ένα μικρό
	// χρονικό διάστημα και ξεκινάμε νέο μάζεμα δεδομένων
	// και συνακόλουθο έλεγχο. Εαν, όμως, ο συνολικός χρόνος
	// του τρέχοντος κύκλου ελέγχου/αποστολής έχει υπερβεί το
	// καθορισμένο maximum, τότε διακόπτουμε και επιστρέφουμε
	// δεδομένα που δείχνουν ότι τα δεδομένα παρέμειναν ίδια.
	// Ο client θα δρομολογήσει νέο κύκλο ελέγχου/αποστολής
	// στοιχείων.
	$elapsed = time() - $ekinisi;
	if ($elapsed > XRONOS_DEDOMENA_MAX) {
		print_epikefalida();
		print ",s:1}";
		monitor_write("exit (timeout)");
		$globals->klise_fige();
	}

	$kiklos++;
	if ($kiklos < 5) {
		usleep(XRONOS_DEDOMENA_TIC);
	}
	elseif ($elapsed < 5) {
		usleep(XRONOS_DEDOMENA_TIC2);
	}
	elseif ($elapsed < 10) {
		usleep(XRONOS_DEDOMENA_TIC3);
	}
	else {
		usleep(XRONOS_DEDOMENA_TIC4);
	}
} while (TRUE);

function check_neotero_id() {
	global $globals;
	global $sinedria;
	global $id;

	if (!$sinedria->fetch()) {
		print_epikefalida();
		print ",fatalError: 'Ακαθόριστη συνεδρία (" . $sinedria->kodikos .
			"). Δοκιμάστε επαναφόρτωση της σελίδας'}";
		$globals->klise_fige();
	}

	if ($sinedria->enimerosi != $id) {
		print_epikefalida();
		print "}";
		$globals->klise_fige();
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
	print "sinedria:{k:{$sinedria->kodikos},i:{$id}";
	if ($globals->pektis->kapikia != 'YES') { print ",p:0"; }
	if ($globals->pektis->katastasi != 'AVAILABLE') { print ",b:0"; }
	if ($globals->pektis->blockimage) { print ",x:true"; }
}

class Dedomena {
	public $partida;
	public $dianomi;
	public $kinisi;
	public $prosklisi;
	public $sxesi;
	public $permes;
	public $trapezi;
	public $rebelos;
	public $sizitisi;
	public $kafenio;

	public function __construct() {
		$this->partida = NULL;
		$this->dianomi = array();
		$this->kinisi = array();
		$this->prosklisi = array();
		$this->sxesi = array();
		$this->permes = array();
		$this->trapezi = array();
		$this->rebelos = array();
		$this->sizitisi = array();
		$this->kafenio = array();
	}

	public function diavase() {
		global $globals;

		if (!$globals->klidoma($globals->pektis->login)) {
			Globals::fatal('cannot lock in order to read data file');
		}

		$fh = self::open_file('r');
		if (!$fh) {
			$globals->xeklidoma($globals->pektis->login);
			return(FALSE);
		}

		while ($line = Globals::get_line($fh)) {
			switch ($line) {
			case '@PARTIDA@':	Partida::diavase($fh, $this->partida); break;
			case '@DIANOMI@':	Dianomi::diavase($fh, $this->dianomi); break;
			case '@KINISI@':	Kinisi::diavase($fh, $this->kinisi); break;
			case '@PROSKLISI@':	Prosklisi::diavase($fh, $this->prosklisi); break;
			case '@SXESI@':		Sxesi::diavase($fh, $this->sxesi); break;
			case '@PERMES@':	Permes::diavase($fh, $this->permes); break;
			case '@TRAPEZI@':	Kafenio::diavase($fh, $this->trapezi); break;
			case '@REBELOS@':	Rebelos::diavase($fh, $this->rebelos); break;
			case '@SIZITISI@':	Sizitisi::diavase($fh, $this->sizitisi); break;
			case '@KAFENIO@':	Sizitisi::diavase($fh, $this->kafenio); break;
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

		// Για λόγους ασφαλείας ονομάζουμε τα αρχεία με επίθεμα ".php"
		// και γράφουμε στην πρώτη γραμμή ένα πολύ απλό πρόγραμμα με
		// το οποίο εκτυπώνεται σελίδα oops σε περίπτωση που κάποιος
		// επιχειρεί να προβάλλει ή να κατεβάσει το περιεχόμενο.
		Globals::put_line($fh, '<?php header("Location: ' . $globals->server .
			'lib/oops.php"); die("Oops!"); ?>');

		Partida::grapse($fh, $this->partida);
		Dianomi::grapse($fh, $this->dianomi);
		Kinisi::grapse($fh, $this->kinisi);
		Prosklisi::grapse($fh, $this->prosklisi);
		Sxesi::grapse($fh, $this->sxesi);
		Permes::grapse($fh, $this->permes);
		Kafenio::grapse($fh, $this->trapezi);
		Rebelos::grapse($fh, $this->rebelos);
		Sizitisi::grapse($fh, $this->sizitisi, 'SIZITISI');
		Sizitisi::grapse($fh, $this->kafenio, 'KAFENIO');

		fclose($fh);
		$globals->xeklidoma($globals->pektis->login);
	}

	private static function open_file($rw) {
		global $globals;

		$fname = "../dedomena/" . $globals->pektis->login . ".php";
		$fh = @fopen($fname, $rw);
		return($fh);
	}
}

function torina_dedomena($prev = NULL) {
	global $globals;
	global $sinedria;

	$dedomena = new Dedomena();

	$dedomena->partida = Partida::process();

	$dedomena->dianomi = Dianomi::process();
	$globals->dianomi = $dedomena->dianomi;

	$dedomena->kinisi = Kinisi::process();
	$globals->kinisi = $dedomena->kinisi;

	if (($prev == NULL) || $globals->pektis->prosklidirty) {
		$dedomena->prosklisi = Prosklisi::process();
	}
	else {
		$dedomena->prosklisi = $prev->prosklisi;
	}

	if (($prev == NULL) || $globals->pektis->minimadirty) {
		$dedomena->permes = Permes::process();
	}
	else {
		$dedomena->permes = $prev->permes;
	}

	if (($prev == NULL) || $globals->pektis->sxesidirty) {
		$dedomena->sxesi = Sxesi::process();
		$sxesi_same = FALSE;
	}
	else {
		$dedomena->sxesi = $prev->sxesi;
		$sxesi_same = TRUE;
	}

	$dedomena->trapezi = Kafenio::process();
	$dedomena->rebelos = Rebelos::process();

	if ($prev == NULL) {
		$dedomena->sizitisi = Sizitisi::process_sizitisi();
		$dedomena->kafenio = Sizitisi::process_kafenio();
	}
	elseif ($sinedria->sizitisidirty > 0) {
		$dedomena->sizitisi = Sizitisi::process_sizitisi();
		$dedomena->kafenio = Sizitisi::process_kafenio();
		$sinedria->clear_sizitisidirty();
	}
	elseif (($dedomena->partida != NULL) && (($prev->partida == NULL) ||
		($dedomena->partida->kodikos != $prev->partida->kodikos))) {
		$dedomena->sizitisi = Sizitisi::process_sizitisi();
		$dedomena->kafenio = $prev->kafenio;
	}
	else {
		$dedomena->sizitisi = $prev->sizitisi;
		$dedomena->kafenio = $prev->kafenio;
	}

	// Αν δεν έχουν ελεγχθεί οι σχέσεις θα πρέπει να ελεγχθούν τώρα,
	// εφόσον έχει αλλάξει κάτι που αφορά στους παίκτες.

	if ($sxesi_same && (($dedomena->trapezi != $prev->trapezi) ||
		($dedomena->rebelos != $prev->rebelos))) {
		$dedomena->sxesi = Sxesi::process();
	}

	return($dedomena);
}

function freska_dedomena($dedomena) {
	$dedomena->grapse();
	print_epikefalida();
	print ",f:1}";

	Partida::print_json_data($dedomena->partida);
	Dianomi::print_json_data($dedomena->dianomi);
	Kinisi::print_json_data($dedomena->kinisi);
	Prosklisi::print_json_data($dedomena->prosklisi);
	Sxesi::print_json_data($dedomena->sxesi);
	Permes::print_json_data($dedomena->permes);
	Kafenio::print_json_data($dedomena->trapezi);
	Rebelos::print_json_data($dedomena->rebelos);
	Sizitisi::sizitisi_json_data($dedomena->sizitisi);
	Sizitisi::kafenio_json_data($dedomena->kafenio);
}

function diaforetika_dedomena($curr, $prev) {
	$curr->grapse();
	print_epikefalida();
	print "}";

	Partida::print_json_data($curr->partida, $prev->partida);
	Dianomi::print_json_data($curr->dianomi, $prev->dianomi);
	Kinisi::print_json_data($curr->kinisi, $prev->kinisi);
	Prosklisi::print_json_data($curr->prosklisi, $prev->prosklisi);
	Sxesi::print_json_data($curr->sxesi, $prev->sxesi);
	Permes::print_json_data($curr->permes, $prev->permes);
	Kafenio::print_json_data($curr->trapezi, $prev->trapezi);
	Rebelos::print_json_data($curr->rebelos, $prev->rebelos);
	Sizitisi::sizitisi_json_data($curr->sizitisi, $prev->sizitisi);
	Sizitisi::kafenio_json_data($curr->kafenio, $prev->kafenio);
}

function open_monitor_file() {
	global $monitor_fh;
	$monitor_fh = NULL;
return;
	$pektis = Globals::perastike_check('login');
	if ($pektis != 'panos') {
		return;
	}

	$fname = '../dedomena/' . $pektis . ".mon";
	$monitor_fh = fopen($fname, "a");
	monitor_write("START");
}

function monitor_write($data = "") {
	global $monitor_fh;
	if (isset($monitor_fh)) {
		fwrite($monitor_fh, microtime(TRUE) . ": " . $data . "\n");
		fflush($monitor_fh);
	}
}

class Sinedria {
	public $kodikos;
	public $enimerosi;
	public $peknpat;
	public $pekstat;
	public $sizitisidirty;

	public function __construct() {
		unset($this->kodikos);
		unset($this->enimerosi);
		unset($this->peknpat);
		unset($this->pekstat);
		$this->sizitisidirty = 0;
	}

	public function fetch() {
		global $globals;
		static $stmnt = NULL;
		$errmsg = "Sinedria::fetch(): ";

		unset($this->enimerosi);
		unset($this->peknpat);
		unset($this->pekstat);
		unset($this->sizitisidirty);

		if ($stmnt == NULL) {
			$query = "SELECT `enimerosi`, `peknpat`, `pekstat`, `sizitisidirty` " .
				"FROM `sinedria` WHERE `kodikos` = ?";
			$stmnt = $globals->db->prepare($query);
			if (!$stmnt) {
				$globals->klise_fige($errmsg . $query . ": failed to prepare");
			}
		}

		$stmnt->bind_param("i", $this->kodikos);
		$stmnt->execute();
		$stmnt->bind_result($this->enimerosi, $peknpat, $this->pekstat, $this->sizitisidirty);
		while ($stmnt->fetch()) {
			$this->peknpat = $peknpat == '' ?
				NULL : ("%" . $globals->asfales($peknpat) . "%");
		}

		return(isset($this->enimerosi));
	}

	public function clear_sizitisidirty() {
		global $globals;
		static $stmnt = NULL;
		$errmsg = "Sinedria::clear_sizitisidirty(): ";

		if ($this->sizitisidirty <= 0) {
			return;
		}

		$this->sizitisidirty--;
		if ($stmnt == NULL) {
			$query = "UPDATE `sinedria` SET `sizitisidirty` = " .
				$this->sizitisidirty . " WHERE `kodikos` = " . $this->kodikos;
			$stmnt = $globals->db->prepare($query);
			if (!$stmnt) {
				$globals->klise_fige($errmsg . $query . ": failed to prepare");
			}
		}

		$stmnt->execute();
	}
}
?>
