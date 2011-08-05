<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../trapezi/trapezi.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();

Prefadoros::pektis_check();
if (!Prefadoros::set_trapezi()) {
	die('Ακαθόριστο τραπέζι');
}
if (!$globals->trapezi->is_pektis()) {
	die('Δεν έχετε δικαίωμα αλλαγής της πρόσβασης στο τραπέζι');
}

$prosvasi = Globals::perastike_check('prosvasi');
$query = "UPDATE `τραπέζι` SET `πρόσβαση` = '" . $globals->asfales($prosvasi) .
	"' WHERE `κωδικός` = " . $globals->trapezi->kodikos;
$globals->sql_query($query);
if (mysqli_affected_rows($globals->db) != 1) {
	die('Δεν άλλαξε η ιδιωτικότητα του τραπεζιού');
}
?>