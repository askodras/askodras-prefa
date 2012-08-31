<?php
require_once '../lib/standard.php';
set_globals(TRUE);
Page::head();
Page::stylesheet('adia/adia');
Page::body();
Page::epikefalida(Globals::perastike('pedi'));
?>
<div class="adiaArea">
<div class="adiaTitlos">
	<span class="data">Δικαιώματα, άδεια και όροι χρήσης</span>
</div>
<p class="adiaKimeno">
	Ο ιστότοπος του διαδικτυακού καφενείου της πρέφας
	στήθηκε με αποκλειστικό σκοπό τη διάδοση και την καλλιέργεια
	του παιχνιδιού της πρέφας, όπως αυτό παίζεται
	στην Ελλάδα και στην Κύπρο. <span class="entono">Ο ιστότοπος δεν έχει
	κερδοσκοπικό χαρακτήρα</span> και, σε κάθε περίπτωση,
	<span class="entono">απαγορεύεται ρητά οποιασδήποτε
	μορφής τζόγος, ή οποιαδήποτε
	άλλη οικονομική συναλλαγή</span> μεταξύ των παικτών, ή άλλων
	παραγόντων του ιστοτόπου (διαχειριστές, προγραμματιστές κλπ).
	Παρέχεται, πάντως, η δυνατότητα συμμετοχής στα έξοδα
	του server υπό τη μορφή δωρεάς, καθώς τα ποσά που
	απαιτούνται δεν είναι ευκαταφρόνητα.
</p>
<p class="adiaKimeno">
	Ο ιστότοπος δίνει τη δυνατότητα ανταλλαγής μηνυμάτων
	μεταξύ των παικτών, είτε με τη μορφή της συζήτησης
	(chat), είτε μέσω του εσωτερικού «ταχυδρομείου» (PM).
	Σε κάθε περίπτωση, <span class="entono">το απόρρητο των συζητήσεων, ή των
	μηνυμάτων αυτών δεν διασφαλίζεται</span> από το πρόγραμμα, επομένως
	δεν μπορούν να αναζητηθούν οποιεσδήποτε ευθύνες από
	τους υπευθύνους του ιστοτόπου (προγραμματιστές, διαχειριστές κλπ)
	για τυχόν δημοσιοποίηση μέρους ή όλων των μηνυμάτων αυτών,
	παρόλο που το πρόγραμμα φροντίζει κατά το δυνατόν για
	την ιδιωτικότητα των μηνυμάτων.
	Η δυνατότητα ανταλλαγής μηνυμάτων παρέχεται από το πρόγραμμα
	με σκοπό την εξυπηρέτηση των αναγκών του παιχνιδιού και
	όχι την κοινωνική δικτύωση των παικτών αυτή καθεαυτή·
	υπάρχουν δεκάδες καλές, ασφαλείς
	και δωρεάν υπηρεσίες στο διαδίκτυο τις οποίες μπορούν
	να χρησιμοποιήσουν οι παίκτες για αυτό το σκοπό.
</p>
<p class="adiaKimeno">
	Η συμπεριφορά των παικτών πρέπει να είναι κόσμια
	και <span class="entono">απαγορεύεται ρητά η διακίνηση προσβλητικού,
	προνογραφικού, ή άλλου υλικού που τυχόν θίγει
	με οποιονδήποτε τρόπο φυσικά, ή νομικά πρόσωπα</span>.
	Οι υπεύθυνοι του ιστοτόπου διατηρούν το δικαίωμα
	του αποκλεισμού παικτών με παραβατική συμπεριφορά,
	καθώς επίσης και το δικαίωμα να «ανεβάζουν» ή να
	«κατεβάζουν» τις υπηρεσίες του ιστοτόπου κατά το
	δοκούν. Δεν μπορεί, πάντως, να ζητηθεί απολύτως καμία
	ευθύνη, ή οποιουδήποτε είδους αποζημίωση από τους
	υπευθύνους του ιστοτόπου για τυχόν παραβατική,
	ή προσβλητική συμπεριφορά των παικτών και δεν υφίσταται
	απολύτως καμία υποχρέωση όσον αφορά στη διαθεσιμότητα,
	ή τη χρήση τόσο των ιστορικών στοιχείων των αρχειοθετημένων
	παρτίδων, όσο και του ιστοτόπου αυτού καθεαυτού.
