<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();

$time = $globals->asfales(Globals::perastike_check('time'));
$scale = $globals->asfales(Globals::perastike_check('scale'));
$tzogos = $globals->asfales(Globals::perastike_check('tzogos'));
Prefadoros::pektis_check();

$query = "UPDATE `pektis` SET `movietime` = '" . $time .
	"', `moviescale` = " . $scale . ", `movietzogos` = '" . $tzogos .
	"' WHERE `login` = " . $globals->pektis->slogin;
$globals->sql_query($query);
if (mysqli_affected_rows($globals->db) != 1) {
	$globals->klise_fige('Απέτυχε η αποθήκευση των ρυθμίσεων');
}
$globals->klise_fige();
?>
