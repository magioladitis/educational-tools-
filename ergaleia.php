<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>

<html lang="el">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="Εργαλειοθήκη Εκπαιδευτικού: δωρεάν εργαλεία για ΑΣΕΠ, αναπληρωτές, αποσπάσεις και Δημόσια Ωνάσεια Σχολεία." name="description"/>
<title>Εργαλειοθήκη Εκπαιδευτικού</title>
<link href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet"/>
</head>
<body class="edu-ui edu-tools-directory">
<?php require_once __DIR__ . '/includes/components/deadline-card.php'; ?>
<main class="page-shell">
<section class="hero">
<span class="hero-kicker">ΔΩΡΕΑΝ ΕΡΓΑΛΕΙΑ ΓΙΑ ΕΚΠΑΙΔΕΥΤΙΚΟΥΣ</span>
<h1>Εργαλειοθήκη Εκπαιδευτικού</h1>
<p> Συγκεντρωμένα εργαλεία υπολογισμού και ελέγχου για προκηρύξεις ΑΣΕΠ,
αναπληρωτές, αποσπάσεις και ειδικές διαδικασίες, όπως τα Δημόσια Ωνάσεια Σχολεία.
<br/>Σχεδιασμός &amp; υλοποίηση: Μ. Μαγιολαδίτης (ΠΕ03, ΠΕ86)     </p>
<div class="hero-meta">
<span>28 διαθέσιμα εργαλεία</span>
<span>ΑΣΕΠ 1ΓΕ/2026 &amp; 2ΓΕ/2026</span>
<span>Αναπληρωτές</span>
<span>Αποσπάσεις</span>
<span>Ειδική Αγωγή</span>
<span>Ωνάσεια</span>
<span>Ψηφιακό Φροντιστήριο</span><span>Εξωτερικό</span>
<span>ΣΔΕ</span>
</div>
</section>
<?php
renderDeadlineCard(array(
    'title' => '📅 Ενεργές & προσεχείς προθεσμίες',
    'intro' => 'Συγκεντρωτικά σημαντικές προθεσμίες για εκπαιδευτικούς, μαζί με όσες συνδέονται άμεσα με εργαλεία της Εργαλειοθήκης.',
    'collapsible' => true,
    'expanded' => true,
    'items' => array(
        array(
            'title' => 'Νεοδιόριστοι 2026 — Ορκωμοσία & ανάληψη υπηρεσίας',
            'meta_html' => '4.789 εκπαιδευτικοί Α/θμιας και Β/θμιας και 470 μέλη ΕΕΠ–ΕΒΠ · από <strong>24/08/2026</strong> έως και <strong>28/08/2026</strong>.',
            'start' => '2026-08-24T00:00:00+03:00',
            'end_exclusive' => '2026-08-29T00:00:00+03:00',
            'source_url' => 'https://www.minedu.gov.gr/site/70681-21-08-26-enemerose-gia-ten-orkomosia-kai-analepse-yperesias-4-789-ekpaideutikon-a-thmias-kai-b-thmias-ekp-ses',
            'source_label' => 'ΥΠΑΙΘΑ — επίσημη ανακοίνωση ↗',
            'open_text' => 'Η περίοδος ορκωμοσίας και ανάληψης υπηρεσίας είναι ανοικτή.',
            'closed_text' => 'Η περίοδος ορκωμοσίας και ανάληψης υπηρεσίας έχει λήξει.'
        ),
        array(
            'title' => 'Νεοδιόριστοι 2026 — αιτήσεις απόσπασης',
            'meta_html' => 'Για νεοδιοριζόμενους που εμπίπτουν στις προβλεπόμενες κατ’ εξαίρεση περιπτώσεις, καθώς και συζύγους/συμβιούντες νεοδιοριζομένων · ΟΠΣΥΔ από <strong>26/08/2026</strong> έως και <strong>01/09/2026</strong>.',
            'start' => '2026-08-26T00:00:00+03:00',
            'end_exclusive' => '2026-09-02T00:00:00+03:00',
            'source_url' => 'https://www.minedu.gov.gr/site/70699-25-08-26-prosklese-neodiorizomenon-ekpaideutikon-gia-ypobole-aiteseon-apospases',
            'source_label' => 'ΥΠΑΙΘΑ — Πρόσκληση 110726/Ε2/25-08-2026 ↗',
            'open_text' => 'Η περίοδος υποβολής αιτήσεων απόσπασης είναι ανοικτή.',
            'closed_text' => 'Η προθεσμία υποβολής αιτήσεων απόσπασης έχει λήξει.',
            'tool_url' => 'ypologismos-morion-apospasis.php',
            'tool_label' => 'Υπολόγισε μόρια απόσπασης →'
        ),
        array(
            'title' => 'Νεοδιοριζόμενα μέλη ΕΕΠ–ΕΒΠ 2026 — κατ’ εξαίρεση απόσπαση',
            'meta_html' => 'Για νεοδιοριζόμενα μέλη ΕΕΠ–ΕΒΠ που εμπίπτουν στις προβλεπόμενες κατ’ εξαίρεση περιπτώσεις · ΟΠΣΥΔ από <strong>27/08/2026</strong> έως και <strong>01/09/2026</strong>.',
            'start' => '2026-08-27T00:00:00+03:00',
            'end_exclusive' => '2026-09-02T00:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A8%CE%9D%CE%99%CE%9346%CE%9D%CE%9A%CE%A0%CE%94-6%CE%930?inline=true',
            'source_label' => 'ΑΔΑ ΨΝΙΓ46ΝΚΠΔ-6Γ0 · 111181/Ε4/26-08-2026 ↗',
            'open_text' => 'Η περίοδος υποβολής αιτήσεων κατ’ εξαίρεση απόσπασης ΕΕΠ–ΕΒΠ είναι ανοικτή.',
            'closed_text' => 'Η προθεσμία υποβολής αιτήσεων κατ’ εξαίρεση απόσπασης ΕΕΠ–ΕΒΠ έχει λήξει.'
        ),
        array(
            'title' => 'Αναπληρωτές / ωρομίσθιοι 2026–2027',
            'meta_html' => '1ΓΕ/2026, 2ΓΕ/2026, 1ΓΤ/2024, 3ΕΑ/2025, 4ΕΑ/2025 · ΟΠΣΥΔ έως <strong>24/08/2026</strong>.',
            'start' => '2026-08-14T00:00:00+03:00',
            'end_exclusive' => '2026-08-25T00:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/9%CE%96%CE%A5%CE%A146%CE%9D%CE%9A%CE%A0%CE%94-%CE%93%CE%A8%CE%A9?inline=true',
            'source_label' => 'ΑΔΑ 9ΖΥΡ46ΝΚΠΔ-ΓΨΩ ↗',
            'tool_url' => 'ypologismos-morion.php',
            'tool_label' => '1ΓΕ/2ΓΕ →'
        ),
        array(
            'title' => 'ΕΕΠ–ΕΒΠ 2026–2027',
            'meta_html' => '2ΕΑ/2025: ΠΕ21, ΠΕ22, ΠΕ23, ΠΕ25, ΠΕ28, ΠΕ29, ΠΕ30 · 1ΕΑ/2025: ΔΕ01 ΕΒΠ · ΟΠΣΥΔ έως <strong>24/08/2026</strong>.',
            'start' => '2026-08-14T00:00:00+03:00',
            'end_exclusive' => '2026-08-25T00:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A8%CE%970%CE%9C46%CE%9D%CE%9A%CE%A0%CE%94-553?inline=true',
            'source_label' => 'ΑΔΑ ΨΗ0Μ46ΝΚΠΔ-553 ↗',
            'tool_url' => 'ypologismos-morion-2ea-2025.php',
            'tool_label' => '2ΕΑ/2025 →'
        ),
        array(
            'title' => 'ΔΗΜ.Ω.Σ. — γενική πρόσκληση',
            'meta_html' => 'Αιτήσεις αναπληρωτών έως <strong>24/08/2026, 15:00</strong>.',
            'end' => '2026-08-24T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%957%CE%98%CE%9146%CE%9D%CE%9A%CE%A0%CE%94-%CE%A1%CE%9C%CE%98?inline=true',
            'source_label' => 'ΑΔΑ Ε7ΘΑ46ΝΚΠΔ-ΡΜΘ ↗',
            'tool_url' => 'ypologismos-morion-onaseia.php',
            'tool_label' => 'Ωνάσεια →'
        ),
        array(
            'title' => 'ΑΣΠΑΙΤΕ — ΕΠΠΑΙΚ 2026–2027',
            'meta_html' => 'Αιτήσεις συμμετοχής στην κλήρωση έως <strong>25/08/2026, 19:00</strong>.',
            'start' => '2026-06-16T12:00:00+03:00',
            'end' => '2026-08-25T19:00:00+03:00',
            'source_url' => 'https://www.aspete.gr/wp-content/uploads/2026/06/6%CE%A7%CE%9B546%CE%A88%CE%A7%CE%99-3%CE%9E%CE%92-%CE%A0%CE%A1%CE%9F%CE%A3%CE%9A%CE%9B%CE%97%CE%A3%CE%97-%CE%95%CE%A0%CE%A0%CE%91%CE%99%CE%9A-2026-2027.pdf',
            'source_label' => 'ΑΔΑ 6ΧΛ546Ψ8ΧΙ-3ΞΒ ↗',
            'tool_url' => 'paidagogiki-eparkeia.php',
            'tool_label' => 'Παιδαγωγική επάρκεια →'
        ),
        array(
            'title' => 'ΔΗΜ.Ω.Σ. — ΕΑΕ / Τμήματα Ένταξης',
            'meta_html' => '3ΕΑ/2025 · ΠΕ02, ΠΕ03, ΠΕ04 με εξειδίκευση ΕΑΕ έως <strong>31/08/2026, 15:00</strong>.',
            'end' => '2026-08-31T15:00:00+03:00',
            'source_url' => 'https://diavgeia.gov.gr/doc/%CE%A1%CE%A4%CE%91%CE%A746%CE%9D%CE%9A%CE%A0%CE%94-%CE%932%CE%97?inline=true',
            'source_label' => 'ΑΔΑ ΡΤΑΧ46ΝΚΠΔ-Γ2Η ↗',
            'tool_url' => 'ypologismos-morion-onaseia.php',
            'tool_label' => 'Ωνάσεια →'
        ),
        array(
            'title' => 'ΣΑΕΚ Σιβιτανιδείου — ωρομίσθιοι εκπαιδευτές 2026–2027',
            'meta_html' => 'Ηλεκτρονικές αιτήσεις για το φθινοπωρινό εξάμηνο 2026Β και το εαρινό 2027Α · από <strong>24/08/2026, 12:00</strong> έως <strong>04/09/2026, 12:00</strong>.',
            'start' => '2026-08-24T12:00:00+03:00',
            'end' => '2026-09-04T12:00:00+03:00',
            'source_url' => 'https://ek.sivitanidios.edu.gr/download/2026/7903.pdf',
            'source_label' => 'Σιβιτανίδειος — Πρόσκληση 7903/21-08-2026 · ΑΔΑ ΨΞ0Ο469ΒΨ1-3ΥΚ ↗',
            'open_text' => 'Η περίοδος υποβολής αιτήσεων είναι ανοικτή.',
            'closed_text' => 'Η προθεσμία υποβολής αιτήσεων έχει λήξει.',
            'tool_url' => 'ypologismos-morion-sivitanidios-saek.php',
            'tool_label' => 'Υπολόγισε μόρια →'
        )
    ),
    'note_html' => 'Στην ορκωμοσία/ανάληψη νεοδιορίστων, στις προσκλήσεις νεοδιοριζομένων εκπαιδευτικών και μελών ΕΕΠ–ΕΒΠ για αιτήσεις απόσπασης και στις προσκλήσεις αναπληρωτών και ΕΕΠ–ΕΒΠ δεν αναφέρεται συγκεκριμένη ώρα λήξης· το countdown θεωρεί τεχνικά το τέλος της αντίστοιχης καταληκτικής ημέρας. Οι επίσημες ανακοινώσεις, οι προσκλήσεις και οι ηλεκτρονικές πλατφόρμες υπερισχύουν.'
));
?>
<section aria-label="Αναζήτηση και φίλτρα εργαλείων" class="toolbar">
<div class="search-wrap">
<input aria-label="Αναζήτηση εργαλείου" autocomplete="off" id="toolSearch" placeholder="Αναζήτηση εργαλείου π.χ. μόρια, παράβολο, Ωνάσεια..." type="search"/>
</div>
<div aria-label="Κατηγορίες εργαλείων" class="filters" role="group">
<button aria-pressed="true" class="filter-btn active" data-filter="all" type="button">Όλα</button>
<button aria-pressed="false" class="filter-btn" data-filter="asep" type="button">ΑΣΕΠ</button>
<button aria-pressed="false" class="filter-btn" data-filter="eidiki-agogi" type="button">Ειδική Αγωγή</button>
<button aria-pressed="false" class="filter-btn" data-filter="anaplirotes" type="button">Αναπληρωτές</button>
<button aria-pressed="false" class="filter-btn" data-filter="apospaseis" type="button">Αποσπάσεις</button>
<button aria-pressed="false" class="filter-btn" data-filter="metatheseis" type="button">Μεταθέσεις</button>
<button aria-pressed="false" class="filter-btn" data-filter="sde" type="button">ΣΔΕ</button>
<button aria-pressed="false" class="filter-btn" data-filter="saek" type="button">ΣΑΕΚ</button>
<button aria-pressed="false" class="filter-btn" data-filter="onaseia" type="button">Ωνάσεια</button>
<button aria-pressed="false" class="filter-btn" data-filter="ypiresiaka" type="button">Υπηρεσιακά</button>
</div>
<div aria-live="polite" class="results-line" id="resultsLine" role="status">Εμφανίζονται 28 εργαλεία.</div>
</section>
<section class="tools-grid" id="toolsGrid">
<a class="tool-card" data-category="asep" data-search="δικαίωμα συμμετοχής προκήρυξη ΑΣΕΠ προϋποθέσεις υποψήφιος" href="dikaioma-symmetoxis.php">
<div class="card-top">
<span class="tool-number">1</span>
<span class="category-tag">ΑΣΕΠ</span>
</div>
<h2>Έχω δικαίωμα συμμετοχής;</h2>
<p>
          Απάντησε σε απλές ερωτήσεις για έναν ενδεικτικό έλεγχο των γενικών
          προϋποθέσεων συμμετοχής στις προκηρύξεις εκπαιδευτικών.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep" data-search="παράβολα παράβολο κόστος ειδικότητα 1ΓΕ 2ΓΕ ΑΣΕΠ" href="posa-paravola.php">
