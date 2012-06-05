<?php
require_once '../lib/standard.php';
require_once '../prefadoros/prefadoros.php';
require_once '../pektis/pektis.php';
set_globals();
Prefadoros::set_pektis();
Page::head();
Page::stylesheet("dorea/dorea");
Page::body();
Page::epikefalida(Globals::perastike('pedi'));
?>
<div class="mainArea" style="height: 13.8cm; min-height: 13.8 cm; max-height: 13.8cm;">
<p class="dexia">
Θεσσαλονίκη, 5 Ιουνίου 2012
</p>
<p>
<img src="<?php print $globals->server; ?>dorea/panos.jpg" alt=""
style="width: 2.2cm; float: left; margin-right: 0.4cm; margin-top: 0.2cm;
	border-style: solid; border-width: 2px; border-color: #003300;" />
Ο ιστότοπος της διαδικτυακής πρέφας στήθηκε και
συντηρείται από εμένα προσωπικά, τόσο όσον αφορά
στην ανάπτυξη των σχετικών προγραμμάτων,
όσο και σε ό,τι αφορά στην κατοχύρωση των σχετικών
domain names (www.prefadoros.gr, www.prefadoros.net, www.prefadoros.info,
www.prefadoros.com, www.prefadoros.org) και στη φιλοξενία του ιστοτόπου
σε ασφαλή και αξιόπιστο server.
Επειδή τα απαιτούμενα ποσά δεν είναι ευκαταφρόνητα, ζητώ
τη συνδρομή των φίλων της ΔΠ, ώστε να μειώσω το σχετικό κόστος.
</p>
<p>
Για να συνεισφέρετε στα έξοδα του Πρεφαδόρου,
έχω προσθέσει σχετικό κουμπάκι κάτω αριστερά
<span class="nobr">(<img src="<?php print $globals->server; ?>images/external/donate.gif" alt=""
	style="height: 0.45cm; margin-bottom: -0.1cm;" />)</span>
με το οποίο μπορείτε να καταθέσετε οποιοδήποτε ποσό στο λογαριασμό
μου στο <a target="_blank" href="http://www.paypal.com">PayPal</a>.
Το PayPal θεωρείται παγκοσμίως από τους πλέον ασφαλείς τρόπους πληρωμής
όσον αφορά στην ιδιωτικότητα των στοιχείων σας.
Η αμοιβή της PayPal είναι <span class="nobr">0.35€ + 3%</span> για κάθε πληρωμή,
επομένως αν καταθέσετε 10€,
θα «χαθούν» περίπου 0.65€, ενώ αν καταθέσετε 20€, θα «χαθούν» περίπου
0.95€. That's life…
</p>
<p>
Οι διαφημίσεις που εμφανίζονται μέσω του Google
<a target="_blank" href="http://en.wikipedia.org/wiki/AdSense">
AdSense</a>
στο επάνω μέρος της κεντρικής σελίδας είναι ασφαλείς και μπορούν
να αποφέρουν συμπληρωματικά έσοδα στον «Πρεφαδόρο».
Τέλος, ευχαριστώ όσους έχουν ήδη ανταποκριθεί σε τούτο το κάλεσμα
και εύχομαι σε όλους καλή διασκέδαση και καλές σολαρίες!
</p>
<p class="dexia">
Με τιμή, Πάνος Παπαδόπουλος
</p>
</div>
<?php
Page::close();
?>
