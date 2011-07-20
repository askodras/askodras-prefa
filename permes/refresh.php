<?php
require_once '../lib/standard.php';
require_once '../prefadoros/prefadoros.php';
require_once '../pektis/pektis.php';
Page::data();
set_globals();

Prefadoros::pektis_check();
$slogin = "'" . $globals->asfales($globals->pektis->login) . "'";

$query = "SELECT * FROM `μήνυμα` WHERE ";
if (Globals::perastike('exer') && Globals::perastike('iser')) {
	$query .= "(`αποστολέας` LIKE " . $slogin . ") OR " .
		"(`παραλήπτης` LIKE " . $slogin . ")";
}
elseif (Globals::perastike('exer')) {
	$query .= "(`αποστολέας` LIKE " . $slogin . ")";
}
elseif (Globals::perastike('iser')) {
	$query .= "(`παραλήπτης` LIKE " . $slogin . ")";
}
else {
	die(0);
}
$query .= " ORDER BY `κωδικός` LIMIT 30";
$result = $globals->sql_query($query);
?>
<table width="100%" cellspacing="10 0 0 0">
<?php
for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
	$apostoleas = $row['αποστολέας'] == $globals->pektis->login ? '' : $row['αποστολέας'];
	$paraliptis = $row['παραλήπτης'] == $globals->pektis->login ? '' : $row['παραλήπτης'];
	if (($apostoleas == '') && ($paraliptis == '')) {
		$paraliptis = $row['παραλήπτης'];
	}
	?>
	<tr id="item<?php print $row['κωδικός']; ?>" <?php
		if ($row['κατάσταση'] == 'ΔΙΑΒΑΣΜΕΝΟ') {
			print 'class="permesDiavasmeno"';
		}?>>
	<td class="permesApo" <?php
		if ($apostoleas != '') {
			?>
			onclick="Permes.sinthesi('<?php print $apostoleas; ?>');"
			title="Απάντηση" style="cursor: pointer;"
			<?php
		}
		?>>
		<?php print $apostoleas; ?>
	</td>
	<td class="permesPros" <?php
		if ($paraliptis != '') {
			?>
			onclick="Permes.sinthesi('<?php print $paraliptis; ?>');"
			title="Νέο μήνυμα" style="cursor: pointer;"
			<?php
		}
		?>>
		<?php print $paraliptis; ?>
	</td>
	<td class="zebra<?php print $i % 2; ?> permesMinima">
	<?php
	$minima = preg_replace("/\n/", "<br />", $row['μήνυμα']);
	print $minima;
	?>
	</td>
	<td style="vertical-align: top;">
	<img class="permesIcon" title="Διαγραφή" src="<?php
		print $globals->server; ?>images/Xred.png" alt="" />
	</td>
	<td style="vertical-align: top;">
		<?php
		if ($row['κατάσταση'] == 'ΝΕΟ') {
			?>
			<img class="permesIcon" title="Διαβάστηκε" src="<?php
				print $globals->server; ?>images/controlPanel/check.png"
				alt="" />
			<?php
		}
		else {
			?>
			<img class="permesIcon" title="Σημαντικό" src="<?php
				print $globals->server; ?>images/important.png"
				alt="" />
			<?php
		}
		?>
	</td>
	</tr>
	<?php
}
?>
</table>
