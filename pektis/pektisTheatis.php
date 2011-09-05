<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../trapezi/trapezi.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();

Prefadoros::pektis_check();
global $slogin;
$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";

if (!Prefadoros::set_trapezi()) {
	die('Ακαθόριστο τραπέζι');
}

@mysqli_autocommit($globals->db, FALSE);
if ($globals->trapezi->theatis == 1) {
	apo_theatis_pektis();
}
else {
	apo_pektis_theatis();
}
@mysqli_commit($globals->db);

function apo_pektis_theatis() {
	global $globals;
	global $slogin;

	// Επιβεβαιώνουμε ότι ο παίκτης όντως συμμετέχει στο τραπέζι.
	$pektis = 'pektis' . $globals->trapezi->thesi;
	if ($globals->trapezi->$pektis != $globals->pektis->login) {
		die('Δεν συμμετέχετε στο τραπέζι');
	}

	// Διαγράφουμε τυχόν εγγραφή του παίκτη ως θεατή.
	$query = "DELETE FROM `theatis` WHERE `pektis` LIKE " . $slogin;
	$globals->sql_query($query);

	// Διαγράφουμε τυχόν εγγραφές συμμετοχής στο τραπέζι για τον
	// ίδιο παίκτη, ή για την ίδια θέση.
	$query = "DELETE FROM `simetoxi` WHERE (`trapezi` = " .
		$globals->trapezi->kodikos . ") AND ((`pektis` LIKE " .
		$slogin . ") OR (`thesi` = " . $globals->trapezi->thesi . "))";
	$globals->sql_query($query);

	// Δημιουργούμε εγγραφή συμμετοχής του παίκτη στο τραπέζι στη
	// θέση που βρίσκεται αυτή τη στιγμή.
	$query = "INSERT INTO `simetoxi` (`trapezi`, `thesi`, `pektis`) " .
		"VALUES (" . $globals->trapezi->kodikos . ", " .
		$globals->trapezi->thesi . ", " . $slogin . ")";
	$globals->sql_query($query);
	if (@mysqli_affected_rows($globals->db) != 1) {
		@mysqli_rollback($globals->db);
		die('Απέτυχε η εισαγωγή συμμετοχής');
	}

	// Δημιουργούμε εγγραφή θεατή για τον παίκτη και (αρχικά) για
	// τη θέση που συμμετείχε ως θεατής.
	$query = "INSERT INTO `theatis` (`pektis`, `trapezi`, `thesi`) " .
		"VALUES (" . $slogin . ", " . $globals->trapezi->kodikos .
		", " . $globals->trapezi->thesi . ")";
	$globals->sql_query($query);
	if (@mysqli_affected_rows($globals->db) != 1) {
		@mysqli_rollback($globals->db);
		die('Απέτυχε η δημιουργία θεατή');
	}

	// Εκκενώνουμε τη θέση του παίκτη στο τραπέζι.
	$query = "UPDATE `trapezi` SET `pektis" . $globals->trapezi->thesi .
		"` = NULL WHERE `kodikos` = " . $globals->trapezi->kodikos;
	$globals->sql_query($query);
	if (@mysqli_affected_rows($globals->db) != 1) {
		@mysqli_rollback($globals->db);
		die('Απέτυχε η εκκένωση της θέσης σας στο τραπέζι');
	}
}

function apo_theatis_pektis() {
	global $globals;
	global $slogin;

	// Αν δεν υπάχει πρόσκληση στο τραπέζι, τότε δεν μπορεί κάποιος
	// να συμμετάσχει ως παίκτης.
	if (!$globals->trapezi->is_prosklisi()) {
		@mysqli_rollback($globals->db);
		die('Δεν έχετε προσκληθεί στο τραπέζι ' . $globals->trapezi->kodikos);
	}

	// Διαγράφουμε την εγγραφή του παίκτη ως θεατή.
	$query = "DELETE FROM `theatis` WHERE `pektis` LIKE " . $slogin;
	$globals->sql_query($query);
	if (mysqli_affected_rows($globals->db) != 1) {
		@mysqli_rollback($globals->db);
		die('Απέτυχε η διαγραφή θεατή');
	}

	// Ελέγχουμε μήπως ο παίκτης συμμετέχει ήδη στο τραπέζι.
	for ($i = 1; $i <= 3; $i++) {
		$pektis = "pektis" . $i;
		if ($globals->trapezi->$pektis == $globals->pektis->login) {
			return;
		}
	}

	// Ελέγχουμε αν υπάρχει εγγραφή συμμετοχής του παίκτη στο τραπέζι
	// και αν υπάρχει προσπαθούμε να τοποθετήσουμε τον παίκτη στην
	// ίδια θέση, διαγράφοντας παράλληλα αυτήν (και ίσως και άλλες)
	// εγγραφές συμμετοχής του παίκτη στο τραπέζι.
	$query = "SELECT `thesi` FROM `simetoxi` WHERE `trapezi` = " .
		$globals->trapezi->kodikos . " AND `pektis` = " . $slogin;
	$result = $globals->sql_query($query);
	$row = @mysqli_fetch_array($result, MYSQLI_NUM);
	if ($row) {
		@mysqli_free_result($result);
		$thesi = $row[0];
		$query = "DELETE FROM `simetoxi` WHERE `trapezi` = " .
			$globals->trapezi->kodikos . " AND `pektis` = " . $slogin;
		$globals->sql_query($query);
	}
	else {
		// Δεν βρέθηκε εγγραφή συμμετοχής του παίκτη στο τραπέζι,
		// επομένως θα επιχειρήσουμε να τον τοποθετήσουμε στη θέση
		// στην οποία παρακολουθούσε ως θεατής.
		$thesi = $globals->trapezi->thesi;
	}

	// Σουλουπώνουμε τη θέση που εκτιμούμε ότι πρέπει να ενταχθεί
	// ο παίκτης στο τραπέζι.
	if (isset($thesi)) {
		$thesi = intval($thesi);
		if (($thesi < 1) || ($thesi > 3)) {
			$thesi = 1;
		}
	}
	else {
		$thesi = 1;
	}

	// Τώρα θα επιχειρήσουμε να εντάξουμε τον παίκτη στο τραπέζι
	// δοκιμάζοντας και τις τρεις θέσεις και ξεκινώντας από τη θέση
	// που έχουμε ήδη επιλέξει.
	$nok = TRUE;
	for ($i = 0; $i < 3; $i++) {
		$query = "UPDATE `trapezi` SET `pektis" . $thesi . "` = " .
			$slogin . " WHERE `kodikos` = " . $globals->trapezi->kodikos .
			" AND `pektis" . $thesi . "` IS NULL";
		$globals->sql_query($query);
		if (@mysqli_affected_rows($globals->db) == 1) {
			$nok = FALSE;
			break;
		}

		$thesi++;
		if ($thesi > 3) { $thesi = 1; }
	}

	if ($nok) {
		@mysqli_rollback($globals->db);
		die('Δεν υπάρχει κενή θέση στο τραπέζι');
	}
}
?>