<div class="card-top">
<span class="tool-number">2</span>
<span class="category-tag">ΑΣΕΠ</span>
</div>
<h2>Πόσα παράβολα χρειάζομαι;</h2>
<p>
          Επίλεξε την ειδικότητα ή τις ειδικότητές σου και δες πόσα παράβολα
          χρειάζεσαι, το συνολικό κόστος και σε ποια προκήρυξη αντιστοιχείς.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep" data-search="δικαιολογητικά τίτλοι σπουδών μεταπτυχιακό διδακτορικό integrated master αλλοδαπή ΔΟΑΤΑΠ" href="dikaiologitika-titlon-spoudon.php">
<div class="card-top">
<span class="tool-number">3</span>
<span class="category-tag">ΑΣΕΠ</span>
</div>
<h2>Τι δικαιολογητικά χρειάζομαι;</h2>
<p>
          Οδηγός για δικαιολογητικά μεταπτυχιακών, διδακτορικών, integrated master
          και τίτλων σπουδών της αλλοδαπής.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes" data-search="υπολογισμός μορίων ΑΣΕΠ αναπληρωτές 1ΓΕ 2ΓΕ ακαδημαϊκά ξένες γλώσσες προϋπηρεσία κοινωνικά κριτήρια" href="ypologismos-morion.php">
