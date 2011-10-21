<?php
require_once '../lib/standard.php';
set_globals(TRUE);
Page::head();
Page::stylesheet('faq/faq');
Page::javascript('faq/faq');
Page::body();
Page::epikefalida(Globals::perastike('pedi'));
?>
<div class="faqArea">

<ul>
<li>
<?php erotisi('Στο «κιτάπι» εμφανίζονται αρνητικές κάσες. Πώς γίνεται αυτό;', 'kitapiMion'); ?>
<div id="kitapiMion" style="display: none;">
	<p class="faqKimeno">
		Πολύ σωστή παρατήρηση. Πράγματι, το κιτάπι εμφανίζει αρνητικές κάσες όταν
		κάποιος παίκτης με τις αγορές του έχει «σηκώσει» από την κάσα ποσό που
		υπερβαίνει τα καπίκια που είχε καταθέσει αρχικά. Κανονικά, σ' αυτήν την
		περίπτωση αρχίζει το «γλείψιμο» της κάσας των άλλων δύο παικτών,
		αλλά στη διαδικτυακή πρέφα αυτό το γεγονός εμφανίζεται ως αρνητική κάσα.
		Μην σας ανησυχεί, δεν υπάρχει απολύτως καμία διαφορά στα καπίκια.
	</p>

	<p class="faqKimeno">
		Εξάλλου το συνολικό υπόλοιπο της κάσας εμφανίζεται πάντα στο επάνω μέρος
		του τραπεζιού και μπορείτε να τελειώσετε το παιχνίδι μόλις το υπόλοιπο αυτό
		μηδενιστεί ή γίνει αρνητικό. Ακόμη, όμως, και με αρνητικό συνολικό υπόλοιπο
		κάσας το παιχνίδι μπορεί να συνεχιστεί κανονικά. Δεν υπάρχει απολύτως καμία
		ανάγκη να μπουν οι παίκτες στη διαδικασία ενίσχυσης της κάσας με επιπλέον
		καπίκια. Απλώς συνεχίστε να παίζετε. Τα συνολικά κέρδη ή οι ζημίες των
		παικτών υπολογίζονται κανονικά και στην περιοχή του κάθε παίκτη εμφανίζονται
		τα καπίκια που κερδίζει ή χάνει ο παίκτης ανά πάσα στιγμή.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Ενώ έχω παίξει οι υπόλοιποι παίκτες δεν απαντούν. Τι πρέπει να κάνω;', 'pasxalitsa'); ?>
<div id="pasxalitsa" style="display: none;">
	<p class="faqKimeno">
		Εφόσον έχετε παίξει το φύλλο σας κανονικά και οι συμπαίκτες
		σας δεν απαντούν, είναι πολύ πιθανό να υπάρχει κάποιο πρόβλημα
		στην επικοινωνία σας με τον server. Δοκιμάστε να κάνετε κλικ στην
		πασχαλίτσα στο control panel. Αυτό μπορεί να επαναφέρει
		το φύλλο σας στα χαρτιά σας, πράγμα που σημαίνει ότι η κίνησή σας
		ήταν όλη την ώρα στην αναμονή και δεν παρελήφθη ποτέ από τον server.
		Αν, παρόλα αυτά, τα προβλήματα επιμένουν, δοκιμάστε να κάνετε
		επαναφόρτωση κάνοντας κλικ στα αντικρυστα, στρογγυλά βελάκια
		που βρίσκονται ακριβώς πάνω από την πασχαλίτσα.
	</p>

	<p class="faqKimeno">
		Αν πάλι δεν λυθούν τα προβλήματα, ακυρώστε μια, ή περισσότερες
		κινήσεις μέχρι το παιχνίδι να έρθει σε προηγούμενη ορθή κατάσταση
		και επαναλάβετε τις κινήσεις. Αν τα προβλήματα επιμείνουν,
		κάντε επαναφόρτωση της σελίδας από το σχετικό κουμπάκι
		του browser που χρησιμοποιείτε και εφόσον δεν λυθούν τα προβλήματα
		ξεκινήστε νέα παρτίδα.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Ποιος μοίρασε; Ποιος μιλάει; Ποιος παίζει πρώτος;', 'pmpmpp'); ?>
