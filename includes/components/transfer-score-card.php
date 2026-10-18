<?php
/* Shared transfer score card. Presentation only: calculators retain their own validation. */
if (!function_exists('transferScoreCard')) {
 function transferScoreCard($grade) {
  $primary = $grade === 'primary';
  $ids = $primary ? array('total'=>'primaryTotal','label'=>'primaryTotalLabel','service'=>'primaryServiceResult','msd'=>'primaryMsdResult','coservice'=>'primaryCoserviceResult','family'=>'primaryFamilyResult','locality'=>'primaryLocalityResult','first'=>'primaryFirstResult','status'=>'primaryTotalStatus')
      : array('total'=>'totalResult','label'=>'transferTotalLabel','service'=>'servicePointsResult','msd'=>'msdPointsResult','coservice'=>'coServiceResult','family'=>'familyResult','locality'=>'localityResult','first'=>'firstPreferenceResult','status'=>'statusResult');
  calculatorScoreHeader(array('value'=>$primary?'—':'0','value_id'=>$ids['total'],'label'=>$primary?'μονάδες μετάθεσης':'συνολικές μονάδες','label_id'=>$ids['label'],'cap'=>'Ενδεικτική βαθμολογία μετάθεσης'));
  $rows=array(array('Μόρια προϋπηρεσίας','service'),array('Μ.Σ.Δ.','msd'),array('Συνυπηρέτηση','coservice'),array('Οικογενειακοί λόγοι','family'),array('Εντοπιότητα','locality'),array('Πρώτη προτίμηση','first'));
  foreach ($rows as $row) calculatorResultRow(array('label'=>$row[0],'value'=>$primary?'—':'0','value_id'=>$ids[$row[1]]));
  calculatorResultMessage(array('variant'=>'status','id'=>$ids['status'],'html'=>$primary?'Συμπληρώστε τα μόρια από το μητρώο.':'Πρόσθεσε τα στοιχεία και τις υπηρετήσεις σου.'));
 }
}