<div class="card-top">
<span class="tool-number">4</span>
<span class="category-tag green">ΑΣΕΠ / Αναπληρωτές</span>
</div>
<h2>Υπολογισμός μορίων 1ΓΕ/2026 &amp; 2ΓΕ/2026</h2>
<p>
          Υπολόγισε τα μόρια για τις προκηρύξεις 1ΓΕ/2026 και 2ΓΕ/2026 με βάση
          ακαδημαϊκά προσόντα, ξένες γλώσσες, προϋπηρεσία και κοινωνικά κριτήρια.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes" data-search="1ΓΤ 2024 1GT ΤΕ01 ΤΕ02 ΤΕ16 τεχνική εκπαίδευση μουσικής μη ανώτατων ιδρυμάτων υπολογισμός μόρια ΑΣΕΠ προϋπηρεσία κοινωνικά ακαδημαϊκά" href="ypologismos-morion-1gt-2024.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">5</span>
<span class="category-tag green">ΑΣΕΠ / Τ.Ε.</span>
</div>
<h2>Υπολογισμός μορίων 1ΓΤ/2024</h2>
<p>
          Υπολόγισε τα μόρια για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16 με βάση
          ακαδημαϊκά προσόντα, προϋπηρεσία και κοινωνικά κριτήρια της 1ΓΤ/2024.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep" data-search="παιδαγωγική διδακτική επάρκεια ΠΔΕ πρόταξη ΑΣΕΠ" href="paidagogiki-eparkeia.php">