<div id="pmpmpp" style="display: none;">
	<p class="faqKimeno">
		Ο παίκτης που μοίρασε φύλλα (dealer) σημαίνεται με μια
		πολύχρωμη βεντάλια 5 φύλλων στο αριστερό μέρος της περιοχής του.
		Ο παίκτης που θα παίξει πρώτος (δηλαδή ο παίκτης μετά τον dealer)
		σημαίνεται με ένα σομόν αστέρι. Το αστέρι αυτό φαίνεται καθ' όλη
		τη διάρκεια της αγοράς και εξαφανίζεται μόλις ξεκινήσει το παιχνίδι.
		Ο παίκτης ο οποίος έχει σειρά να κάνει την επόμενη ενέργεια,
		ήτοι να κάνει κάποια δήλωση, ή να παίξει κάποιο φύλλο, σημαίνεται
		με χρυσαφί περίγραμμα στην περιοχή του. Αν κάποιος παίκτης
		έχει δηλώσει "ΜΑΖΙ" κατά τη φάση των δηλώσεων συμμετοχής
		σημαίνεται με δύο ανθρωπάκια στο αριστερό μέρος της περιοχής του,
		ενώ ο παίκτης που κλήθηκε να συμμετάσχει ενώ είχε δηλώσει πάσο
		εμφανίζεται με ελαφρώς αχνό χρώμα στην περιοχή του. Τέλος,
		οι παίκτες που δήλωσαν πάσο και δεν συμμετέχουν τελικώς
		στο παιχνίδι, εμφανίζονται με αχνά χρώματα και ρίγες
		διαγράμμισης στην περιοχή τους.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πώς ξεκινάει η παρτίδα;', 'pxip'); ?>
<div id="pxip" style="display: none;">
	<p class="faqKimeno">
		Εφόσον έχουν συμπληρωθεί 3 παίκτες στο τραπέζι, η παρτίδα
		μπορεί να ξεκινήσει. Για να ξεκινήσει, όμως, η παρτίδα πρέπει
		προηγουμένως και οι 3 παίκτες να αποδεχθούν το ύψος της κάσας
		και τη διάταξη των παικτών. Το ύψος της κάσας μπορεί να είναι
		3Χ300=900 ή 3Χ500=1500 καπίκια και αλλάζει κάνοντας κλικ στο
		σχετικό εικονίδιο του control panel. Η διάταξη των παικτών αφορά
		στη σειρά με την οποία παίζουν οι παίκτες και αλλάζει επίσης
		με σχετικό εικονίδιο του control panel.
	</p>

	<p class="faqKimeno">
		Εφόσον συμφωνείτε με το ύψος της κάσας και τη διάταξη των παικτών,
		μπορείτε να κάνετε κλικ στο εικονίδιο αποδοχής των όρων του παιχνιδιού
		(πράσινο check) οπότε θα δείτε το περίγραμμά σας να γίνεται πράσινο
		(by default είναι κόκκινο). Κάποια στιγμή θα κάνει το ίδιο και
		ο δεύτερος από τους συμπαίκτες σας, ενώ ο τρίτος θα δει να εμφανίζεται
		εικονίδιο Go στη θέση του check. Αυτό σημαίνει ότι κάνοντας αποδοχή
		και ο τρίτος παίκτης μοιράζεται η πρώτη διανομή και εκκινεί το παιχνίδι.
	</p>

	<p class="faqKimeno">
		Πριν κάνετε αποδοχή των όρων του παιχνιδιού μπορείτε να ρυθμίσετε
		ακόμη δύο παραμέτρους του παιχνιδιού. Μπορείτε να καθορίσετε το
		πάσο, πάσο, πάσο και το αν θα μετράνε οι άσοι ή όχι. Το πάσο,
		πάσο, πάσο (ΠΠΠ) καθορίζεται από το παιδικό κουδουνάκι στο
		control panel και σημαίνει ότι οι διανομές που δεν καταλήγουν αγορά
		θα παιχτούν χωρίς ατού και οι παίκτες που κάνουν τις περισσότερες
		μπάζες θα ανεβάσουν κάσα όσο η διαφορά τους με τον παίκτη
		που έκανε τις λιγότερες μπάζες. Το να μην μετράνε οι άσοι
		σημαίνει ότι ο παίκτης που έχει τέσσερις άσους, ούτε
		θα τους δηλώσει, ούτε θα πάρει κάποια έξτρα αμοιβή.
	</p>

	<p class="faqKimeno">
		Ένας άλλος τρόπος να εκκινήσετε την παρτίδα είναι η επανεκκίνηση.
		Εφόσον παίζετε ήδη κάποια παρτίδα με τους φίλους σας, μπορείτε
		να κάνετε κλικ στο εικονίδιο επανεκκίνησης παρτίδας. Αυτό θα προκαλέσει
		διαγραφή όλων των διανομών που έχετε παίξει στο συγκεκριμένο τραπέζι
		και μηδενισμό των καπικιών. Κατόπιν μπορείτε να εκκινήσετε νέα παρτίδα
		ακριβώς με τον τρόπο που περιγράψαμε παραπάνω. Απλώς κάντε κλικ στο
		εικονίδιο αποδοχής και ξανακάνετε κλικ στο εικονίδιο Go.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πώς αλλάζω τα φύλλα του τζόγου;', 'patftt'); ?>