</p>
<p class="adiaKimeno">
	Το πρόγραμμα του διαδικτυακού καφενείου της πρέφας
	αναπτύχθηκε εξ ολοκλήρου με προγραμματιστικά εργαλεία
	ανοικτού κώδικα. Πιο συγκεκριμένα, η ανάπτυξη
	έγινε σε <a target="_blank" href="http://www.linux.org/">Linux</a>
	(<a target="_blank" href="http://www.centos.org/">CentOS</a>)
	και χρησιμοποιήθηκε η γλώσσα
	<a target="_blank" href="http://www.php.net/">PHP</a>
	για τα προγράμματα που εκτελούνται στον server,
	ενώ για τα προγράμματα στους clients χρησιμοποιήθηκε η γλώσσα
	<a target="_blank" href="http://www.ecmascript.org/">Javascript</a>.
	Στην καρδιά του συστήματος βρίσκεται η βάση δεδομένων
	του διαδικτυακού καφενείου, για το στήσιμο
	και το χειρισμό της οποίας χρησιμοποιήθηκε η
	<a target="_blank" href="http://www.mysql.com/">MySQL</a>.
	Όσον αφορά στο user interface, έγινε περιορισμένη χρήση του
	<a target="_blank" href="http://jquery.com/">jQuery</a>.
</p>
<p class="adiaKimeno">
	Οι εξαιρετικές εικόνες των παιγνιοχάρτων είναι από το site
	<a target="_blank" href="http://www.jfitz.com/cards/">jfitz.com</a>,
	ενώ τα περισσότερα από τα εικονίδια που χρησιμοποιούνται
	στο διαδικτυακό καφενείο της πρέφας κατέβηκαν από τα sites
	<a target="_blank" href="http://www.iconfinder.com/">www.iconfinder.com</a>,
	<a target="_blank" href="http://www.findicons.com/">www.findicons.com</a> και
	<a target="_blank" href="http://www.glitter-graphics.com">www.glitter-graphics.com</a>,
	ενώ σε όσα από αυτά χρειάστηκε, η επεξεργασία τους έγινε
	με τον <a target="_blank" href="http://www.gimp.org/">GIMP</a>.
	Σε ό,τι αφορά στους ήχους, χρησιμοποιήθηκε η βιβλιοθήκη
	<a target="_blank" href="http://www.schillmania.com/">SoundManager2</a>
	του <a target="_blank" href="http://www.linkedin.com/pub/scott-schiller/1/13a/239">Scott Schiller</a>,
	με ήχους που κατέβηκαν από το site <a target="_blank" href="http://www.grsites.com/">GRSites</a>,
	από το οποίο επίσης κατέβηκε και το κομφετί που
	συνθέτει το default background pattern.
	Το όλο project συντηρείται με το
	<a target="_blank" href="http://mercurial.selenic.com/">Mercurial</a>
	SCM, ενώ ως κεντρικό repository χρησιμοποιείται το project
	<a target="_blank" href="http://code.google.com/p/prefadoros/"
		style="font-style: italic;">prefadoros</a>,
	που φιλοξενείται απο το
	<a target="_blank" href="http://code.google.com/hosting/">Google code</a>.
</p>
<p class="adiaKimeno">
	Η άδεια χρήσης, αντιγραφής και τροποποίησης του προγράμματος
	εμπίπτει μάλλον στα πλαίσια της
	<a target="_blank" href="http://www.gnu.org/licenses/agpl.html">AGPL</a>,
	καθώς αυτό επιτάσσουν οι άδειες των εργαλείων που χρησιμοποιήθηκαν.
	Πάντως, το θέμα της άδειας χρήσης, αντιγραφής και τροποποίησης
	του προγράμματος είναι ακόμη ασαφές και μέχρι να ολοκληρωθεί
	η πρώτη σταθερή έκδοση του προγράμματος, απαγορεύεται ρητά η τροποποίηση
	και διασπορά του κώδικα καθ' οιονδήποτε τρόπο. Απαγορεύεται, επίσης,
	η καθ' οιονδήποτε τρόπο εκμετάλλευση του προγράμματος με στόχο
	τον προσπορισμό οικονομικού ή άλλου οφέλους από οποιονδήποτε χωρίς
	την έγγραφη άδεια του ιδιοκτήτη.