<div class="card-top">
<span class="tool-number">6</span>
<span class="category-tag">ΑΣΕΠ</span>
</div>
<h2>Έχω Παιδαγωγική και Διδακτική Επάρκεια;</h2>
<p>
          Έλεγξε ενδεικτικά αν διαθέτεις Π.Δ.Ε. και ποιο αποδεικτικό μπορεί να
          χρειάζεται για την πρόταξη στους αξιολογικούς πίνακες.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="anaplirotes onaseia" data-search="Ωνάσεια ΔΗΜΩΣ Δημόσια Ωνάσεια Σχολεία μόρια αναπληρωτή Πρότυπα Πειραματικά ακαδημαϊκά προσόντα" href="ypologismos-morion-onaseia.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">7</span>
<span class="category-tag purple">Ωνάσεια</span>
</div>
<h2>Μόρια Αναπληρωτή στα Ωνάσεια</h2>
<p>
          Υπολόγισε τα μόριά σου για τα Δημόσια Ωνάσεια Σχολεία με βάση τα
          ακαδημαϊκά προσόντα ΑΣΕΠ και την προϋπηρεσία σε Πρότυπα/Πειραματικά.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis onaseia" data-search="ΔΗΜΩΣ Ωνάσεια Δημόσια Ωνάσεια Σχολεία απόσπαση μόνιμων εκπαιδευτικών 53 μόρια επιστημονική παιδαγωγική συγγραφικό καινοτόμο έργο" href="ypologismos-morion-apospasis-dimos.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">8</span>
