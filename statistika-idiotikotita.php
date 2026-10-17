<?php
require_once __DIR__ . '/includes/config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['analytics_pref']) && in_array($_POST['analytics_pref'], array('off','on'), true)) {
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    if ($_POST['analytics_pref'] === 'off') {
        setcookie('edu_analytics_off', '1', array('expires'=>time()+365*86400,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax'));
    } else {
        setcookie('edu_analytics_off', '', array('expires'=>time()-3600,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax'));
    }
    header('Location: statistika-idiotikotita.php', true, 303); exit;
}
$eduOptedOut = !empty($_COOKIE['edu_analytics_off']);
?>
<!doctype html><html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Στατιστικά και ιδιωτικότητα — Εργαλειοθήκη</title><link rel="stylesheet" href="<?= htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8') ?>"></head>
<body class="edu-ui edu-calc-standard"><?php require __DIR__ . '/includes/header.php'; ?>
<main id="main-content" class="page-shell" style="max-width:900px;margin:auto;padding:20px"><section class="card" style="padding:24px">
<h1>Στατιστικά χρήσης και ιδιωτικότητα</h1>
<p>Η Εργαλειοθήκη συγκεντρώνει μόνο αθροιστικά στοιχεία ανά ημέρα και ανά εργαλείο: φορτώσεις σελίδας και δευτερόλεπτα ενεργής χρήσης. Ο ενεργός χρόνος εκτιμάται μόνο όταν η καρτέλα είναι ορατή και ο χρήστης παραμένει ενεργός.</p>
<p>Δεν δημιουργούμε αναγνωριστικό επισκέπτη ούτε εγκαθιστούμε cookie για τα στατιστικά. Δεν αποθηκεύουμε διευθύνσεις IP, ονόματα, emails, αριθμούς μητρώου, περιεχόμενο φορμών, κωδικούς ή διαδρομές χρήστη στα δεδομένα των στατιστικών. Το hosting ενδέχεται να διατηρεί ανεξάρτητα τυπικά αρχεία προσβάσεων.</p>
<p>Οι αριθμοί είναι συγκεντρωτικοί και <strong>δεν αντιστοιχούν σε μοναδικούς επισκέπτες</strong>. Μπορείτε να εξαιρεθείτε από όλες τις μετρήσεις με το παρακάτω κουμπί. Επίσης τιμούμε «Do Not Track» και «Global Privacy Control», όπου υποστηρίζονται. Δεν χρησιμοποιούνται εξωτερικές υπηρεσίες analytics.</p>
<p>Τα δεδομένα της Εργαλειοθήκης προορίζονται αποκλειστικά για συγκριτική βελτίωση των εργαλείων, και το dashboard είναι διαθέσιμο μόνο στον διαχειριστή. Πριν από τη δημοσίευση της λειτουργίας πρέπει να ελεγχθούν οι υποχρεώσεις ενημέρωσης και τυχόν συγκατάθεσης με βάση την πολιτική του ιστοτόπου.</p>
<form method="post" style="margin-top:24px"><p><strong><?= $eduOptedOut ? 'Οι ανώνυμες μετρήσεις είναι απενεργοποιημένες για αυτόν τον browser.' : 'Οι ανώνυμες μετρήσεις είναι ενεργές για αυτόν τον browser.' ?></strong></p><button type="submit" name="analytics_pref" value="<?= $eduOptedOut ? 'on' : 'off' ?>" style="padding:12px 18px;border:1px solid #778ca3;border-radius:10px;cursor:pointer"><?= $eduOptedOut ? 'Ενεργοποίηση ανώνυμων στατιστικών' : 'Να μη μετριέται η χρήση μου' ?></button><p class="small-note">Η επιλογή εξαίρεσης αποθηκεύεται σε ένα απαραίτητο cookie προτίμησης, χωρίς αναγνωριστικό επισκέπτη.</p></form>
</section></main><?php require __DIR__ . '/includes/footer.php'; ?></body></html>
