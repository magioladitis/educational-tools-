<?php
/**
 * Deadlines displayed on the central tools page (ergaleia.php).
 *
 * Update this file when a deadline is added, extended or removed.
 * Presentation remains in includes/components/deadline-card.php.
 * PHP syntax intentionally remains compatible with older server runtimes.
 */
return array(
    'title' => '📅 Ενεργές & προσεχείς προθεσμίες',
    'intro' => 'Συγκεντρωτικά σημαντικές προθεσμίες για εκπαιδευτικούς, μαζί με όσες συνδέονται άμεσα με εργαλεία της Εργαλειοθήκης.',
    'collapsible' => true,
    'expanded' => true,
    'items' => array(
        array(
            'title' => 'Νεοδιόριστοι 2026 — Ορκωμοσία & ανάληψη υπηρεσίας',
            'meta_html' => '4.789 εκπαιδευτικοί Α/θμιας και Β/θμιας και 470 μέλη ΕΕΠ–ΕΒΠ · από <strong>24/08/2026</strong> έως και <strong>28/08/2026, 15:00</strong>.',
            'start' => '2026-08-24T00:00:00+03:00',
            'end' => '2026-08-28T15:00:00+03:00',
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
            'title' => 'Υποδιευθυντές δημόσιων Σ.Α.Ε.Κ. — αιτήσεις υποψηφιότητας',
            'meta_html' => 'Για μόνιμους εκπαιδευτικούς Σ.Α.Ε.Κ., διοικητικούς υπαλλήλους με σχέση εργασίας και αποσπασμένους εκπαιδευτικούς για διοικητικό έργο, που υπηρετούν στην οικεία Σ.Α.Ε.Κ. και πληρούν τις προϋποθέσεις της πρόσκλησης · αιτήσεις αποκλειστικά στη Σ.Α.Ε.Κ. όπου υπηρετούν από <strong>01/09/2026</strong> έως και <strong>10/09/2026</strong>.',
            'start' => '2026-09-01T00:00:00+03:00',
            'end_exclusive' => '2026-09-11T00:00:00+03:00',
            'source_url' => 'https://gsvetlly.minedu.gov.gr/publications/docs2023/%CE%A0%CF%81%CF%8C%CF%83%CE%BA%CE%BB%CE%B7%CF%83%CE%B7_%CE%B5%CE%BA%CE%B4%CE%AE%CE%BB%CF%89%CF%83%CE%B7%CF%82_%CE%B5%CE%BD%CE%B4%CE%B9%CE%B1%CF%86%CE%AD%CF%81%CE%BF%CE%BD%CF%84%CE%BF%CF%82_%CE%B3%CE%B9%CE%B1_%CF%84%CE%B7%CE%BD_%CF%80%CE%BB%CE%AE%CF%81%CF%89%CF%83%CE%B7_%CE%BC%CE%B5_%CE%B5%CF%80%CE%B9%CE%BB%CE%BF%CE%B3%CE%AE_%CE%B8%CE%AD%CF%83%CE%B5%CF%89%CE%BD_%CE%A5%CF%80%CE%BF%CE%B4%CE%B9%CE%B5%CF%85%CE%B8%CF%85%CE%BD%CF%84%CF%8E%CE%BD.pdf',
            'source_label' => 'ΥΠΑΙΘΑ — Πρόσκληση 113585/01-09-2026 · ΑΔΑ 6ΤΨΝ46ΝΚΠΔ-ΦΗΙ ↗',
            'open_text' => 'Η περίοδος υποβολής αιτήσεων είναι ανοικτή.',
            'closed_text' => 'Η προθεσμία υποβολής αιτήσεων έχει λήξει.',
            'tool_url' => 'dikaioma-ypodiefthynti-saek.php',
            'tool_label' => 'Έλεγξε αν έχεις δικαίωμα →'
        ),
        array(
            'title' => 'ΣΑΕΚ Σιβιτανιδείου — ωρομίσθιοι εκπαιδευτές 2026–2027',
            'meta_html' => 'Ηλεκτρονικές αιτήσεις για το φθινοπωρινό εξάμηνο 2026Β και το εαρινό 2027Α · <strong>ΠΑΡΑΤΑΣΗ</strong> λόγω τεχνικού προβλήματος έως <strong>07/09/2026, 10:30</strong>.',
            'start' => '2026-08-24T12:00:00+03:00',
            'end' => '2026-09-07T10:30:00+03:00',
            'source_url' => 'https://www.sivitanidios.edu.gr/2026/09/paratasi.html',
            'source_label' => 'Σιβιτανίδειος — Παράταση 04/09/2026 ↗',
            'open_text' => 'Η περίοδος υποβολής αιτήσεων είναι ανοικτή.',
            'closed_text' => 'Η προθεσμία υποβολής αιτήσεων έχει λήξει.',
            'tool_url' => 'ypologismos-morion-sivitanidios-saek.php',
            'tool_label' => 'Υπολόγισε μόρια →'
        )
    ),
    'note_html' => 'Στις προσκλήσεις νεοδιοριζομένων εκπαιδευτικών και μελών ΕΕΠ–ΕΒΠ για αιτήσεις απόσπασης, στις προσκλήσεις αναπληρωτών και ΕΕΠ–ΕΒΠ και στην πρόσκληση Υποδιευθυντών δημόσιων Σ.Α.Ε.Κ. δεν αναφέρεται συγκεκριμένη ώρα λήξης· το countdown θεωρεί τεχνικά το τέλος της αντίστοιχης καταληκτικής ημέρας. Οι επίσημες ανακοινώσεις, οι προσκλήσεις και οι ηλεκτρονικές πλατφόρμες υπερισχύουν.'
);
