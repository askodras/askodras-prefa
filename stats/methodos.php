<?php
require_once '../lib/standard.php';
set_globals(TRUE);
Page::head();
Page::stylesheet("stats/methodos");
Page::body();
Page::epikefalida(Globals::perastike('pedi'));
?>
<div class="main">
<h3 style="text-align: center;">
Τρόπος αξιολόγησης
</h3>
<p class="kimeno">
	Διατρέχονται όλες οι παρτίδες και προσμετρώνται τα καπίκια που
	κέρδισε ή έχασε συνολικά ο κάθε παίκτης. Αυτά τα καπίκια διαιρούνται
	δια του πλήθους των διανομών στις οποίες συμμετείχε ο παίκτης
	και προκύπτει ένας αρχικός «βαθμός» για όσους παίκτες συμμετείχαν
	σε περισσότερες από 1.000 διανομές. Με βάση αυτόν το αρχικό
	βαθμό (κατ' ουσίαν πρόκειται για τον μέσο όρο των καπικιών ανά
	διανομή) διατρέχονται εκ νέου οι παρτίδες και σε όσες διανομές
	και οι τρεις παίκτες είναι βαθμολογημένοι, αναπροσαρμόζονται τα
	καπίκια με βάση τα αναμενόμενα κέρδη ή ζημίες για τον κάθε παίκτη.
	Μετά την προσαρμογή γίνεται επαναβαθμολόγηση με βάση τις αναπροσαρμοσμένες
	τελευταίες 1.000 περίπου διανομές για κάθε παίκτη και αυτή η διαδικασία
	επαναλαμβάνεται αρκετές φορές.
</p>
<p class="kimeno" style="color: #FF3300; font-style: italic;">
	Η μέθοδος αξιολόγησης μελετάται και αναθεωρείται διαρκώς,
	επομένως τα αποτελέσματα που παράγονται είναι μάλλον ανακριβή και
	οπωσδήποτε η βαθμολογία δεν αντανακλά στην πραγματική δυναμικότητα
	των παικτών.
</p>
</div>
<?php
Page::close();
$globals->klise_fige();