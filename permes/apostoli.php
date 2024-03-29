<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();
Prefadoros::pektis_check();

$login = $globals->asfales($globals->pektis->login);

Globals::perastike_check('pros');
$pros = $globals->asfales($_REQUEST['pros']);

Globals::perastike_check('minima');
$minima = $globals->asfales($_REQUEST['minima']);

$query = "SELECT `login` FROM `pektis` WHERE `login` = '" . $pros . "' LIMIT 1";
$result = $globals->sql_query($query);
while ($row = @mysqli_fetch_array($result, MYSQLI_NUM)) {
	@mysqli_free_result($globals->db);
	break;
}
if (!$row) {
	$globals->klise_fige('Δεν βρέθηκε ο παραλήπτης');
}

$query = "INSERT INTO `minima` (`apostoleas`, `paraliptis`, `minima`) " .
	"VALUES ('" . $login . "', '" . $row[0] . "', '" . $minima . "')";
$result = @mysqli_query($globals->db, $query);
if ((!$result) || (mysqli_affected_rows($globals->db) != 1)) {
	$globals->klise_fige('Απέτυχε η αποστολή του μηνύματος');
}
$globals->klise_fige();
?>