<span class="category-tag purple">Ωνάσεια / Αποσπάσεις</span>
</div>
<h2>Μόρια Απόσπασης στα ΔΗΜ.Ω.Σ.</h2>
<p>
          Υπολόγισε τα μόρια για απόσπαση μόνιμων εκπαιδευτικών στα Δημόσια
          Ωνάσεια Σχολεία. Μέγιστο σύνολο 53 μόρια στις κατηγορίες Α, Β και Γ.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis" data-search="υπολογισμός μόρια απόσπασης εκπαιδευτικών συνυπηρέτηση εντοπιότητα οικογενειακοί λόγοι υπηρεσία" href="ypologismos-morion-apospasis.php">
<div class="card-top">
<span class="tool-number">9</span>
<span class="category-tag orange">Αποσπάσεις</span>
</div>
<h2>Υπολογισμός μορίων απόσπασης</h2>
<p>
          Υπολόγισε ενδεικτικά τα μόρια απόσπασης εκπαιδευτικών με βάση τα
          αντίστοιχα κριτήρια της διαδικασίας.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes eidiki-agogi" data-search="1ΕΑ 2025 ΕΒΠ ΔΕ01 ειδικό βοηθητικό προσωπικό ειδική αγωγή μόρια ΑΣΕΠ βαθμός ΙΕΚ ΕΠΑΛ προϋπηρεσία κοινωνικά κριτήρια" href="ypologismos-morion-1ea-2025.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">10</span>
<span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
</div>
<h2>Υπολογισμός μορίων 1ΕΑ/2025</h2>
<p>
          Υπολόγισε τα μόρια για τον κλάδο ΔΕ01 Ειδικού Βοηθητικού Προσωπικού,
          με διαφορετικό συντελεστή βαθμού για τίτλους δευτεροβάθμιας και ΙΕΚ/Τάξης Μαθητείας.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes eidiki-agogi" data-search="2ΕΑ 2025 ΕΕΠ ΠΕ21 ΠΕ22 ΠΕ23 ΠΕ25 ΠΕ28 ΠΕ29 ΠΕ30 ΠΕ31 ειδικό εκπαιδευτικό προσωπικό ειδική αγωγή μόρια ΑΣΕΠ πρόταξη σχολική ψυχολογία παιδαγωγική επάρκεια Braille ΕΝΓ" href="ypologismos-morion-2ea-2025.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">11</span>
<span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
</div>
<h2>Υπολογισμός μορίων 2ΕΑ/2025</h2>
<p>
          Υπολόγισε τα μόρια των κλάδων ΕΕΠ και δες ειδικές ενδείξεις πρόταξης
          για Παιδαγωγική Επάρκεια, ΠΕ23 Σχολικής Ψυχολογίας, Braille και ΕΝΓ.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes eidiki-agogi" data-search="3ΕΑ 2025 ειδική αγωγή ΕΑΕ κύριος κύριο πίνακας Β επικουρικός πίνακας μόρια ΑΣΕΠ αναπληρωτές ειδική εκπαίδευση" href="ypologismos-morion-3ea-2025.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">12</span>
<span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
</div>
<h2>Υπολογισμός μορίων 3ΕΑ/2025</h2>
<p>
          Υπολόγισε τα μόριά σου στην Ειδική Αγωγή και έλεγξε ενδεικτικά αν
          εντάσσεσαι στον Κύριο (Πίνακα Β΄) ή στον Επικουρικό Πίνακα.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes eidiki-agogi" data-search="4ΕΑ 2025 ειδική αγωγή ΕΑΕ ΤΕ01 ΤΕ02 ΤΕ16 κύριος κύριο πίνακας Β επικουρικός πίνακας μόρια ΑΣΕΠ αναπληρωτές τεχνική εκπαίδευση" href="ypologismos-morion-4ea-2025.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">13</span>
<span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
</div>
<h2>Υπολογισμός μορίων 4ΕΑ/2025</h2>
<p>
          Υπολόγισε τα μόρια για τους κλάδους ΤΕ01, ΤΕ02 και ΤΕ16 στην Ειδική Αγωγή
          και έλεγξε ενδεικτικά ένταξη στον Κύριο ή στον Επικουρικό Πίνακα.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes eidiki-agogi" data-search="5ΕΑ 2022 ειδική αγωγή ΕΑΕ ΔΕ01 ΔΕ02 ΔΕ01.05 ΔΕ01.13 ΔΕ01.14 ΔΕ01.15 ΔΕ01.17 ΔΕ02.01 ΔΕ02.02 ιστορική προκήρυξη μόρια ΑΣΕΠ 123 αιτήσεις" href="ypologismos-morion-5ea-2022.php">
<div class="card-top">
<span class="tool-number">14</span>
<span class="category-tag green">ΑΣΕΠ / Ειδική Αγωγή</span>
</div>
<h2>Υπολογισμός μορίων 5ΕΑ/2022</h2>
<p>
          Η «μικρή» ιστορική προκήρυξη ΔΕ Ειδικής Αγωγής: 7 ειδικότητες ΔΕ01/ΔΕ02,
          μόλις 123 αιτήσεις, με έλεγχο Κύριου/Επικουρικού Πίνακα και ειδικούς κανόνες ΔΕ.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep" data-search="ένσταση ενστάσεις ΑΣΕΠ 1ΓΕ 2ΓΕ 2026 προσωρινοί πίνακες προσωρινών πινάκων e-παράβολο eparavolo παράβολο 50 ευρώ 20ψήφιο 20 ψηφία κωδικός επανυποβολή ανάκληση προθεσμία countdown δικαιολογητικά" href="odigos-enstasis.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">15</span>