<div id="patftt" style="display: none;">
	<p class="faqKimeno">
		Εφόσον κερδίσατε την αγορά, θα πρέπει να εμφανίζονται
		στο κάτω μέρος του τραπεζιού 12 φύλλα αντί για τα 10 φύλλα
		που είχατε πάρει κατά τη διανομή, καθώς προσετέθησαν και
		τα φύλλα του τζόγου. Κάντε κλικ στα φύλλα που θέλετε να
		ξεσκαρτάρετε. Όσο έχετε 2 φύλλα επιλεγμένα, το πρόγραμμα
		σας προτείνει τις αγορές που μπορείτε να κάνετε, αλλιώς
		δεν προτείνεται αγορά μέχρι να επιλέξετε
		ακριβώς 2 φύλλα. Επιλέγοντας την αγορά σας, τα φύλλα
		που έχετε επιλέξει θα απαλειφθούν και θα μείνετε πάλι
		με 10 φύλλα, περιμένοντας τις δηλώσεις συμμετοχής από τους αμυνόμενους.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πού φαίνεται η κάσα του κάθε παίκτη;', 'pfiktkp'); ?>
<div id="pfiktkp" style="display: none;">
	<p class="faqKimeno">
		Στη ΔΠ η κάσα είναι κοινή (όπως είναι στην πραγματικότητα)
		και αρχικά περιέχει τα καπίκια
		που καταθέτουν οι παίκτες πριν ξεκινήσουν να παίζουν.
		Τα καπίκια που κατατίθενται αρχικά στην κάσα μπορούν
		να είναι 3Χ300=900 ή 3Χ500=1500 καπίκια, ανάλογα με
		το πόσο επιθυμούμε να διαρκέσει το παιχνίδι.
		Από τη στιγμή που θα ξεκινήσει το παιχνίδι, οι μπάζες
		πληρώνονται από την κάσα εφόσον οι αγορές δεν έχουν
		μπει μέσα. Αν ο τζογαδόρος βάλει μέσα την αγορά,
		τότε καταθέτει το αντίτιμο δέκα μπαζών στην κάσα
		και πληρώνει επιπλέον τις μπάζες των αμυνομένων,
		ενώ αν οι αμυνόμενοι μπούνε μέσα, πληρώνουν τις μπάζες
		στον τζογαδόρο. Το υπόλοιπο της (συνολικής) κάσας
		εμφανίζεται ανά πάσα στιγμή στο επάνω μέρος του τραπεζιού
		διαιρεμένο δια 10. Αν, π.χ. το υπόλοιπο της κάσας είναι 98,
		αυτό σημαίνει ότι υπάρχουν ακόμη 980 καπίκια στην κάσα.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πότε τελειώνει το παιχνίδι;', 'pttp'); ?>
