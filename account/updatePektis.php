<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();
Prefadoros::pektis_check();

$login = $globals->asfales($globals->pektis->login);

Globals::perastike_check('onoma');
$onoma = $globals->asfales($_REQUEST['onoma']);

Globals::perastike_check('email');
if ($_REQUEST['email'] == '') {
	$email = 'NULL';
}
else {
	Globals::email_check($_REQUEST['email']);
	$email = "'" . $globals->asfales($_REQUEST['email']) . "'";
}

Globals::perastike_check('plati');
$plati = "'" . $globals->asfales($_REQUEST['plati']) . "'";

Globals::perastike_check('password');
$password = $globals->asfales($_REQUEST['password']);

$query = "UPDATE `pektis` SET `onoma` = '" . $onoma .
	"', `email` = " . $email . ", `plati` = " . $plati;
if (Globals::perastike('password1') && ($_REQUEST['password1'])) {
	$query .= ", `password` = '" . sha1($globals->asfales($_REQUEST['password1'])) . "'";
}
$query .= " WHERE `login` LIKE '" . $login . "' AND `password` LIKE '" . sha1($password) . "'";

$result = $globals->sql_query($query);
if (mysqli_affected_rows($globals->db) != 1) {
	print 'Δεν έγιναν αλλαγές στα στοιχεία του λογαριασμού';
	die(1);
}
?>
