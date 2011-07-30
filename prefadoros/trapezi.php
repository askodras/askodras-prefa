<?php
function process_trapezi() {
	global $globals;
	global $sinedria;

	$energos = Prefadoros::energos_pektis();
	$trapezi = array();

	$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";
	$query = "SELECT * FROM `τραπέζι` WHERE (`τέλος` IS NULL) " .
		"ORDER BY `κωδικός` DESC"; 
	$result = $globals->sql_query($query);
	while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$t = new Trapezi(FALSE);
		$t->set_from_dbrow($row);
		$t->set_energos_pektis($energos);
		$trapezi[] = $t;
	}

	return($trapezi);
}
?>
