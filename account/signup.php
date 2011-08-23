<?php
require_once '../lib/standard.php';
require_once '../pektis/pektis.php';
require_once '../prefadoros/prefadoros.php';
set_globals();
Prefadoros::set_pektis();
Page::head();
Page::stylesheet('lib/forma');
Page::javascript('lib/forma');
Page::javascript('account/account');
Page::body();
Page::epikefalida($globals->is_pektis());
?>
<div class="mainArea">
<form class="forma" method="post" action="<?php print $globals->server; ?>index.php">
<table class="formaData tbldbg">
<tr>
	<td class="formaHeader tbldbg" colspan="2">
		<?php print ($globals->is_pektis() ? 'Update' : 'Create'); ?> account
	</td>
</tr>
<tr>
	<td class="formaPrompt tbldbg">
		Login
	</td>
	<td class="tbldbg">
		<input name="login" id="login" type="text" maxlength="32"
			size="32" class="formaField"
			<?php
			if ($globals->is_pektis()) {
				?>
				value="<?php print $globals->pektis->login; ?>" disabled
				style="font-weight: bold;"
				<?php
			}
			else {
				?>
				value="" onkeyup="account.checkLoginOnline(this);"
				onchange="account.loginAvailable(this);"
				<?php
			}
			?> />
	</td>
</tr>
<tr>
	<td class="formaPrompt tbldbg">
		Name
	</td>
	<td class="tbldbg">
		<input id="onoma" name="onoma" type="text" maxlength="128" size="50" value="<?php
			if ($globals->is_pektis()) {
				print $globals->pektis->onoma;
			}
			?>" class="formaField" />
	</td>
</tr>
<tr>
	<td class="formaPrompt tbldbg">
		Email
	</td>
	<td class="tbldbg">
		<input name="email" type="text" maxlength="64" size="50" value="<?php
			if ($globals->is_pektis()) {
				print $globals->pektis->email;
			}
			?>" onkeydown="this.style.color=globals.color.ok;"
			onblur="account.checkEmailValue(this);" class="formaField" />
	</td>
</tr>
<tr>
	<td class="formaPrompt tbldbg">
		Πλάτη
	</td>
	<td class="tbldbg">
		<select name="plati" class="formaField formaSelect"
			onfocus="formaFyi('Χρώμα πλάτης παιγνιοχάρτων');">
			<?php plati_list(); ?>
		</select>
	</td>
</tr>
<?php
if ($globals->is_pektis()) {
	?>
	<tr>
		<td class="formaPrompt tbldbg">
			Current Password
		</td>
		<td class="tbldbg">
			<input name="password" type="password" maxlength="50"
				size="16" value="@@@@@@@@" class="formaField" />
		</td>
	</tr>
	<?php
}
?>
<tr>
	<td class="formaPrompt tbldbg">
		Password
	</td>
	<td class="tbldbg">
		<input name="password1" type="password" maxlength="50" size="16"
			value="" class="formaField" />
	</td>
</tr>
<tr>
	<td class="formaPrompt tbldbg">
		Repeat
	</td>
	<td class="tbldbg">
		<input name="password2" type="password" maxlength="50" size="16"
			value="" class="formaField" />
	</td>
</tr>
<tr>
	<td colspan="2">
		&#xfeff;<span id="formaFyi" class="fyi formaFyi"></span>
	</td>
</tr>
</table>
<table class="formaPanel tbldbg">
<tr>
	<td class="tbldbg">
		<input type="submit" value="<?php
			print ($globals->is_pektis() ? 'Update' : 'Create') ; ?> account"
			class="button formaButton"
			onclick="return account.<?php print ($globals->is_pektis() ?
				'update' : 'add' ); ?>Pektis(this.form);" />
	</td>
	<td class="tbldbg">
		<input type="reset" value="Reset" class="button formaButton" />
	</td>
	<td class="tbldbg">
		<input type="button" value="Cancel" class="button formaButton"
			onclick="return exitChild();" />
	</td>
</tr>
</table>
</form>
</div>
<?php
Page::close();

function plati_list() {
	global $globals;

	$timi = array("RANDOM", "BLUE", "RED");
	$desc = array();
	$desc["RANDOM"] = "Τυχαία";
	$desc["BLUE"] = "Μπλε";
	$desc["RED"] = "Κόκκινη";

	if ($globals->is_pektis()) {
		$plati = $globals->pektis->plati;
		?>
		<option value="<?php print $plati; ?>" selected="selected"><?php
			print $desc[$plati]; ?></option>
		<?php
	}
	else {
		$plati = "";
	}

	for ($i = 0; $i < 3; $i++) {
		if ($timi[$i] != $plati) {
			?>
			<option value="<?php print $timi[$i]; ?>"><?php
				print $desc[$timi[$i]]; ?></option>
			<?php
		}
	}
}
?>
