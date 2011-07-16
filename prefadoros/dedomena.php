<?php
header('Content-type: text/plain; charset=utf-8');
global $no_session;
$no_session = TRUE;
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
set_globals();
Prefadoros::pektis_check();
$globals->pektis->poll_update();

global $id;
$id = Globals::perastike_check('id');

global $slogin;
$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";

$prev = new Dedomena();
if (!$prev->diavase()) {
	freska_dedomena(torina_dedomena());
	telos_ok();
}

$ekinisi = time();
do {
	$curr = torina_dedomena();
	if ($curr != $prev) {
		diaforetika_dedomena($curr, $prev);
		die(0);
	}
	usleep(XRONOS_DEDOMENA_TIC);
} while ((time() - $ekinisi) < XRONOS_DEDOMENA_MAX);

function telos_ok() {
	die('@OK');
}

function torina_dedomena() {
	$curr = new Dedomena();
	$curr->sxesi = process_sxesi();
	$curr->trapezi = process_trapezi();
	return($curr);
}

function freska_dedomena($x) {
	$x->grapse();
	header('Content-type: application/json; charset=utf-8');
	print_epikefalida();
	print ",freska:true";
	print "}";
	$x->print_json_data();
}

function print_epikefalida() {
	global $id;
	print "data:{id:{$id}";
}

if ($same) {
	print <<<DOC
	same: true
}
DOC;
	die('@OK');
}

print <<<DOC
	sxesi: [],
	trapezi: []
}
DOC;

class Sxesi {
	public $login;
	public $onoma;
	public $online;
	public $diathesimos;
	public $status;

	public static function is_online($idle) {
		return ($idle < XRONOS_PEKTIS_IDLE_MAX);
	}

	public function __construct() {
		$this->login = '';
		$this->onoma = '';
		$this->online = FALSE;
		$this->diathesimos = FALSE;
		$this->status = '';
	}

	public function set_from_dbrow($row, $energos, $status = '') {
		$this->login = $row['login'];
		$this->onoma = $row['όνομα'];
		$this->online = self::is_online($row['idle']);
		$this->diathesimos = array_key_exists($row['login'], $energos);
		$this->status = $status;
	}

	public function set_from_file($line) {
		$cols = explode("\t", $line);
		if (count($cols) != 5) {
			return(FALSE);
		}

		$this->login = $cols[0];
		$this->onoma = $cols[1];
		$this->online = self::is_online($cols[2]);
		$this->diathesimos = $cols[3];
		$this->status = $cols[4];
		return(TRUE);
	}

	public function print_raw_data($fh) {
		fwrite($fh, $this->login . "\t" . $this->onoma . "\t" .
			($this->online ? 1 : 0) . "\t" . $this->status);
	}

	public function print_json_data() {
		print "{l:'" . $this->login . "',n:'" . $this->onoma .
			"',o:" . ($this->online ? 'true' : 'false') .
			",s:'" . $this->status . "'}";
	}

	public static function energos_pektis() {
		global $globals;

		$pektis = array();
		$query = "SELECT `παίκτης1`, `παίκτης2`, `παίκτης3` " .
			"FROM `τραπέζι` WHERE `τέλος` IS NULL";
		$result = $globals->sql_query($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			for ($i = 0; $i < 3; $i++) {
				$pektis[$row[$i]] = TRUE;
			}
		}

		return($pektis);
	}
}

class Dedomena {
	public $sxesi;
	public $trapezi;

	public function __construct() {
		$this->sxesi = array();
		$this->trapezi = array();
	}

	public function diavase() {
		$fh = self::open_file('r');
		while ($line = fgets($fh)) {
			switch ($line) {
			case '@SXESI@':
				$this->diavase_sxesi($fh);
				break;
			case '@TRAPEZIA@':
				$this->diavase_trapezi($fh);
				break;
			}
		}
		fclose($fh);
	}

	private function diavase_sxesi($fh) {
		while ($line = fgets($fh)) {
			if ($line === '@END@') {
				return;
			}

			$s = new Sxesi();
			if ($s->set_from_file($line)) {
				unset($s);
			}
			else {
				$this->sxesi[] = $s;
			}
		}
	}