</p>
<p class="adiaKimeno">
	Θα ήταν, όμως, άδικο να μην αναφερθώ σε όλους αυτούς που με
	τον έναν ή τον άλλο τρόπο με βοήθησαν στην ανάπτυξη και στη
	βελτίωση του προγράμματος.
	Πράγματι, το διαδικτυακό καφενείο της πρέφας δεν θα είχε υλοποιηθεί
	χωρίς την παρότρυνση και την αμέριστη στήριξη των φίλων και της
	παρέας της ζωντανής πρέφας: Χρήστος Μασούρας, Ιγνάτης Μαυρομάτης,
	Αχιλλέας Πέττας, Αχιλλέας Μένος, Τάσος Βασιλόπουλος, Γιάννης
	Γκατζώλης. Σημαντικό, όμως, ρόλο στην ανάπτυξη του εγχειρήματος
	έπαιξε και ο φίλος μαθηματικός
	και μπριτζέρ, Θοδωρής Ανδριόπουλος, ο οποίος, χωρίς να το γνωρίζει
	ο ίδιος, μου έδωσε την αφορμή να ασχοληθώ εκ νέου με το πρόγραμμα,
	καθώς στην προσπάθειά μου να υλοποιήσω διαδικτυακά τις πρωτότυπες
	ιδέες του πάνω στην διδασκαλία των μαθηματικών, αναγκάστηκα να
	ανατρέξω σε παλαιότερα δικά μου σκαριφήματα που αφορούσαν στο
	παιχνίδι της πρέφας, η κατάληξη των οποίων ήταν το πρόγραμμα του
	διαδικτυακού καφενείου της πρέφας.
</p>
<p class="adiaKimeno">
	Ευχαριστώ, επίσης, τον κομμωτή μου και απόφοιτο
	του τμήματος πληροφορικής του ανοικτού πανεπιστημίου
	της Πάτρας, Παναγιώτη Λάσκαρη, καθώς ο ενθουσιασμός του
	αποτέλεσε για μένα το καλύτερο κίνητρο να ασχοληθώ με
	ένα πρόγραμμα γεμάτο προκλήσεις για μια ακόμη φορά.
	Τέλος, ευχαριστώ θερμά τον πρώην συνάδελφο και διαπρεπή
	μαθηματικό, Γιάννη Ανδρεάδη, για την πολύτιμη βοήθειά του
	σε ό,τι αφορά στην κατοχύρωση του domain name και στη φιλοξενία του
	παιχνιδιού σε δικό του server συντελώντας με τον τρόπο
	αυτό στην αξιοπρεπή συμπεριφορά του προγράμματος.
	Από τα τέλη Αυγούστου 2011 ο «Πρεφαδόρος» φιλοξενείται, πλέον,
	σε δικό του (εικονικό) server, στα έξοδα του οποίου μπορεί
	οποιοσδήποτε να <a href="<?php print $globals->server;
	?>dorea/index.php">συνεισφέρει</a>.
</p>
<p class="adiaKimeno">
	Από τον Φεβρουάριο του 2012, ο ιστότοπος έχει εγκατασταθεί
	σε πραγματικό (όχι εικονικό) server που φιλοξενείται στις
	εγκαταστάσεις της εταιρείας HETZNER, μετά από προτροπή
	του <span class="nobr">-απίστευτου-</span>
	Γιάννη Οικονόμου και της <a target="_blank"
	href="http://www.antithesis.gr">Antithesis&nbsp;Group</a>,
	ο οποίος ανέλαβε και όλη τη διαδικασία του στησίματος
	και της ρύθμισης τόσο του server όσο και του ίδιου
	του ιστοτόπου, με σκοπό την εξυπηρέτηση όσο το δυνατόν
	μεγαλύτερου αριθμού παικτών στο μικρότερο δυνατό κόστος.
	Πρόκειται για άνθρωπο με τεράστια εμπειρία και ευρύτατο
	πεδίο γνώσης στον τομέα του διαδικτύου, και στην επιστήμη
	της πληροφορικής γενικότερα. Οι συμβουλές του Γιάννη
	απογείωσαν, πράγματι, τη συμπεριφορά του server και
	έχει εξοπλίσει το πρόγραμμα με όλα εκείνα τα εργαλεία
	και τις διαδικασίες που βοηθούν στην ορθή διαχείριση
	τόσο των δικτυακών πόρων, όσο και στην εκμετάλλευση
	των δυνατοτήτων του server στο 100%.
</p>
<p class="adiaKimeno" style="text-align: right; font-style: italic;">
	Πάνος Παπαδόπουλος, Θεσσαλονίκη 8 Μαρτίου 2012
</p>
</div>
<?php
Page::close();
$globals->klise_fige();
?>
