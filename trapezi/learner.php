<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../trapezi/trapezi.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();

Prefadoros::pektis_check();
Prefadoros::trapezi_check();
if ($globals->trapezi->theatis) {
	$globals->klise_fige('Δεν μπορείτε να αλλάξετε αγωνιστικό/εκπαιδευτικό ως θεατής');
}

$learner = ($globals->trapezi->learner == 1 ? "NO" : "YES");

Prefadoros::klidose_trapezi();

switch ($globals->trapezi->thesi) {
case 1:		$ena = 2; $dio = 3; break;
case 2:		$ena = 1; $dio = 3; break;
case 3:		$ena = 1; $dio = 2; break;
default:	$globals->klise_fige('Ακαθόριστη θέση παίκτη');
}

$query = "UPDATE `trapezi` SET `learner` = '" . $learner .
	"', `apodoxi" . $ena . "` = 'NO', `apodoxi" . $dio . "` = 'NO' " .
	"WHERE `kodikos` = " . $globals->trapezi->kodikos;
$globals->sql_query($query);
if (@mysqli_affected_rows($globals->db) != 1) {
	Prefadoros::xeklidose_trapezi(FALSE);
	$globals->klise_fige('Δεν έγινε η αλλαγή αγωνιστικό/εκπαιδευτικό');
}

Prefadoros::set_trapezi_dirty();
Prefadoros::xeklidose_trapezi(TRUE);
$globals->klise_fige();
?>
