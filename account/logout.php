<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/sizitisi.php';
require_once '../prefadoros/prefadoros.php';
Page::data();
set_globals();

Prefadoros::set_pektis();
if (isset($globals->pektis->login)) {
	$query = "UPDATE `pektis` SET `poll` = DATE_SUB(NOW(), INTERVAL " .
		XRONOS_PEKTIS_IDLE_MAX . " SECOND) WHERE `login` LIKE " .
		$globals->pektis->slogin;
	$globals->sql_query($query);
	Sizitisi::cleanup_writing();
}

if (!Globals::perastike('offlineOnly')) {
	unset($_SESSION['ps_login']);
	Prefadoros::klise_sinedria();
}

?>