<div id="pttp" style="display: none;">
	<p class="faqKimeno">
		Κανονικά το παιχνίδι της πρέφας τελειώνει όταν τελειώσει η κάσα.
		Στη ΔΠ, όμως, δεν ισχύει αυτό. Το παιχνίδι μπορεί να συνεχιστεί
		ακόμη και αν το υπόλοιπο της κάσας γίνει αρνητικό. Τα καπίκια
		που κερδίζει ή χάνει ο κάθε παίκτες εμφανίζονται ανά πάσα στιγμή
		στην περιοχή του κάθε παίκτη, επομένως οι παίκτες μπορούν να
		τελειώσουν το παιχνίδι όποτε το επιθυμούν, απλώς αποχωρώντας
		από το τραπέζι. Αν υπάρχει χρόνος και διάθεση να συνεχιστεί
		το παιχνίδι, δεν υπάρχει απολύτως κανένας λόγος να προσθέσουν
		καπίκια στην κάσα· απλώς συνεχίζουν να παίζουν.
		Αν, πάλι, σας ξενίζει το αρνητικό υπόλοιπο κάσας, μπορείτε
		ανά πάσα στγμή να προσθέσετε καπίκια στην κάσα κάνοντας
		κλικ στο πράσινο βέλος που εμφανίζεται δίπλα στην
		αρχική κάσα στο επάνω μέρος της τσόχας.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πώς μπορώ να παίξω σε κάποιο τραπέζι;', 'pbnpskt'); ?>
<div id="pbnpskt" style="display: none;">
	<p class="faqKimeno">
		Για να παίξετε σε κάποιο τραπέζι υπάρχουν ακριβώς δύο τρόποι:
	</p>
	<ul>
		<li>
			Δημιουργήστε ένα νέο τραπέζι
		</li>
		<li>
			Αποδεχτείτε πρόσκληση που σας έχει στείλει παίκτης από κάποιο τραπέζι
		</li>
	</ul>
	<p class="faqKimeno faqTitlos">
		Δημιουργία νέου τραπεζιού
	</p>
	<p class="faqKimeno">
		Εφόσον δεν συμμετέχετε ήδη σε κάποιο τραπέζι θα πρέπει
		να βλέπετε στο τραπέζι μια τράπουλα και στο επάνω μέρος
		του control panel πρέπει να υπάρχει εικονίδιο που επιγράφεται
		"Μια πρέφα παρακαλώ!" στο οποίο μπορείτε να κάνετε κλικ για
		να δημιουργήσετε ένα νέο τραπέζι. Αν βρίσκεστε ήδη σε κάποιο
		τραπέζι, θα πρέπει να αποχωρήσετε προκειμένου να δημιουργήσετε νέο.
		Για να αποχωρήσετε από το τραπέζι κάντε κλικ στο εικονίδιο
		αποχώρησης που παριστάνεται από ένα κεραμιδί τετράγωνο κουμπί
		με μαύρο περίγραμμα. Εφόσον δημιουργήσετε κάποιο νέο τραπέζι,
		θα πρέπει να προσκαλέσετε τους φίλους σας στο καινούριο τραπέζι,
		αλλιώς δεν θα μπορεί κανείς άλλος να μπει σ' αυτό το τραπέζι.
	</p>
	<p class="faqKimeno faqTitlos">
		Αποδοχή πρόκλησης
	</p>
	<p class="faqKimeno">
		Οι προσκλήσεις που απευθύνονται στο πρόσωπό σας,
		όπως και αυτές που έχετε απευθύνει εσείς προς άλλους
		παίκτες της ΔΠ, εμφανίζονται στο χώρο των προσκλήσεων
		που βρίσκεται δεξιά από το τραπέζι στο επάνω μέρος.
		Οι προσκλήσεις που απευθύνονται στο πρόσωπό σας είναι
		της μορφής "από panos για το τραπέζι 12". Για να αποδεχθείτε
		μια πρόσκληση που σας έχουν απευθύνει, κάντε κλικ στο πράσινο βέλος,
		ή απλά κάντε κλικ σε οποιοδήποτε σημείο της πρόσκλησης εκτός
		από το εικονίδιο απόρριψης που παριστάνεται με ένα κόκκινο X.
		Κάντε κλικ στο εικονίδιο απόρριψης μόνο εφόσον δεν επιθυμείτε
		να αποδεχτείτε την πρόσκληση. Αφού μεταφερθείτε στο τραπέζι
		στο οποίο σας έχουν προσκαλέσει, μπορείτε κι εσείς να απευθύνετε
		προσκλήσεις προς άλλους συμπαίκτες.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Δεν βρίσκω κάποιο εργαλείο στο control panel!', 'dvtcp'); ?>