<span class="category-tag">ΑΣΕΠ</span>
</div>
<h2>Οδηγός ένστασης 1ΓΕ/2026 &amp; 2ΓΕ/2026</h2>
<p>
          Προετοίμασε την ένστασή σου με έλεγχο 20ψήφιου e-Παραβόλου, προθεσμία,
          επανυποβολή, δικαιολογητικά και σύνδεση με τον υπολογισμό μορίων.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="asep anaplirotes" data-search="δικαιολογητικά τέκνα αναπηρία κοινωνικά κριτήρια ΑΣΕΠ 1ΓΕ 2ΓΕ μοριοδοτούμενα τέκνα αναπηρία ιδίου συζύγου τέκνου" href="dikaiologitika-tekna-anapiria.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">16</span>
<span class="category-tag green">ΑΣΕΠ / Αναπληρωτές</span>
</div>
<h2>Δικαιολογητικά τέκνων &amp; αναπηρίας</h2>
<p>
          Ενδεικτικός οδηγός για τα κοινωνικά κριτήρια: μοριοδοτούμενα τέκνα,
          αναπηρία ιδίου, τέκνου ή συζύγου και τα σχετικά δικαιολογητικά.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis" data-search="ψηφιακό φροντιστήριο απόσπαση αποσπάσεις μόρια μοριοδότηση μόνιμοι εκπαιδευτικοί βιντεοσκοπημένο μάθημα συνέντευξη πανελλαδικές ΤΠΕ" href="ypologismos-morion-apospasis-psifiako-frontistirio.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">17</span>
<span class="category-tag orange">Αποσπάσεις</span>
</div>
<h2>Μόρια Απόσπασης στο Ψηφιακό Φροντιστήριο</h2>
<p>
          Υπολόγισε τη μοριοδότηση έως 100 μονάδες για απόσπαση στο Ψηφιακό Φροντιστήριο:
          γενική παρουσία, επιστημονική κατάρτιση–εμπειρία και βιντεοσκοπημένο μάθημα.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis sde" data-search="ΣΔΕ Σχολεία Δεύτερης Ευκαιρίας απόσπαση αποσπάσεις μόρια μοριοδότηση μόνιμοι εκπαιδευτικοί γραμματισμοί ειδικότητες εκπαίδευση ενηλίκων" href="ypologismos-morion-apospasis-sde.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">18</span>
<span class="category-tag orange">ΣΔΕ / Αποσπάσεις</span>
</div>
<h2>Μόρια Απόσπασης στα ΣΔΕ</h2>
<p>
          Έλεγξε αν η ειδικότητά σου είναι αποδεκτή και υπολόγισε τη μοριοδότηση έως 40 μόρια
          για απόσπαση στα Σχολεία Δεύτερης Ευκαιρίας.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis" data-search="εξωτερικό απόσπαση αποσπάσεις εκπαιδευτικών μόρια μοριοδότηση γλωσσομάθεια βασικός πίνακας εναλλακτικός πίνακας ΔΙΠΟΔΕ ελληνόγλωσση εκπαίδευση" href="ypologismos-morion-apospasis-exoteriko.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top">
<span class="tool-number">19</span>
<span class="category-tag orange">Αποσπάσεις</span>
</div>
<h2>Μόρια Απόσπασης στο Εξωτερικό</h2>
<p>
          Υπολόγισε τα μόρια τίτλων και γλωσσομάθειας και κάνε βασικό έλεγχο
          δικαιώματος για Βασικό ή Εναλλακτικό Πίνακα απόσπασης στο εξωτερικό.
        </p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="apospaseis" data-search="ευρωπαϊκά σχολεία Ευρωπαϊκά Σχολεία απόσπαση αποσπάσεις μόρια μοριοδότηση συνέντευξη γλωσσομάθεια ΤΠΕ διδασκαλία" href="ypologismos-morion-apospasis-evropaika-scholeia.php"><span class="new-badge">ΝΕΟ</span><div class="card-top"><span class="tool-number">20</span><span class="category-tag orange">Αποσπάσεις</span></div><h2>Μόρια Απόσπασης σε Ευρωπαϊκά Σχολεία</h2><p>Υπολόγισε τα μόρια τυπικών προσόντων και εμπειρίας πριν από τη συνέντευξη και το τελικό σύνολο μετά την προφορική διαδικασία.</p><span class="button-like">Άνοιγμα εργαλείου →</span></a>
