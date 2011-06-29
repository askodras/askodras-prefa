<?php
require_once 'lib/standard.php';
set_globals();
Page::init();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Διαδικτυακή πρέφα" />
<meta name="keywords" content="<?php print 'πρέφα, χαρτοπαίγνιο, ' .
	'πρεφαδόρος, ατού, αγορά, μπάζα, μπάζες, σόλο, σολαρία, ' .
	'κάσα, καπίκι, καπίκια'; ?>" />
<title>Πρεφαδόρος</title>
<link rel="stylesheet" type="text/css" href="lib/standard.css" />
<script type="text/javascript">
	//<![CDATA[
	globals = {};
	globals.server = '<?php print $globals->server; ?>';
	globals.timeDif = <?php print time(); ?>;
	//]]>
</script>
<script type="text/javascript" src="lib/standard.js"></script>
</head>

<body>
<div class="testBoxArea">
	<span onclick="testAjax(1)" class="testBox" >Test1</span>
</div>
<div class="testBoxArea">
	<span onclick="testAjax(2)" class="testBox" >Test2</span>
</div>
Ok!
<div id="info">
</div>
</body>
</html>