<div id="dvtcp" style="display: none;">
	<p class="faqKimeno">
		Το control panel είναι μια στήλη από εικονίδια που
		εμφανίζεται δεξιά από το τραπέζι. Κάθε εικονίδιο εκκινεί
		κάποια συγκεκριμένη ενέργεια που έχει σχέση με το παιχνίδι
		ή με το τραπέζι γενικότερα. Όταν δεν βλέπετε το εργαλείο
		που επιθυμείτε, μπορείτε να πατήσετε το εικονίδιο με τις
		τέσσερις χρωματιστές μπαλίτσες στην κορυφή του control panel
		και να αλλάξετε σετ εργαλείων. Υπάρχουν 2-3 σετ εργαλείων,
		ενώ το πρόγραμμα προτείνει σε κάθε φάση του παιχνιδιού
		το σετ με τα εργαλεία που μάλλον θα σας χρειαστούν στη συγκεκριμένη φάση.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Προσπαθώ να διαβάσω τη συζήτηση αλλά «σκρολάρει».', 'tndskdb'); ?>
<div id="tndskdb" style="display: none;">
	<p class="faqKimeno">
		Ίσως έχετε προσπαθήσει να διαβάσετε τα μηνύματα που ανταλλάσσουν
		οι παίκτες στο χώρο ανταλλαγής μηνυμάτων και δεν μπορέσατε να
		το κάνετε, καθώς η συζήτηση «σκρολάρει» κάθε φορά που αλλάζει
		κάτι στην οθόνη του φυλλομετρητή σας.
		Απλώς, κάντε κλικ στο χώρο συζήτησης και θα μπορείτε να
		έχετε πλήρη έλεγχο, χρησιμοποιώντας το scroll bar
		του χώρου ανταλλαγής μηνυμάτων. Όσο χρόνο συμβαίνει αυτό,
		στο background του χώρου ανταλλαγής μηνυμάτων εμφανίζονται
		κάποια τουβλάκια, ώστε να μην ξεχαστείτε και χάσετε τυχόν
		νέα μηνύματα που ανταλλάσσουν οι παίκτες.
		Κάνοντας ξανά κλικ στο χώρο ανταλλαγής μηνυμάτων,
		η συζήτηση σκρολάρει και πάλι.
	</p>
	<?php korifi(); ?>
</div>
</li>

