<?php
/**
 * Service-change timeline dataset.
 *
 * Historical dates originate from the user's working chronology. A record is
 * marked latest_verified only when the latest-cycle value has been checked
 * against an official Ministry/education-authority source. Older historical
 * dates are retained as research data and are not presented as fully
 * source-verified yet.
 */
return array(
    'updated_at' => '28/09/2026',
    'current_cycle' => '2026-2027',
    'history_years' => array('2019-2020','2020-2021','2021-2022','2022-2023','2023-2024','2024-2025','2025-2026'),
    'groups' => array(
        'metatheseis' => 'Μεταθέσεις',
        'paraitiseis' => 'Παραιτήσεις',
        'apospaseis' => 'Αποσπάσεις',
        'metatakseis' => 'Μετατάξεις',
        'neodioristoi' => 'Νεοδιόριστοι',
    ),
    'events' => array(
        array(
            'id' => 'metatheseis-circular',
            'group' => 'metatheseis',
            'title' => 'Εγκύκλιος μεταθέσεων',
            'latest' => '15/10/2025',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('12/11/2019','03/11/2020','11/11/2021','14/10/2022','12/10/2023','15/10/2024','15/10/2025'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μεταθέσεις Δ.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/site/63068-15-10-25-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-sxolikoy-etous-2025-2026',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μεταθέσεις Π.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/site/63072-15-10-25-metatheseis-ekpaideftikon-protovathmias-ekpaidefsis-sxolikoy-etous-2025-2026',
                ),
            ),
            'note' => 'Οι Π.Ε. και Δ.Ε. έχουν χωριστές επίσημες εγκυκλίους, με κοινή ημερομηνία έκδοσης.',
        ),
        array(
            'id' => 'metatheseis-applications',
            'group' => 'metatheseis',
            'title' => 'Αιτήσεις μετάθεσης',
            'latest' => '16–31/10/2025',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('12–25/11/2019','04–17/11/2020','11–22/11/2021','16–31/10/2022','16–31/10/2023','16–31/10/2024','16–31/10/2025'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/site/63068-15-10-25-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-sxolikoy-etous-2025-2026',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Πρόσκληση ΕΕΠ-ΕΒΠ 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/site/63076-15-10-25-prosklisi-melon-eidikoy-ekpaideftikoy-prosopikoy-eep-kai-eidikoy-voithitikoy-prosopikoy-evp-gia-ypovoli-aitiseon-metathesis-sxolikoy-etous-2025-2026',
                ),
            ),
            'note' => 'Η περίοδος αιτήσεων του τελευταίου κύκλου είναι 16 έως 31 Οκτωβρίου 2025.',
        ),
        array(
            'id' => 'temporary-transfer-points',
            'group' => 'metatheseis',
            'title' => 'Προσωρινοί πίνακες μορίων μετάθεσης',
            'latest' => '24/11/2025',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('13/12/2019','07/12/2020','13/12/2021','21/11/2022','21/11/2023','24/11/2024','24/11/2025'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A9%CE%9D_%CE%94%CE%95_2025-2026_6%CE%94%CE%A7%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%A4%CE%954.pdf',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Π.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/%CE%A8%CE%A9%CE%9E%CE%A646%CE%9D%CE%9A%CE%A0%CE%94-%CE%A0%CE%9C%CE%A8_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%99%CE%A3_%CE%95%CE%9A%CE%A0_%CE%A0%CE%95_2025_2026.pdf',
                ),
            ),
            'note' => 'Η εγκύκλιος Δ.Ε. ορίζει ανακοίνωση των πινάκων μοριοδότησης στις 24/11/2025 και αιτήματα διορθώσεων/παραλείψεων 24–28/11. Η ημερομηνία του ιστορικού αρχείου επιβεβαιώνεται για τον τελευταίο κύκλο.',
        ),
        array(
            'id' => 'transfer-application-withdrawal',
            'group' => 'metatheseis',
            'title' => 'Ανάκληση αίτησης μετάθεσης — λήξη',
            'latest' => '31/12/2025 · 15:00',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('31/12/2019','31/12/2020','31/12/2021','31/12/2022','31/12/2023','31/12/2024','31/12/2025'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A9%CE%9D_%CE%94%CE%95_2025-2026_6%CE%94%CE%A7%CE%9F46%CE%9D%CE%9A%CE%A0%CE%94-%CE%A4%CE%954.pdf',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Π.Ε. 2025–2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/%CE%A8%CE%A9%CE%9E%CE%A646%CE%9D%CE%9A%CE%A0%CE%94-%CE%A0%CE%9C%CE%A8_%CE%9C%CE%95%CE%A4%CE%91%CE%98%CE%95%CE%A3%CE%95%CE%99%CE%A3_%CE%95%CE%9A%CE%A0_%CE%A0%CE%95_2025_2026.pdf',
                ),
            ),
            'note' => 'Πρόκειται για ανάκληση της αίτησης πριν από τη διενέργεια των μεταθέσεων. Η εγκύκλιος Δ.Ε. διευκρινίζει ότι ανάκληση ήδη πραγματοποιημένης μετάθεσης δεν προβλέπεται.',
        ),
        array(
            'id' => 'resignations-applications',
            'group' => 'paraitiseis',
            'title' => 'Αιτήσεις παραίτησης',
            'latest' => '01–11/02/2026 ηλεκτρονικά · 02–11/02/2026 δια ζώσης',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('01–10/04/2020','17/02–10/03/2021','01–11/02/2022','01–13/02/2023','01–12/02/2024','01–11/02/2025','02–11/02/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Υποβολή αιτήσεων παραίτησης 2025–2026 (11087/Ε3/29-01-2026)',
                    'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/RT3TH46NKPD-YCH5.pdf',
                ),
            ),
            'note' => 'Η επίσημη εγκύκλιος επιτρέπει ηλεκτρονική υποβολή από 01/02/2026 (πρωτοκόλληση 02/02) και δια ζώσης από 02/02 έως 11/02/2026. Το ιστορικό κελί κρατά το υπηρεσιακό εύρος 02–11/02.',
        ),
        array(
            'id' => 'organic-gaps-circular',
            'group' => 'metatheseis',
            'title' => 'Εγκύκλιος προσδιορισμού οργανικών κενών / πλεονασμάτων',
            'latest' => '03/03/2026',
            'latest_verified' => true,
            'source_coverage' => 'official-reference',
            'history' => array('18/02/2020','02/03/2021','24/02/2022','02/02/2023','23/02/2024','04/03/2025','03/03/2026'),
            'sources' => array(
                array(
                    'label' => 'ΔΔΕ Αργολίδας — επίσημο έγγραφο που παραπέμπει στις εγκυκλίους 25616/Ε2 και 25644/Ε2/03-03-2026',
                    'url' => 'https://dide.arg.sch.gr/site/wp-content/uploads/2026/03/%CE%95%CE%9D%CE%97%CE%9C%CE%95%CE%A1%CE%A9%CE%A3%CE%97-%CE%93%CE%99%CE%91-%CE%A5%CE%A0%CE%95%CE%A1%CE%91%CE%A1%CE%99%CE%98%CE%9C%CE%99%CE%95%CE%A3-%CE%9A%CE%91%CE%99-%CE%9F%CE%A1%CE%93%CE%91%CE%9D%CE%99%CE%9A%CE%91-%CE%9A%CE%95%CE%9D%CE%91-2026.pdf',
                ),
            ),
            'note' => 'Η ημερομηνία και οι αριθμοί πρωτοκόλλου επιβεβαιώνονται από επίσημο υπηρεσιακό έγγραφο ΔΔΕ που εφαρμόζει τις εγκυκλίους ΥΠΑΙΘΑ. Δεν έχει ακόμη εντοπιστεί απευθείας κεντρικός σύνδεσμος των δύο εγκυκλίων.',
        ),
        array(
            'id' => 'resignation-withdrawal',
            'group' => 'paraitiseis',
            'title' => 'Ανάκληση αίτησης παραίτησης',
            'latest' => 'Έως 1 μήνα από κάθε αίτηση · έως 11/03/2026 για αίτηση 11/02',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('10/05/2020','12/03/2021','13/03/2022','14/03/2023','13/03/2024','11/03/2025','11/03/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Υποβολή αιτήσεων παραίτησης 2025–2026 (11087/Ε3/29-01-2026)',
                    'url' => 'https://www.minedu.gov.gr/images/joomlart/PDFs/RT3TH46NKPD-YCH5.pdf',
                ),
            ),
            'note' => 'Δεν υπάρχει μία κοινή ημερομηνία για όλους: η αποκλειστική προθεσμία ανάκλησης είναι ένας μήνας από την ημερομηνία της αρχικής αίτησης. Η 11/03 είναι η τελευταία δυνατή ημερομηνία για αίτηση που υποβλήθηκε 11/02.',
        ),
        array(
            'id' => 'transfer-results',
            'group' => 'metatheseis',
            'title' => 'Ανακοινώσεις μεταθέσεων',
            'latest' => 'Π.Ε. 19/03 · Δ.Ε. 23/03 · ΕΕΠ-ΕΒΠ 03/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('Π.Ε. 07/04 · Δ.Ε. 24/04/2020','24/03/2021','18/03/2022','02/03/2023','27/03/2024','20/03/2025','Π.Ε. 19/03 · Δ.Ε. 23/03 · ΕΕΠ-ΕΒΠ 03/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μεταθέσεις Π.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64520-19-03-26-apo-to-ypourgeio-paideias-thriskevmaton-kai-athlitismoy-anakoinonontai-oi-metatheseis-ekpaideftikon-tis-protovathmias-ekpaidefsis-genikis-ekpaidefsis-kai-eidikis-agogis-etous-2026',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μεταθέσεις Δ.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/en/mixanografiko?catid=1183&id=70019%3A23-03-26-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-sti-geniki-ekpaidefsi-kai-tin-eidiki-agogi-kai-ekpaidefsi&view=article',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μεταθέσεις ΕΕΠ-ΕΒΠ 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64693-03-04-26-ekdosi-ypourgikis-apofasis-metatheseon-melon-eep-evp-sxolikoy-etous-2025-2026',
                ),
            ),
            'note' => 'Στο εργαλείο οι τρεις κατηγορίες εμφανίζονται μαζί, αλλά κρατούν τις χωριστές ημερομηνίες και πηγές τους.',
        ),
        array(
            'id' => 'transfer-objections',
            'group' => 'metatheseis',
            'title' => 'Αιτήσεις θεραπείας / επανεξέτασης μετά τις μεταθέσεις',
            'latest' => '2026: δεν εντοπίστηκε ενιαία δημοσιευμένη προθεσμία',
            'latest_verified' => false,
            'source_coverage' => 'research',
            'history' => array('25/04–09/05/2020','25/03–08/04/2021','19/03–02/04/2022','03/03–17/03/2023','28/03–11/04/2024','21/03–04/04/2025',null),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026 (15/10/2025)',
                    'url' => 'https://www.minedu.gov.gr/site/63068-15-10-25-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-sxolikoy-etous-2025-2026',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αμοιβαίες μεταθέσεις Δ.Ε.: 15ήμερη προθεσμία έως 07/04/2026',
                    'url' => 'https://www.minedu.gov.gr/site/70026-24-03-26-aitiseis-gia-amoivaies-metatheseis-sti-vvathmia-ekpaidefsi',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Τροποποίηση απόφασης μεταθέσεων Δ.Ε. (08/06/2026)',
                    'url' => 'https://www.minedu.gov.gr/site/65177-08-06-26-tropopoiisi-tis-ypo-36339-e2-23-03-2026-y-a-me-thema-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-etous-2027',
                ),
            ),
            'note' => 'Η εγκύκλιος μεταθέσεων Δ.Ε. 2025–2026 προβλέπει ρητά προθεσμία 15 ημερών για τις αμοιβαίες μεταθέσεις, όχι όμως μία κοινή 15ήμερη περίοδο «ενστάσεων» μετά την ανακοίνωση των μεταθέσεων. Το ΥΠΑΙΘΑ δημοσίευσε αργότερα τροποποιήσεις αποφάσεων μεταθέσεων, άρα αιτήματα θεραπείας/επανεξέτασης πράγματι εξετάζονται κατά περίπτωση. Οι ιστορικές περίοδοι του αρχείου διατηρούνται, αλλά δεν παρουσιάζονται ως ισχύων κανόνας για το 2026 χωρίς πρωτογενή πηγή ανά έτος.',
        ),
        array(
            'id' => 'mutual-transfer-applications',
            'group' => 'metatheseis',
            'title' => 'Αμοιβαίες μεταθέσεις — λήξη αιτήσεων',
            'latest' => 'Π.Ε. 03/04 · Δ.Ε. 07/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('09/05/2020','08/04/2021','02/04/2022','17/03/2023','11/04/2024','04/04/2025','Π.Ε. 03/04 · Δ.Ε. 07/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αιτήσεις αμοιβαίας μετάθεσης Π.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64531-23-03-26-aitiseis-gia-amoivaia-metathesi',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αιτήσεις αμοιβαίων μεταθέσεων Δ.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/site/70026-24-03-26-aitiseis-gia-amoivaies-metatheseis-sti-vvathmia-ekpaidefsi',
                ),
            ),
            'note' => 'Για την Π.Ε. η 15ήμερη προθεσμία λήγει 03/04/2026. Η ίδια ανακοίνωση ζητά την αποστολή της συμπληρωμένης αίτησης στο πρωτόκολλο έως 14/04/2026· αυτό είναι ξεχωριστό στάδιο διαβίβασης. Για τη Δ.Ε. η 15ήμερη προθεσμία λήγει 07/04/2026.',
        ),
        array(
            'id' => 'detachments-circular',
            'group' => 'apospaseis',
            'title' => 'Εγκύκλιος αποσπάσεων',
            'latest' => '02/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('19/04/2020','16/04/2021','04/04/2022','27/03/2023','04/04/2024','03/04/2025','02/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αποσπάσεις ΠΥΣΠΕ/ΠΥΣΔΕ 2026–2027',
                    'url' => 'https://www.minedu.gov.gr/site/64683-02-04-26-prosklisi-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-gia-ypovoli-aitiseon-apospaseon-apo-pyspe-pysde-se-pyspe-pysde-se-domes-e-a-e-ke-d-a-s-y-mousika-kai-kallitexnika-sxoleia-gia-to-didaktiko-etos-2026-2027',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αποσπάσεις σε υπηρεσίες/φορείς 2026–2027',
                    'url' => 'https://www.minedu.gov.gr/site/64684-02-04-26-prosklisi-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-gia-ypovoli-aitiseon-apospaseon-se-ypiresies-kai-foreis-armodiotitas-tou-ypourgeiou-paideias-thriskevmaton-kai-athlitismoy-apo-to-sxoliko-etos-2026-2027-kai-me-monoeti-trieti-pentaeti-diarkeia-kata-periptosi',
                ),
            ),
            'note' => 'Την ίδια ημέρα εκδόθηκαν χωριστές προσκλήσεις για μετακινήσεις μεταξύ περιοχών/δομών και για υπηρεσίες/φορείς.',
        ),
        array(
            'id' => 'detachments-applications',
            'group' => 'apospaseis',
            'title' => 'Αιτήσεις απόσπασης εκπαιδευτικών',
            'latest' => '03–20/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('30/04–11/05/2020','20–27/04/2021','05–15/04/2022','06–18/04/2023','08–17/04/2024','07–23/04/2025','03–20/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Πρόσκληση αποσπάσεων 02/04/2026',
                    'url' => 'https://www.minedu.gov.gr/site/64683-02-04-26-prosklisi-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-gia-ypovoli-aitiseon-apospaseon-apo-pyspe-pysde-se-pyspe-pysde-se-domes-e-a-e-ke-d-a-s-y-mousika-kai-kallitexnika-sxoleia-gia-to-didaktiko-etos-2026-2027',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος 41297/Ε2/02-04-2026 (PDF)',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/41297E2_02-04-2026_%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%9B%CE%99%CE%9F%CE%A3_%CE%91%CE%A0%CE%9F%CE%A3%CE%A0_%CE%A0%CE%A5%CE%A3%CE%A0%CE%95_%CE%A0%CE%A5%CE%A3%CE%94%CE%95_2026-2027_9%CE%9F%CE%99%CE%9B46%CE%9D%CE%9A%CE%A0%CE%94-4%CE%936.pdf',
                ),
            ),
            'note' => 'Διορθώθηκε το ιστορικό 2023–2024 από «08/0/2024» σε «08/04/2024». Για το 2026, η επίσημη εγκύκλιος 41297/Ε2/02-04-2026 ορίζει αιτήσεις από 03/04 έως 20/04/2026, ώρα 15:00.',
        ),
        array(
            'id' => 'eep-ebp-detachments',
            'group' => 'apospaseis',
            'title' => 'Αιτήσεις απόσπασης ΕΕΠ-ΕΒΠ',
            'latest' => '19/05–02/06/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,'03–10/06/2022','06–18/04/2023','15–17/07/2024','14–26/05/2025','19/05–02/06/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος αποσπάσεων ΕΕΠ-ΕΒΠ 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64980-19-05-26-ekdosi-egkykliou-apospaseon-melon-eep-evp',
                ),
            ),
            'note' => 'Η επίσημη ανακοίνωση αναφέρει ρητά το διάστημα 19/05 έως 02/06/2026.',
        ),
        array(
            'id' => 'mutual-transfer-primary-result',
            'group' => 'metatheseis',
            'title' => 'Αμοιβαίες μεταθέσεις Π.Ε. — ανακοίνωση',
            'latest' => '05/05/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,null,'05/04/2023','31/05/2024','24/04/2025','05/05/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αμοιβαίες μεταθέσεις Π.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64863-05-05-26-amoivaies-metatheseis-ekpaideftikon-a-thmias-ekpaidefsis-etous-2027',
                ),
            ),
            'note' => '',
        ),
        array(
            'id' => 'mutual-transfer-secondary-result',
            'group' => 'metatheseis',
            'title' => 'Αμοιβαίες μεταθέσεις Δ.Ε. — ανακοίνωση',
            'latest' => '13/05/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,'11/05/2022','20/04/2023','19/06/2024','30/05/2025','13/05/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αμοιβαίες μεταθέσεις Δ.Ε. 2026',
                    'url' => 'https://www.minedu.gov.gr/site/64931-13-05-26-amoivaies-metatheseis-ekpaideftikon-defterovathmias-ekpaidefsis-etous-2028',
                ),
            ),
            'note' => '',
        ),
        array(
            'id' => 'bodies-detachment-application-withdrawal',
            'group' => 'apospaseis',
            'title' => 'Ανάκληση αίτησης απόσπασης σε υπηρεσίες / φορείς — λήξη',
            'latest' => '27/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('12/05/2020','31/05/2021','10/05/2022','25/04/2023','23/04/2024','30/04/2025','27/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος αποσπάσεων σε υπηρεσίες/φορείς 41298/Ε2/02-04-2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/41298E2_02-04-2026_%CE%95%CE%93%CE%9A%CE%A5%CE%9B%CE%99%CE%9F%CE%A3_%CE%91%CE%A0%CE%9F%CE%A3%CE%A0_%CE%A6%CE%9F%CE%A1%CE%95%CE%99%CE%A3_2026-2027_%CE%95%CE%A9%CE%95%CE%9946%CE%9D%CE%9A%CE%A0%CE%94-%CE%9D06.pdf',
                ),
            ),
            'note' => 'Η εγκύκλιος ορίζει ότι η ανάκληση της αίτησης γίνεται μόνο μέσω ΟΠΣΥΔ, με απενεργοποίηση, έως τη Δευτέρα 27/04/2026.',
        ),
        array(
            'id' => 'metatakseis-circular',
            'group' => 'metatakseis',
            'title' => 'Εγκύκλιος μετατάξεων',
            'latest' => '30/04/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('20/05/2020','07/05/2021','16/05/2022','29/03/2023','19/04/2024','29/04/2025','30/04/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μετατάξεων 2026 (52463/Ε2/30-04-2026)',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/52463%CE%952_30-04-2026_9%CE%A9%CE%93%CE%A346%CE%9D%CE%9A%CE%A0%CE%94-%CE%A5%CE%97%CE%92.pdf',
                ),
            ),
            'note' => 'Στο αρχικό φύλλο το κελί του 2025–2026 έδειχνε 30/04/2025. Η επίσημη εγκύκλιος επιβεβαιώνει 30/04/2026.',
        ),
        array(
            'id' => 'metatakseis-applications',
            'group' => 'metatakseis',
            'title' => 'Αιτήσεις μετάταξης',
            'latest' => '04–15/05/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('21–29/05/2020','10–17/05/2021','17–26/05/2022','29/03–11/04/2023','22/04–01/05/2024','30/04–12/05/2025','04–15/05/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μετατάξεων 2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/52463%CE%952_30-04-2026_9%CE%A9%CE%93%CE%A346%CE%9D%CE%9A%CE%A0%CE%94-%CE%A5%CE%97%CE%92.pdf',
                ),
            ),
            'note' => 'Η εγκύκλιος αναφέρει αιτήσεις από Δευτέρα 04/05 έως Παρασκευή 15/05/2026.',
        ),
        array(
            'id' => 'detachment-application-withdrawal',
            'group' => 'apospaseis',
            'title' => 'Ανάκληση αίτησης απόσπασης ΠΥΣΠΕ/ΠΥΣΔΕ — λήξη',
            'latest' => '20/05/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('25/05/2020','24/05/2021','10/06/2022','02/06/2023','17/05/2024','23/05/2025','20/05/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος αποσπάσεων ΠΥΣΠΕ/ΠΥΣΔΕ 41297/Ε2/02-04-2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/41297E2_02-04-2026_%CE%95%CE%93%CE%9A%CE%A5%CE%9A%CE%99%CE%9F%CE%A3_%CE%91%CE%A0%CE%9F%CE%A3%CE%A0_%CE%A0%CE%A5%CE%A3%CE%A0%CE%95_%CE%A0%CE%A5%CE%A3%CE%94%CE%95_2026-2027_9%CE%9F%CE%99%CE%9B46%CE%9D%CE%9A%CE%A0%CE%94-4%CE%936.pdf',
                ),
            ),
            'note' => 'Η ίδια η αίτηση μπορεί να απενεργοποιηθεί από τον εκπαιδευτικό έως 20/05/2026. Μετά την ανακοίνωση αποτελεσμάτων προβλέπεται ξεχωριστή διαδικασία επανεξέτασης/ανάκλησης εντός πέντε ημερών, η οποία δεν ταυτίζεται με αυτή τη γραμμή.',
        ),
        array(
            'id' => 'metatakseis-application-withdrawal',
            'group' => 'metatakseis',
            'title' => 'Ανάκληση αίτησης μετάταξης — λήξη',
            'latest' => '27/05/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('15/06/2020','31/05/2021','10/06/2022','28/04/2023','15/05/2024','23/05/2025','27/05/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Εγκύκλιος μετατάξεων 52463/Ε2/30-04-2026',
                    'url' => 'https://www.minedu.gov.gr/publications/docs2023/52463%CE%952_30-04-2026_9%CE%A9%CE%93%CE%A346%CE%9D%CE%9A%CE%A0%CE%94-%CE%A5%CE%97%CE%92.pdf',
                ),
            ),
            'note' => 'Η εγκύκλιος ορίζει ηλεκτρονική ανάκληση της αίτησης μετάταξης έως και την Τετάρτη 27/05/2026.',
        ),
        array(
            'id' => 'first-bodies-detachments',
            'group' => 'apospaseis',
            'title' => 'Πρώτες ανακοινώσεις αποσπάσεων σε υπηρεσίες / φορείς',
            'latest' => '15/06/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('22/06/2020','09/07/2021','17/06/2022','07/07/2023','27/05/2024','06/06/2025','15/06/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — επίσημη ενότητα κινητικότητας (αναρτήσεις 15/06/2026)',
                    'url' => 'https://www.minedu.gov.gr/monimoi-metatakseis-metatheseis-apospaseis?start=80',
                ),
                array(
                    'label' => 'ΥΠΑΙΘΑ — παράδειγμα πρώτης ανάρτησης 15/06/2026 (Ισραηλιτική Κοινότητα Αθηνών)',
                    'url' => 'https://www.minedu.gov.gr/site/65238-15-06-26-apospasi-ekpaideftikon-p-e-sto-dimotiko-sxoleio-tis-israilitikis-koinotitas-athinas-gia-to-sxoliko-etos-2026-2028',
                ),
            ),
            'note' => 'Στις 15/06/2026 εμφανίζονται οι πρώτες επιμέρους επίσημες αναρτήσεις που εντοπίστηκαν για φορείς/σχολικές δομές. Δεν σημαίνει ότι όλες οι κατηγορίες αποσπάσεων σε φορείς ανακοινώθηκαν την ίδια ημέρα.',
        ),
        array(
            'id' => 'functional-gaps-primary-circular',
            'group' => 'apospaseis',
            'title' => 'Λειτουργικά κενά / πλεονάσματα για αποσπάσεις — Π.Ε.',
            'latest' => '05/06/2026 · 73719/Ε2',
            'latest_verified' => true,
            'verification_label' => '✓ Η ημερομηνία και ο αριθμός πρωτοκόλλου ελέγχθηκαν σε ψηφιακό αντίγραφο επίσημης εγκυκλίου ΥΠΑΙΘΑ',
            'source_coverage' => 'official-document-copy',
            'history' => array(null,null,null,null,null,null,'05/06/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — 73719/Ε2/05-06-2026 (ψηφιακό αντίγραφο εγκυκλίου)',
                    'url' => 'https://www.alfavita.gr/sites/default/files/2026-06/egyklios-kenon-apospaseon-05.06.2026_1.pdf',
                ),
            ),
            'note' => 'Το ίδιο το έγγραφο φέρει ημερομηνία 05/06/2026 και αρ. πρωτ. 73719/Ε2 και αφορά τα λειτουργικά κενά/πλεονάσματα Πρωτοβάθμιας Εκπαίδευσης για τις αποσπάσεις 2026–2027. Το διαθέσιμο αντίγραφο είναι πιστό ψηφιακό αντίγραφο εγγράφου του ΥΠΑΙΘΑ, αλλά φιλοξενείται σε τρίτο ιστότοπο· δεν εντοπίστηκε ακόμη μόνιμος σύνδεσμος του ίδιου PDF στο minedu.gov.gr.',
        ),
        array(
            'id' => 'functional-gaps-secondary-circular',
            'group' => 'apospaseis',
            'title' => 'Λειτουργικά κενά / πλεονάσματα για αποσπάσεις — Δ.Ε.',
            'latest' => '05/06/2026 · αρ. πρωτ. υπό διασταύρωση',
            'latest_verified' => false,
            'source_coverage' => 'research',
            'history' => array(null,null,null,null,null,null,'05/06/2026'),
            'sources' => array(
                array(
                    'label' => 'Β. Παπαχρήστου — αναπαραγωγή εγγράφου ως 74047/Ε2/05-06-2026',
                    'url' => 'https://vaspapachristou.gr/kataxorisi-leitourgikon-kenon-gia-apospaseis-2026-2027/',
                ),
                array(
                    'label' => 'especial.gr — αναφορά εγγράφου ως 74045/Ε2/05-06-2026',
                    'url' => 'https://www.especial.gr/na-anakoinothoun-oi-apospaseis-se-foreis-oste-na-prochorisoun-oi-apospaseis-se-pysde-eae-kedasy/',
                ),
                array(
                    'label' => 'ΔΔΕ Πειραιά — επίσημη εφαρμογή της διαδικασίας λειτουργικών κενών',
                    'url' => 'https://dide-peiraia.att.sch.gr/index.php/menu-pysde/menu-pysde-announcements?start=10',
                ),
            ),
            'note' => 'Η ημερομηνία 05/06/2026 τεκμηριώνεται συνεκτικά, αλλά ο ακριβής αριθμός πρωτοκόλλου της εγκυκλίου Δ.Ε. παραμένει ανοιχτός: πλήρης αναπαραγωγή του κειμένου αναφέρει 74047/Ε2, ενώ άλλες αναφορές χρησιμοποιούν 74045/Ε2. Μέχρι να βρεθεί το πρωτογενές PDF/ανάρτηση του ΥΠΑΙΘΑ δεν επιλέγουμε έναν από τους δύο αριθμούς.',
        ),
        array(
            'id' => 'functional-gaps-history',
            'group' => 'apospaseis',
            'title' => 'Λειτουργικά κενά / πλεονάσματα — ιστορική συνοπτική γραμμή αρχείου',
            'latest' => 'Ιστορικό αρχείου — απαιτεί διάκριση Π.Ε./Δ.Ε. ανά έτος',
            'latest_verified' => false,
            'source_coverage' => 'research',
            'history' => array(null,'10/06/2021','26/05/2022','07/07/2023','23/05/2024','12/06/2025','05/06/2026'),
            'sources' => array(),
            'note' => 'Η αρχική καρτέλα είχε μία κοινή ιστορική γραμμή χωρίς διάκριση βαθμίδας. Τη διατηρούμε αυτούσια ως ερευνητικό ίχνος, αλλά δεν αποδίδουμε τις ημερομηνίες 2021–2025 ούτε στην Π.Ε. ούτε στη Δ.Ε. πριν βρεθούν οι αντίστοιχες πρωτογενείς πηγές.',
        ),
        array(
            'id' => 'first-primary-detachments',
            'group' => 'apospaseis',
            'title' => 'Πρώτη ανακοίνωση αποσπάσεων ΠΥΣΠΕ',
            'latest' => '30/06/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('10/07/2020','30/06/2021','06/07/2022','20/07/2023','19/06/2024','19/06/2025','30/06/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αποσπάσεις / Εγκύκλιοι & αποφάσεις',
                    'url' => 'https://www.minedu.gov.gr/kinitikotita/apospaseis-egkyklioi-proskliseis',
                ),
            ),
            'note' => 'Η επίσημη ενότητα του Υπουργείου καταγράφει την απόφαση ΠΥΣΠΕ→ΠΥΣΠΕ στις 30/06/2026.',
        ),
        array(
            'id' => 'first-secondary-detachments',
            'group' => 'apospaseis',
            'title' => 'Πρώτη ανακοίνωση αποσπάσεων ΠΥΣΔΕ',
            'latest' => '02/07/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array('16/07/2020','16/07/2021','06/07/2022','21/07/2023','27/06/2024','26/06/2025','02/07/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Αποσπάσεις Δ.Ε. / ΠΥΣΔΕ 2026–2027',
                    'url' => 'https://www.minedu.gov.gr/site/70258-02-07-26-apospaseis-bbathmias-ekpaideuses-kedasy-smeae-scholeia-typhlon-kophon-mousika-kallitechnika-kai-apo-pysde-se-pysde',
                ),
            ),
            'note' => '',
        ),
        array(
            'id' => 'metatakseis-to-eep',
            'group' => 'metatakseis',
            'title' => 'Μετατάξεις προς κλάδους ΕΕΠ',
            'latest' => 'Υ.Α. 13/08 · ανακοίνωση 19/08/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,null,null,null,'08/10/2025','Υ.Α. 13/08 · ανακοίνωση 19/08/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μετατάξεις εκπαιδευτικών/ΕΒΠ προς κλάδους ΕΕΠ (19/08/2026)',
                    'url' => 'https://www.minedu.gov.gr/site/70661-19-08-26-metataxeis-ekpaideutikon-protobathmias-kai-deuterobathmias-ekpaideuses-kai-melon-eidikou-boethetikou-prosopikou-se-kladous-eidikou-ekpaideutikou-prosopikou',
                ),
            ),
            'note' => 'Η ανακοίνωση της 19/08 παραπέμπει στην Υ.Α. 107886/Ε4/13-08-2026, η οποία δημοσιεύθηκε σε ΦΕΚ στις 18/08. Έτσι εξηγείται η ημερομηνία 13/08 στο αρχικό φύλλο.',
        ),
        array(
            'id' => 'metatakseis-to-primary',
            'group' => 'metatakseis',
            'title' => 'Μετατάξεις προς άλλους κλάδους Πρωτοβάθμιας / ΚΕΔΑΣΥ',
            'latest' => '20/08/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,null,null,null,null,'20/08/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μετατάξεις προς κλάδους Π.Ε./ΚΕΔΑΣΥ (20/08/2026)',
                    'url' => 'https://www.minedu.gov.gr/site/70666-20-08-26-metataxeis-ekpaideutikon-protobathmias-ekpaideuses-deuterobathmias-ekpaideuses-kai-melon-eidikou-boethetikou-prosopikou-eidikes-agoges-se-allous-kladous-tes-protobathmias-ekpaideuses-kai-se-ke-d-a-s-y-etous-2026',
                ),
            ),
            'note' => 'Από το 2026 εμφανίζεται ως διακριτή επίσημη ανακοίνωση. Δεν αποδίδουμε τις παλαιότερες συνοπτικές ημερομηνίες του αρχείου σε αυτή την ειδική κατηγορία χωρίς επιπλέον τεκμηρίωση.',
        ),
        array(
            'id' => 'metatakseis-to-secondary',
            'group' => 'metatakseis',
            'title' => 'Μετατάξεις προς άλλους κλάδους Δευτεροβάθμιας / ΚΕΔΑΣΥ',
            'latest' => '21/08/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,null,null,null,null,null,'21/08/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Μετατάξεις προς κλάδους Δ.Ε./ΚΕΔΑΣΥ (21/08/2026)',
                    'url' => 'https://www.minedu.gov.gr/site/70675-21-08-26-metataxeis-ekpaideutikon-athmias-ekpaideuses-bthmias-ekpaideuses-kai-melon-eep-ebp-tes-eidikes-agoges-se-allous-kladous-tes-bthmias-ekpaideuses-kai-se-ke-d-a-s-y-etous-2026',
                ),
            ),
            'note' => 'Από το 2026 εμφανίζεται ως διακριτή επίσημη ανακοίνωση. Δεν αποδίδουμε τις παλαιότερες συνοπτικές ημερομηνίες του αρχείου σε αυτή την ειδική κατηγορία χωρίς επιπλέον τεκμηρίωση.',
        ),
        array(
            'id' => 'metatakseis-results-history',
            'group' => 'metatakseis',
            'title' => 'Μετατάξεις — ιστορική συνοπτική γραμμή αρχείου',
            'latest' => 'Ιστορικό αρχείου (όχι ενιαίο γεγονός το 2026)',
            'latest_verified' => false,
            'source_coverage' => 'research',
            'history' => array('16/07/2020','09/08/2021','18/07/2022','16/08/2023','01/08/2024','02/09/2025 · ΕΕΠ 08/10/2025','13/08/2026'),
            'sources' => array(),
            'note' => 'Διατηρείται μόνο για να μη χαθεί το ιστορικό του αρχικού φύλλου. Για το 2026 η μία ημερομηνία δεν επαρκεί: οι μετατάξεις ανακοινώθηκαν σε διαφορετικές ημερομηνίες ανά κατηγορία, όπως φαίνεται στις τρεις τεκμηριωμένες κάρτες.',
        ),
        array(
            'id' => 'newly-appointed-detachment-circular',
            'group' => 'neodioristoi',
            'title' => 'Πρόσκληση αποσπάσεων νεοδιόριστων',
            'latest' => '25/08/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,'21/08/2021','17/08/2022','17/08/2023','21/08/2024','27/08/2025','25/08/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Πρόσκληση νεοδιοριζόμενων για απόσπαση 2026',
                    'url' => 'https://www.minedu.gov.gr/site/70699-25-08-26-prosklese-neodiorizomenon-ekpaideutikon-gia-ypobole-aiteseon-apospases',
                ),
            ),
            'note' => 'Αφορά νεοδιοριζόμενους Π.Ε./Δ.Ε. και τις προβλεπόμενες περιπτώσεις συζύγων/συμβιούντων.',
        ),
        array(
            'id' => 'newly-appointed-detachment-applications',
            'group' => 'neodioristoi',
            'title' => 'Αιτήσεις απόσπασης νεοδιόριστων',
            'latest' => '26/08–01/09/2026',
            'latest_verified' => true,
            'source_coverage' => 'latest',
            'history' => array(null,'21–24/08/2021','19–23/08/2022','21–23/08/2023','28/08–04/09/2024','29/08–03/09/2025','26/08–01/09/2026'),
            'sources' => array(
                array(
                    'label' => 'ΥΠΑΙΘΑ — Πρόσκληση νεοδιοριζόμενων 25/08/2026',
                    'url' => 'https://www.minedu.gov.gr/site/70699-25-08-26-prosklese-neodiorizomenon-ekpaideutikon-gia-ypobole-aiteseon-apospases',
                ),
                array(
                    'label' => 'ΠΔΕ Κεντρικής Μακεδονίας — επίσημη ανάρτηση εγκυκλίου 110726/Ε2/25-08-2026',
                    'url' => 'https://kmaked.pde.sch.gr/2026/08/26/prosklisi-neodiorizomenon-ekpaideftikon-protovathmias-kai-defterovathmias-ekpaidefsis-kai-syzygon-symviounton-afton-gia-ypovoli-aitiseon-apospasis-apo-pyspe-pysde-se-pyspe-pysde-kai-se-domes-tis-eidik/',
                ),
            ),
            'note' => 'Η επίσημη περιφερειακή ανάρτηση της εγκυκλίου 110726/Ε2/25-08-2026 επιβεβαιώνει υποβολή αιτήσεων στο ΟΠΣΥΔ από 26/08 έως και 01/09/2026.',
        ),
    ),
);