	private function diavase_trapezi($fh) {
		while ($line = fgets($fh)) {
			if ($line === '@END@') {
				return;
			}
		}
	}

	public function grapse() {
		$fh = self::open_file('w');
		$this->grapse_sxesi($fh);
		$this->grapse_trapezi($fh);
		fclose($fh);
	}

	private function grapse_sxesi($fh) {
		fwrite($fh, "@SXESI@\n");
		$n = count($this->sxesi);
		for ($i = 0; $i < $n; $i++) {
			$this->sxesi[$i]->print_raw_data($fh);
		}
	}

	private function grapse_trapezi($fh) {
		fwrite($fh, "@TRAPEZI@\n");
		$n = count($this->trapezi);
		for ($i = 0; $i < $n; $i++) {
			$this->trapezi[$i]->print_raw_data($fh);
		}
	}

	private static function open_file($rw) {
		global $globals;

		$fname = '../dedomena/' . $globals->pektis->login;
		$fh = fopen($fname, $rw);
		if (!isset($fh)) {
			Gobals::fatal($fname . ': cannot open file');
		}
		return($fh);
	}

	public function print_json_data() {
		$this->print_sxesi_json_data();
	}

	private function print_sxesi_json_data() {
		print ',sxesi:[';
		$n = count($this->sxesi);
		for ($i = 0; $i < $n; $i++) {
			if ($i > 0) {
				print ',';
			}
			$this->sxesi[$i]->print_json_data();
		}
		print ']';
	}
}

function process_sxesi() {
	global $globals;
	global $slogin;

	$sxesi = array();

	$query1 = "SELECT `login`, `όνομα`, (`poll` - NOW()) AS `idle` " .
		"FROM `παίκτης` WHERE 1 ";

	if (Globals::perastike('spat')) {
		$pat = "'%" . $globals->asfales($_REQUEST['spat']) . "%'";
		$query1 .= "AND ((`όνομα` LIKE " . $spat . ") OR " .
			"(`login` LIKE " . $spat . ")) ";
	}

	if (Globals::perastike('skat')) {
		$query1 .= "AND (`idle` < " . XRONOS_PEKTIS_IDLE_MAX . ") ";
	}

	$query2 = " ORDER BY `login`";

	$energos = Sxesi::energos_pektis();

	$query = $query1 . "AND (`login` IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
		"(`παίκτης` LIKE " . $slogin . ") AND " .
		"(`status` LIKE 'ΦΙΛΟΣ')))" . $query2;
	$result = $globals->sql_query($query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$s = new Sxesi;
		$s->set_from_dbrow($row, $energos, 'ΦΙΛΟΣ');
		$sxesi[] = $s;
	}

	// Αν έχει δοθεί name pattern ή κατάσταση online/available, τότε επιλέγω και
	// μη σχετιζόμενους παίκτες.

	if (Globals::perastike('spat') || Globals::perastike('skat')) {
		$query = $query1 . "AND (`login` NOT IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
			"(`παίκτης` LIKE " . $slogin . ")))" . $query2;
		$result = $globals->sql_query($query);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$s = new Sxesi;
			$s->set_from_dbrow($row, $energos);
			$sxesi[] = $s;
		}
	}

	$query = $query1 . "AND (`login` IN (SELECT `σχετιζόμενος` FROM `σχέση` WHERE " .
		"(`παίκτης` LIKE " . $slogin . ") AND " .
		"(`status` LIKE 'ΑΠΟΚΛΕΙΣΜΕΝΟΣ')))" . $query2;
	$result = $globals->sql_query($query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$s = new Sxesi;
		$s->set_from_dbrow($row, $energos, 'ΑΠΟΚΛΕΙΣΜΕΝΟΣ');
		$sxesi[] = $s;
	}

	return $sxesi;
}

function sxesi_dif($curr, $prev) {
	return(FALSE);
}

function process_trapezi() {
	$trapezi = array();
	return($trapezi);
}

function trapezi_dif($curr, $prev) {
	return(FALSE);
}
?>