<a class="tool-card" data-category="asep" data-search="μετατροπή κλίμακας βαθμός πτυχίου 10βάθμια 20βάθμια 1ΓΕ 2026 1ΓΤ 2024 λεκτική ΚΑΛΩΣ ΛΙΑΝ ΚΑΛΩΣ ΑΡΙΣΤΑ ακέραιο μέρος αριθμητής παρονομαστής κλάσμα δεκαδικός" href="metatropi-klimakas.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">21</span><span class="category-tag">ΑΣΕΠ</span></div>
<h2>Μετατροπή κλίμακας βαθμού</h2>
<p>Μετέτρεψε βαθμό από 10βάθμια σε 20βάθμια κλίμακα ή από λεκτική μορφή και πάρε έτοιμα τα πεδία Ακέραιο μέρος – Αριθμητής – Παρονομαστής.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="sde" data-search="ΣΔΕ Σχολεία Δεύτερης Ευκαιρίας Διευθυντές Υποδιευθυντές διευθυντής υποδιευθυντής θέσεις ευθύνης μόρια μοριοδότηση διοικητική εμπειρία διδακτική εμπειρία εκπαίδευση ενηλίκων συνέντευξη" href="ypologismos-morion-diefthynton-ypodiefthynton-sde.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">22</span><span class="category-tag orange">ΣΔΕ / Θέσεις Ευθύνης</span></div>
<h2>Μόρια Διευθυντών &amp; Υποδιευθυντών ΣΔΕ</h2>
<p>Υπολόγισε τα μόρια για θέσεις Διευθυντή ή Υποδιευθυντή ΣΔΕ, με τυπικά προσόντα, διδακτική και διοικητική εμπειρία, επιμόρφωση και συνέντευξη όπου προβλέπεται.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="sde" data-search="ΣΔΕ Μητρώο ωρομίσθιο εκπαιδευτικό προσωπικό εκπαιδευτές Σύμβουλοι Ψυχολόγοι Σύμβουλοι Σταδιοδρομίας μόρια μοριοδότηση ανεργία κοινωνικά κριτήρια ΕΟΠΠΕΠ" href="ypologismos-morion-mitroo-sde.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">23</span><span class="category-tag orange">ΣΔΕ / Μητρώο</span></div>
<h2>Μόρια Μητρώου ΣΔΕ</h2>
<p>Ενιαίος υπολογιστής για Εκπαιδευτικό Προσωπικό, Συμβούλους Ψυχολόγους και Συμβούλους Σταδιοδρομίας, με δυναμικά κριτήρια και κοινωνικές προσαυξήσεις.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="saek" data-search="ΣΑΕΚ Σιβιτανίδειος Σιβιτανιδείου ωρομίσθιοι εκπαιδευτές 2026 2027 μόρια μοριοδότηση εκπαίδευση ενηλίκων διδακτική εμπειρία κοινωνικά κριτήρια" href="ypologismos-morion-sivitanidios-saek.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">24</span><span class="category-tag green">ΣΑΕΚ / Ωρομίσθιοι</span></div>
<h2>Μόρια Εκπαιδευτή ΣΑΕΚ Σιβιτανιδείου</h2>
<p>Υπολόγισε τη βασική βαθμολογία και τις κοινωνικές προσαυξήσεις για την πρόσκληση ωρομίσθιων εκπαιδευτών 2026–2027.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="ypiresiaka" data-search="υποχρεωτικό διδακτικό ωράριο υποστηρικτικό έργο ΕΕΠ ειδικό εκπαιδευτικό προσωπικό ΕΒΠ ειδικό βοηθητικό προσωπικό ώρες εβδομάδα πρωτοβάθμια νηπιαγωγείο δημοτικό δευτεροβάθμια καθηγητές διευθυντές υποδιευθυντές οργανικότητα έτη μήνες ημέρες υπηρεσίας" href="ypologismos-didaktikou-orariou.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">25</span><span class="category-tag">Υπηρεσιακά</span></div>
<h2>Υπολογισμός υποχρεωτικού ωραρίου</h2>
<p>Υπολόγισε το διδακτικό ωράριο εκπαιδευτικών σε Νηπιαγωγείο, Δημοτικό ή Δευτεροβάθμια και το ωράριο υποστηρικτικού έργου ΕΕΠ/ΕΒΠ.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="ypiresiaka" data-search="μισθολογικό κλιμάκιο ΜΚ μισθολογική εξέλιξη εκπαιδευτικών ΠΕ ΤΕ ΔΕ ΥΕ προϋπηρεσία μεταπτυχιακό διδακτορικό integrated master 4354 5246" href="ypologismos-misthologikou-klimakiou.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">26</span><span class="category-tag">Υπηρεσιακά</span></div>
<h2>Υπολογισμός Μισθολογικού Κλιμακίου (Μ.Κ.)</h2>
<p>Βρες ενδεικτικά το Μ.Κ. από την κατηγορία, τον αναγνωρισμένο μισθολογικό χρόνο και τυχόν αναγνωρισμένη προώθηση λόγω τίτλου σπουδών.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="metatheseis ypiresiaka" data-search="μετάθεση μεταθέσεις μόρια μετάθεσης μονάδες συνθηκών διαβίωσης ΜΣΔ δυσπρόσιτα απομακρυσμένα καταστήματα κράτησης ψηφιακό φροντιστήριο συνυπηρέτηση εντοπιότητα πρώτη προτίμηση" href="ypologismos-morion-metathesis.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">27</span><span class="category-tag orange">Μεταθέσεις</span></div>
<h2>Υπολογισμός μορίων μετάθεσης</h2>
<p>Υπολόγισε τα βασικά μόρια μετάθεσης Δ.Ε. και αναλυτικά τις Μ.Σ.Δ., με δυσπρόσιτα/απομακρυσμένα, καταστήματα κράτησης, Ψηφιακό Φροντιστήριο και ειδικές υπηρετήσεις.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a>
<a class="tool-card" data-category="ypiresiaka eidiki-agogi" data-search="αναθέσεις μαθημάτων Α Β Γ ανάθεση ειδικότητα κλάδος Γυμνάσιο ΓΕΛ Εσπερινό ΕΑΕ ΕΝΕΕΓΥΛ ΕΝ.Ε.Ε.ΓΥ.-Λ." href="anatheseis-mathimaton.php">
<span class="new-badge">ΝΕΟ</span>
<div class="card-top"><span class="tool-number">28</span><span class="category-tag green">Υπηρεσιακά / Αναθέσεις</span></div>
<h2>Αναθέσεις Μαθημάτων ανά Ειδικότητα</h2>
<p>Δες ποια μαθήματα έχει ο κλάδος σου σε Α΄, Β΄ και Γ΄ ανάθεση, με φίλτρα ανά τύπο σχολείου και τις αντίστοιχες επίσημες πηγές.</p>
<span class="button-like">Άνοιγμα εργαλείου →</span>
</a></section>
<div aria-hidden="true" class="no-results" id="noResults">
      Δεν βρέθηκε εργαλείο που να ταιριάζει στην αναζήτησή σου.
    </div>