<li>
<?php erotisi('Πώς μπορώ να στείλω προσωπικό μήνυμα;', 'pbnspm'); ?>
<div id="pbnspm" style="display: none;">
	<p class="faqKimeno">
		Οι παίκτες μπορούν να επικοινωνούν μεταξύ τους είτε
		απευθείας, συζητώντας στο τραπέζι ή στο καφενείο,
		είτε ανταλλάσσοντας προσωπικά μηνύματα.
		Για να στείλουμε προσωπικό μήνυμα σε κάποιον
		παίκτη, κάνουμε κλικ στο tab [PM] του toolbar
		που βρίσκεται στο επάνω μέρος της οθόνης του φυλλομετρητή μας
		και εμφανίζεται νέα καρτέλα (παράθυρο) διαχείρισης
		προσωπικών μηνυμάτων.
		Κάνουμε κλικ στο κουμπί [Σύνθεση] και εμφανίζεται φόρμα
		με πεδίο στο οποίο συμπληρώνουμε το login
		name του παίκτη στον οποίον θέλουμε να στείλουμε προσωπικό
		μήνυμα, και χώρος στο οποίον γράφουμε το κείμενο του μηνύματος.
		Κάνουμε κλικ στο κουμπί [Αποστολή] και το μήνυμα έχει
		αποσταλεί.
	</p>
	<p class="faqKimeno">
		Ο παραλήπτης του μηνύματος θα ειδοποιηθεί για το μήνυμα
		μέσω περίληψης του μηνύματος που θα ρολάρει στο κάτω μέρος
		της οθόνης του φυλλομετρητή του.
		Αυτό ακούγεται κάπως ενοχλητικό και γιαυτό το λόγο
		το ρολάρισμα σταματά κάνοντας κλικ πάνω στην ταινία προβολής
		περίληψης μηνυμάτων.
		Κατόπιν κάνουμε κλικ στο tab [PM] όπου μπορούμε να διαβάσουμε
		το πλήρες κείμενο του μηνύματος (ή των μηνυμάτων).
		Μπορούμε να χαρακτηρίσουμε τα μηνύματα ως διαβασμένα,
		ή απλώς να τα διαγράψουμε.
		Κάνοντας κλικ στο κουτάκι [Εξερχόμενα] μπορούμε να διαχειριστούμε
		τα μηνύματα που εμείς έχουμε αποστείλει, ακόμη και να
		διαγράψουμε αυτά τα μηνύματα. Επομένως, δεν είναι απολύτως
		ασφαλές να κρατούμε τα προσωπικά μας μηνύματα, καθώς
		ο αποστολέας μπορεί ανά πάσα στιγμή να τα διαγράψει.
	</p>
	<p class="faqKimeno">
		Μπορούμε, επίσης, να απαντήσουμε σε κάποιο εισερχόμενο μήνυμα
		κάνοντας κλικ στο όνομα του αποστολέα που εμφανίζεται στο
		αριστερό μέρος του μηνύματος.
		Για να στείλουμε προσωπικό μήνυμα σε παίκτες που βρίσκονται
		online, μπορούμε απλώς να τους κάνουμε κλικ στο χώρο του
		καφενείου. Αυτός είναι ένας γρήγορος τρόπος αποστολής
		προσωπικών μηνυμάτων, αλλά παρέχεται και η δυνατότητα
		πρόσκλησης στο τραπέζι. Μπορούμε, τέλος, να στείλουμε
		προσωπικό μήνυμα εντοπίζοντας τον παραλήπτη γράφοντας
		το όνομά του στο πεδίο "Ψάξτε για φίλους".
		Πράγματι, μόλις εντοπιστεί ο παραλήπτης μπορούμε
		να βάλουμε το ποντίκι μας στη βούλα κατάστασης,
		αριστερά από το όνομα του παραλήπτη, και θα
		εμφανιστεί panel διαχείρισης σχέσεων. Εκεί
		υπάρχει και sticky note το οποίο μας οδηγεί
		και πάλι στη φόρμα αποστολής μηνυμάτων.
	</p>
	<?php korifi(); ?>
</div>
</li>

</ul>
</div>
<?php
Page::close();

function erotisi($topic, $id) {
	$idx = preg_replace('/[, ]/', '_', $topic);
	?>
	<a href="#" class="faqErotisi"
		onmouseover="this.style.fontWeight='bold';"
		onmouseout="this.style.fontWeight='normal';"
		onclick="return faq.anixeKlise('<?php print $id; ?>');">
		<?php print $topic; ?>
	</a>
	<?php
}

function korifi() {
	?>
	<div class="faqKorifi">
		[&nbsp;<a href="#" onclick="return faq.klise(this);">Κλείσιμο</a>&nbsp;]
		[&nbsp;<a href="#">Αρχή σελίδας</a>&nbsp;]
	</div>
	<?php
}

?>