<section class="info-grid">
<div class="notice">
<strong>Σημαντική σημείωση:</strong>
        Τα εργαλεία παρέχουν ενδεικτική πληροφόρηση και δεν αντικαθιστούν τις
        επίσημες προκηρύξεις, εγκυκλίους και οδηγίες των αρμόδιων φορέων.
        Πριν από την οριστική υποβολή αίτησης, ελέγχετε πάντοτε τα επίσημα έγγραφα
        και τα στοιχεία που εμφανίζονται στο ΑΣΕΠ ή/και στο ΟΠΣΥΔ.
      </div>
<div class="side-box">
<strong>Ειδικά για 1ΓΕ/2026 &amp; 2ΓΕ/2026</strong>
        Τα εργαλεία ΑΣΕΠ παραμένουν συγκεντρωμένα και στην ειδική σελίδα
        <a href="asep-tools.php">Χρήσιμα εργαλεία 1ΓΕ/2026 &amp; 2ΓΕ/2026</a>.
      </div>
</section>
</main>
<script>
    (function () {
      const searchInput = document.getElementById('toolSearch');
      const cards = Array.from(document.querySelectorAll('.tool-card'));
      const filterButtons = Array.from(document.querySelectorAll('.filter-btn'));
      const resultsLine = document.getElementById('resultsLine');
      const noResults = document.getElementById('noResults');

      let activeFilter = 'all';

      function normalizeGreek(text) {
        return (text || '')
          .toLocaleLowerCase('el-GR')
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '')
          .replace(/ς/g, 'σ');
      }

      function updateCards() {
        const query = normalizeGreek(searchInput.value.trim());
        let visible = 0;

        cards.forEach(card => {
          const categories = (card.dataset.category || '').split(/\s+/);
          const haystack = normalizeGreek(
            (card.dataset.search || '') + ' ' + card.textContent
          );

          const matchesFilter = activeFilter === 'all' || categories.includes(activeFilter);
          const matchesSearch = !query || haystack.includes(query);
          const show = matchesFilter && matchesSearch;

          card.classList.toggle('hidden-card', !show);
          if (show) visible++;
        });

        resultsLine.textContent = visible === 1
          ? 'Εμφανίζεται 1 εργαλείο.'
          : 'Εμφανίζονται ' + visible + ' εργαλεία.';

        noResults.style.display = visible === 0 ? 'block' : 'none';
        noResults.setAttribute('aria-hidden', visible === 0 ? 'false' : 'true');
      }

      filterButtons.forEach(button => {
        button.addEventListener('click', () => {
          activeFilter = button.dataset.filter || 'all';

          filterButtons.forEach(btn => {
            const isActive = btn === button;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
          });

          updateCards();
        });
      });

      searchInput.addEventListener('input', updateCards);
      updateCards();
    })();
  </script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
