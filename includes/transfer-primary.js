/* Isolated PRIMARY general, EAE and special structure transfer UI. Shared scoring module is unchanged. */
(function (global) {
  'use strict';
  var scoring = global.EducationTransfer;
  var eae = global.EducationTransferPrimaryEae;
  var eaeRoutes = global.EducationTransferPrimaryEaeRoutes;
  var intercultural = global.EducationTransferPrimaryIntercultural;
  var interVacancies = global.EducationTransferPrimaryInterculturalVacancies;
  var minority = global.EducationTransferPrimaryMinority;
  // Scoring must not silently stop if an unrelated eligibility/vacancy asset fails to load.
  // Primary's ordinary transfer scoring is independent of EAE and intercultural data.
  if (!scoring) return;
  var $ = function (id) { return document.getElementById(id); };
  if (!$('primaryMode')) return;
  var checklistState = global.EducationTransferChecklist ? global.EducationTransferChecklist.create() : null;
  // The official application type is selected once; the destination refines the
  // existing, distinct eligibility checks without creating a second application.
  function primaryEffectiveMode(){
    var group=$('primaryMode').value;
    if(group==='eae' || group==='sensory')return $('primaryDestination').value || (group==='eae'?'eae':'deaf');
    return group;
  }
  function syncPrimaryDestination(){
    var group=$('primaryMode').value;
    var field=$('primaryDestinationField'), select=$('primaryDestination');
    var grouped=group==='eae'||group==='sensory';
    field.hidden=!grouped;
    if(!grouped)return;
    if(select.dataset.group===group)return;
    var options=group==='eae'?
      [['eae','ΣΜΕΑΕ'],['eeeek','ΕΕΕΕΚ']]:
      [['deaf','Σχολείο Κωφών (ΕΝΓ)'],['blind','Σχολείο Τυφλών (Braille)']];
    select.replaceChildren();
    options.forEach(function(item){select.add(new Option(item[1],item[0]));});
    select.dataset.group=group;
  }
  var escapeText = global.EducationTransferHtmlEscape.escapeHtml;
  function format(n) { return Number(n || 0).toLocaleString('el-GR', { maximumFractionDigits: 2 }); }
  // Clamp only official, direct point entries while typing/pasting. This prevents
  // an over-limit value from invalidating an otherwise valid score. Keep comma
  // decimals and intermediate input (e.g. '12,') as entered.
  function capPrimaryDirectPoints(el) {
    var max=Number(el.getAttribute('data-primary-points-cap'));
    if(!Number.isFinite(max) || max<=0)return;
    var raw=String(el.value).trim().replace(',', '.');
    if(/^\d+(?:\.\d*)?$/.test(raw) && Number(raw)>max)el.value=String(max);
  }
  function action(id,title,desc) { return {id:id, title:title,desc:desc,channel:'email',action:true}; }
  function info(id,title,desc) { return {id:id, title:title,desc:desc,channel:'check',action:false}; }
  function mydocs(id,title,desc) { return {id:id, title:title,desc:desc,channel:'mydocs',action:true}; }
  // Shared social-criteria UI; grade-specific evidence and eligibility remain here.
  var spouseRules = {
    unspecified:{summary:'Επιλέξτε καθεστώς εργασίας για έλεγχο των +4 μορίων.',review:true},
    public_organic:{summary:'Δημόσιος υπάλληλος με οργανική θέση: δεν απαιτείται διετία.'},
    teacher_organic:{summary:'Μόνιμος εκπαιδευτικός: λαμβάνεται υπόψη η οργανική θέση, όχι η απόσπαση.'},
    teacher_disposal:{summary:'Στη διάθεση ΠΥΣΠΕ/ΠΥΣΔΕ: μόνο για μετάθεση από περιοχή σε περιοχή.'},
    private_employee:{summary:'Ιδιωτικός υπάλληλος: συμπληρωμένη διετία και ασφαλιστικά στοιχεία.'},
    self_employed:{summary:'Ελεύθερος επαγγελματίας: διετία, ασφαλιστική βεβαίωση και έδρα.'},
    remote_employee:{summary:'Τηλεργασία: αποκλειστική, με έδρα τη ζητούμενη περιοχή και δήλωση στην ΕΡΓΑΝΗ.'},
    lawyer:{summary:'Δικηγόρος: δεν απαιτείται διετής υπηρεσία.'},
    private_doctor:{summary:'Ιατρός εκτός Δημοσίου: απαιτείται τεκμηρίωση ιδιωτικού τομέα και διετία.'},
    substitute_teacher:{summary:'Σύζυγος αναπληρωτής: δεν χορηγούνται μονάδες συνυπηρέτησης.',blocked:true},
    public_contract:{summary:'Εργαζόμενος Δημοσίου χωρίς οργανική, με σύμβαση: δεν χορηγούνται +4.',blocked:true},
    other:{summary:'Σύνθετη περίπτωση: απαιτείται επαλήθευση από τη ΔΠΕ.',review:true}
  };
  function syncPrimaryCoService(){
    var check=$('primaryCoservice'), choice=$('primaryCoServiceCase');
    if(!check || !choice)return false;
    var data=spouseRules[choice.value]||spouseRules.unspecified;
    // A spouse merely placed at the disposal of a service has no established
    // organic municipality for the KEDASY-specific municipal criterion.
    var teacherAtDisposal=choice.value==='teacher_disposal';
    var localPlacement=isPlacement() || isMinorityWithin();
    var municipalDisposal=teacherAtDisposal && $('primaryMode').value==='kedasy';
    // Circular 138023/E2, Part III, Chapter B, clarification 9:
    // being at the disposal of a PYSPE/PYSDE does not provide co-service points
    // for improvement or final placement; only area-to-area transfers qualify.
    if(teacherAtDisposal && localPlacement)data={summary:'Στην οριστική τοποθέτηση ή βελτίωση δεν αποδίδονται μόρια συνυπηρέτησης όταν ο/η σύζυγος βρίσκεται στη διάθεση ΠΥΣΠΕ/ΠΥΣΔΕ. Η εξαίρεση αφορά μετάθεση από περιοχή σε περιοχή (εγκύκλιος Π.Ε., Μέρος Γ΄ Κεφ. Β΄, διευκρίνιση 9).',blocked:true};
    else if(municipalDisposal)data={summary:'Στα ΚΕΔΑΣΥ η συνυπηρέτηση εξετάζεται στον δήμο του ΚΕΔΑΣΥ. Η διάθεση σε ΠΥΣΠΕ/ΠΥΣΔΕ δεν τεκμηριώνει οργανική θέση στον συγκεκριμένο δήμο.',blocked:true};
    var blocked=!!data.blocked, review=!!data.review;
    if(blocked)check.checked=false;
    check.disabled=blocked;
    var field=$('primaryCoServiceCaseField');
    if(global.EducationTransferSocialInput && global.EducationTransferSocialInput.syncCoServiceField)
      global.EducationTransferSocialInput.syncCoServiceField(check, field, choice);
    else {field.style.display=check.checked || choice.value!=='unspecified'?'block':'none';}
    var note=$('primaryCoServiceAdvice');
    if(note)note.textContent=(check.checked || choice.value!=='unspecified')?(data.summary + (review?' Τα +4 μόρια δεν προστίθενται μέχρι επαλήθευσης.':'')):'';
    check.setAttribute('aria-describedby',blocked||review?'primaryCoServiceAdvice':'');
    var row=check.closest && check.closest('.check-row');
    if(row)row.classList.toggle('transfer-criterion-unavailable',blocked);
    return blocked || review;
  }
  function isPlacement(){return $('primaryMode').value==='placement-general'||$('primaryMode').value==='placement-eae';}
  function isMinority(){return $('primaryMode').value==='minority';}
  function isMinorityWithin(){return isMinority() && $('primaryMinorityRoute').value==='within';}
  function updateReturnFromEae(){
    var active=$('primaryMode').value==='region',panel=$('primaryReturnFromEae'),path=$('primaryReturnPath').value;
    panel.hidden=!active;
    $('primaryReturnYearsField').hidden=!active||path==='none';
    $('primaryReturnAreaYearsField').hidden=!active||path!=='appointed';
    if(!active||path==='none'){$('primaryReturnStatus').textContent='';return;}
    var yRaw=$('primaryReturnYears').value.trim(),aRaw=$('primaryReturnAreaYears').value.trim();
    var y=yRaw===''?NaN:Number(yRaw), a=aRaw===''?NaN:Number(aRaw);
    var yrOK=Number.isFinite(y)&&y>=0&&y<=50;
    var organic=$('primaryOrganic').value;
    var status='';
    if(path==='transferred'){
      status=!yrOK?'ⓘ Συμπληρώστε τα έτη ΕΑΕ από την προηγούμενη μετάθεση.':
        y<2?'⚠ Δεν έχει συμπληρωθεί η προβλεπόμενη διετής διδακτική υπηρεσία από τη μετάθεση σε ΕΑΕ.':
        organic!=='yes'?'ⓘ Η διετία καλύπτεται, αλλά πρέπει να επιβεβαιωθεί η υπηρέτηση οργανικής θέσης.':
        '✓ Θετική ένδειξη διετίας και υπηρεσίας οργανικής, με τελικό έλεγχο από τη ΔΠΕ.';
    }else{
      var areaOK=Number.isFinite(a)&&a>=0&&a<=50&&(!yrOK||a<=y);
      status=!yrOK||!areaOK?'ⓘ Συμπληρώστε έγκυρα συνολικά έτη ΕΑΕ και έτη στην περιοχή διορισμού.':
        y<5||a<2?'⚠ Δεν συμπληρώνονται η πενταετία ΕΑΕ και η διετία περιοχής διορισμού.':
        '✓ Θετική ένδειξη πενταετίας και διετίας περιοχής διορισμού, με τελικό έλεγχο από τη ΔΠΕ.';
    }
    $('primaryReturnStatus').textContent=status;
  }
  function updateEaeRoute(){
    var mode=primaryEffectiveMode();
    var active=['eae','eeeek','deaf','blind','kedasy','placement-eae'].indexOf(mode)!==-1;
    var section=$('primaryEaeRouteFields'),status=$('primaryEaeRouteStatus');
    section.hidden=!active;
    if(!active)return;
    if(!eaeRoutes || typeof eaeRoutes.check!=='function'){
      status.textContent='Δεν φορτώθηκαν οι ειδικές οδηγίες διαδρομών ΕΑΕ. Ελέγξτε την εγκύκλιο ή επαναφορτώστε τη σελίδα.';
      status.setAttribute('data-status','review');return;
    }
    var result=eaeRoutes.check({mode:mode,route:$('primaryEaeMoveRoute').value,
      disability:$('primaryEaeDisability80').value});
    status.setAttribute('data-status',result.status);
    status.replaceChildren();
    result.messages.forEach(function(message){var line=document.createElement('p');line.textContent=message;status.appendChild(line);});
  }
  function buildDocuments() {
    var mode=primaryEffectiveMode();
    var docs=mode==='intercultural'?[]:[info('registry','Υπηρεσιακό μητρώο και μονάδες','Έλεγχος στο επίσημο μητρώο — χωρίς αποστολή')];
    if(['eae','eeeek','deaf','blind','kedasy','placement-eae'].indexOf(mode)!==-1){
      var route=$('primaryEaeMoveRoute').value,disability=$('primaryEaeDisability80').value;
      if(route!=='unknown' && eaeRoutes){
        var routing=eaeRoutes.check({mode:mode,route:route,disability:disability});
        docs.push(info('eae-route-procedure','Διαδρομή αίτησης ΕΑΕ',routing.messages[0]||'Ελέγξτε τον προβλεπόμενο τύπο αίτησης.'));
      }
      if(disability==='lifetime80'){
        docs.push(info('eae-disability80-exception','Αναπηρία ιδίου ≥80% εφ’ όρου ζωής — ειδική διαδικασία',
          mode==='kedasy'?'Η κατ’ εξαίρεση μετάθεση δεν εφαρμόζεται στα ΚΕΔΑΣΥ.':
          mode==='placement-eae'?'Η βελτίωση/τοποθέτηση εξετάζεται με τα κριτήρια ειδικής κατηγορίας στις διαθέσιμες κενές θέσεις.':
          'Η κατ’ εξαίρεση μετάθεση αφορά περιοχές ΕΑΕ, με επίσημη πιστοποίηση και τις προβλεπόμενες διαδικασίες. Δεν τεκμαίρεται από την επιλογή.'));
      }
    }
    if(isMinority()){
      var procedure=isMinorityWithin()?'βελτίωση εντός νομού':'μετάθεση μεταξύ περιοχών Θράκης';
      docs.push(info('minority-procedure','ΠΕ73 — '+procedure,
        'Ειδικός προέλεγχος του μειονοτικού προγράμματος. '+(isMinorityWithin()?'Για βελτίωση εντός νομού αρμόδιο είναι το οικείο ΠΥΣΠΕ.':'Για μετάθεση από νομό σε νομό αρμόδιο είναι το ΑΠΥΣΠΕ Ανατολικής Μακεδονίας–Θράκης.')+' Δεν τεκμαίρεται οργανικό κενό ή κωδικός στο ηλεκτρονικό σύστημα.'));
    }
    if(isPlacement())docs.push(info('placement-procedure','Οριστική τοποθέτηση / βελτίωση — στάδια',
      'Αρχική ηλεκτρονική αίτηση έως 02/11/2026 χωρίς προτιμήσεις σχολείων. Η τοπική ΔΠΕ θα ανακοινώσει μεταγενέστερα τα οργανικά κενά και τις προτιμήσεις· ενημερωτικός προέλεγχος, όχι πιστοποίηση δικαιώματος.'));
    if($('primaryMode').value==='region' && $('primaryReturnPath').value!=='none')docs.push(info('return-eae-general','Επιστροφή από ΕΑΕ στη Γενική',
      'Η αίτηση παραμένει από περιοχή σε περιοχή, ακόμη και αν ο προορισμός είναι σχολείο Γενικής της ίδιας περιοχής. Δείτε τον ειδικό προέλεγχο διετίας ή πενταετίας και την υπηρέτηση οργανικής.'));
    if (mode!=='intercultural' && (Number($('primaryChildren').value)>0 || $('primaryFamily').checked)) {
      docs.push(info('family','Πιστοποιητικό οικογενειακής κατάστασης','Αυτεπάγγελτα από ΔΠΕ — δεν το προσκομίζετε'));
    }
    if(mode!=='intercultural' && Number($('primaryChildren').value)>0) {
      docs.push(info('children-guide','Τέκνα — πότε απαιτούνται επιπλέον αποδεικτικά',
        global.EducationTransferChildrenDocuments ? global.EducationTransferChildrenDocuments.summary() :
        'Ελέγξτε τις ειδικές περιπτώσεις σπουδών, επιμέλειας, πολυτεκνίας και νεογέννητου σύμφωνα με την εγκύκλιο.'));
    }
    if(mode!=='intercultural' && $('primaryChildrenExtra').checked && Number($('primaryChildren').value)>0) docs.push(action('children','Προετοιμασία πρόσθετου αποδεικτικού τέκνου','Email στη ΔΠΕ οργανικής μόνο όπου απαιτείται ειδικό αποδεικτικό (π.χ. φοίτηση ή επιμέλεια) — δείτε τις περιπτώσεις στον οδηγό τέκνων.'));
    if(mode!=='intercultural'){
      var selectedCoService=$('primaryCoServiceCase').value;
      var restrictedCoService=syncPrimaryCoService();
      if($('primaryCoservice').checked && !restrictedCoService){
        docs.push(action('coservice','Δικαιολογητικά συνυπηρέτησης','Αποστολή με email στη ΔΠΕ οργανικής — όπου απαιτούνται αποδεικτικά. '+(spouseRules[selectedCoService]||spouseRules.unspecified).summary));
      } else if(selectedCoService!=='unspecified' && restrictedCoService){
        docs.push(info('coservice','Συνυπηρέτηση — διευκρινίσεις χωρίς αποστολή δικαιολογητικών','Πληροφοριακός έλεγχος: '+$('primaryCoServiceAdvice').textContent));
      }
    }
    if((mode==='region' || (mode==='minority' && !isMinorityWithin())) && $('primarySpecialCase') && $('primarySpecialCase').value!=='none') {
      var special=$('primarySpecialCase').value;
      if(special==='health_committee') docs.push(info('health-committee','Ειδική κατηγορία υγείας — γνωμάτευση υγειονομικής επιτροπής','Κλειστός φυσικός φάκελος προς ΥΠΑΙΘΑ με ένδειξη εμπιστευτικού πρωτοκόλλου· δεν αποστέλλεται με κοινό email. Εγκύκλιος Π.Ε. §2.2.3.'));
      if(special==='health_kepa') docs.push(info('health-kepa','Ειδική κατηγορία υγείας — ΚΕΠΑ','Καταχώριση Αριθμού Επιτροπής και Αριθμού Μητρώου ΚΕΠΑ στο επίσημο σύστημα, χωρίς αποστολή γνωμάτευσης. Εγκύκλιος Π.Ε. §2.2.3.'));
      if(special==='judicial_spouse') docs.push(mydocs('judicial-spouse','Σύζυγος δικαστικού λειτουργού — κατά προτεραιότητα','Μέσω mydocs: βεβαίωση εργασίας και έγγραφο περιφέρειας αρμοδιότητας του δικαστηρίου. Πιστοποιητικό οικογενειακής κατάστασης αυτεπάγγελτα. Εγκύκλιος Π.Ε. §2.2.4.'));
    }
    if(mode!=='intercultural' && $('primaryLocality').checked) docs.push(info('locality','Εντοπιότητα',global.EducationTransferLocalityResources ? global.EducationTransferLocalityResources.summary('primary') : 'Ελέγξτε τη διετία δημοτικότητας και τα επίσημα στοιχεία σας.'));
    if(mode==='intercultural'){
      var existing=$('primaryInterDossier').value;
      var chosenQuals=interQualifications();
      if(existing==='unknown' && chosenQuals.length)docs.push(info('inter-dossier-review','Προηγούμενος φάκελος Διαπολιτισμικών','Ελέγξτε εάν απαιτείται νέα ή συμπληρωματική υποβολή.'));
      if(existing==='new')chosenQuals.forEach(function(k){docs.push(mydocs('inter-'+k,intercultural.qualificationNames[k],'Υποβολή μέσω mydocs — μόνο για νέα ή συμπληρωματικά αποδεικτικά προσόντων.'));});
      if($('primaryInterInclusion').value==='yes' && $('primaryInterEaeQualification').value==='yes')docs.push(info('inter-inclusion-eae','Προσόντα ΕΑΕ για Τμήμα Ένταξης','Επαληθεύστε εάν ο σχετικός φάκελος ΕΑΕ απαιτεί συμπλήρωση.'));
      return docs;
    }
    if(mode!=='region' && mode!=='minority' && !isPlacement()) {
      var origin=$('primaryEaeOrigin').value;
      var dossier=$('primaryEaeDossier').value;
      var options=[['phd','primaryEaePhd'],['masters','primaryEaeMasters'],['retraining','primaryEaeRetraining'],['experience','primaryEaeExperience'],['pe11','primaryEaePe11']];
      var chosen=options.filter(function(pair){return $(pair[1]).checked;});
      var labels={phd:'Διδακτορικός τίτλος ΕΑΕ και τεκμηρίωση συνάφειας',masters:'Μεταπτυχιακός τίτλος ΕΑΕ και τεκμηρίωση συνάφειας',retraining:'Πτυχίο διετούς μετεκπαίδευσης ΕΑΕ',pe11:'Πτυχίο ΤΕΦΑΑ με κύρια ειδικότητα ΕΑΕ'};
      if(origin==='appointed-eae') {
        docs.push(info('appointed-eae','Διορισμός στην ΕΑΕ — υπηρεσιακή διετία','Έλεγχος από ΔΠΕ / υπηρεσιακό φάκελο — όχι αυτοτελής αποστολή'));
      }
      if(origin==='general' || origin==='transferred-eae') {
        if(chosen.length && dossier==='unknown') {
          docs.push(info('eae-dossier','Προηγούμενος φάκελος προσόντων ΕΑΕ','Ελέγξτε αν υπάρχει φάκελος μετάθεσης 2025–26 ή απόσπασης ΕΑΕ 2026· επανυποβάλλονται μόνο νέα/συμπληρωματικά στοιχεία.'));
        }
        if(chosen.length && dossier==='previous') {
          docs.push(info('eae-previous-dossier','Υφιστάμενος φάκελος προσόντων ΕΑΕ','Δεν απαιτείται νέα υποβολή ήδη κατατεθειμένων δικαιολογητικών χωρίς νέα στοιχεία.'));
        }
        chosen.forEach(function(pair){
          var key=pair[0];
          if(key==='experience'){
            docs.push(info('eae-experience','Πενταετής διδακτική υπηρεσία ΕΑΕ','Διαπίστωση υπηρεσίας από το αρμόδιο τμήμα του ΥΠΑΙΘΑ βάσει αποδεικτικών στοιχείων· όχι αυτόματη αποστολή με email.'));
            return;
          }
          if(dossier==='new') {
            var note= key==='phd'?'Για μη ήδη αναγνωρισμένη συνάφεια ενδέχεται να χρειάζεται περίληψη διατριβής.':key==='masters'?'Για μη ήδη αναγνωρισμένη συνάφεια ενδέχεται να απαιτείται αναλυτικό πρόγραμμα και περίληψη εργασίας.':'Ελέγξτε τυχόν απαιτούμενη αναγνώριση ξένου τίτλου.';
            docs.push(mydocs('eae-'+key, labels[key], 'Υποβολή μέσω mydocs — πρώτη υποβολή ή νέα/συμπληρωματικά στοιχεία. '+note));
          }
        });
      }
      if(mode==='deaf' || mode==='blind') {
        var certificate = $('primaryEaeCertification').value;
        var certificateDossier = $('primaryEaeCertificationDossier').value;
        var certName = mode==='deaf'?'Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)':'γραφής Braille';
        if(certificate==='yes' && certificateDossier==='new') {
          docs.push(mydocs('eae-certificate-'+mode, 'Αποδεικτικό πιστοποιημένης γνώσης '+certName, 'Υποβολή μέσω mydocs — μόνο αν απαιτείται νέος ή συμπληρωματικός φάκελος.'));
        } else if(certificate==='yes' && certificateDossier==='unknown') {
          docs.push(info('eae-cert-check-'+mode, 'Αποδεικτικό γνώσης '+certName, 'Ελέγξτε αν το έγκυρο αποδεικτικό έχει ήδη υποβληθεί· όχι αυτόματη επανυποβολή.'));
        }
      }
      docs.push(info('eae-review','Υπηρεσιακή επαλήθευση ΕΑΕ / ΚΕΔΑΣΥ','Οι προϋποθέσεις και τα πραγματικά οργανικά κενά ελέγχονται από τα αρμόδια υπηρεσιακά όργανα.'));
    }
    return docs;
  }
  // The two grades share the same disclosure/card visual primitives. Their
  // evidence, channel and submission rules remain grade-specific.
  var primaryOpenDetails = {};
  function updateCheckList(docs) {
    var checklist=$('primaryChecklist');
    var counts=checklistState.reconcile(docs,function(d){return d.action;});
    checklist.innerHTML=docs.map(function(d){
      var id=escapeText(d.id),title=escapeText(d.title);
      var extra='';
      if(d.id==='registry') {
        extra='<p>Ελέγξτε την αναγνωρισμένη συνολική υπηρεσία, τις καταχωρισμένες υπηρετήσεις και τις μονάδες συνθηκών διαβίωσης στο επίσημο Μητρώο πριν οριστικοποιήσετε την αίτηση.</p>'+
          '<p><a class="edu-transfer-doc__portal" href="https://teachers.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">Άνοιγμα teachers.minedu.gov.gr ↗</a></p>';
      } else if(d.id==='family') {
        extra='<p>Το πιστοποιητικό οικογενειακής κατάστασης αναζητείται αυτεπαγγέλτως από την αρμόδια ΔΠΕ, σύμφωνα με την εγκύκλιο Πρωτοβάθμιας. Δεν απαιτείται να το στείλετε αυτοτελώς, εκτός εάν σας ζητηθεί ειδικά.</p>';
      } else if(d.id==='coservice' && global.EducationTransferCoServiceResources) {
        var resources=global.EducationTransferCoServiceResources,choice=$('primaryCoServiceCase').value;
        var legal=resources.legalDetail ? resources.legalDetail(choice) : '';
        var practical=resources.render(choice);
        if(legal || practical)extra=(legal?'<p>'+escapeText(legal)+'</p><p>'+escapeText(resources.evidenceRecency())+'</p><p class="edu-transfer-doc__source">'+escapeText(resources.legalSource('primary',choice))+'</p>':'')+practical;
      } else if(d.id==='locality' && global.EducationTransferLocalityResources) {
        extra='<p>'+escapeText(global.EducationTransferLocalityResources.detail('primary')).replace(/\n/g,'<br>')+'</p><p class="edu-transfer-doc__source">Πηγή: '+escapeText(global.EducationTransferLocalityResources.source('primary'))+'</p>';
      } else if(d.id==='children-guide' && global.EducationTransferChildrenDocuments) {
        extra=global.EducationTransferChildrenDocuments.render('primary');
      }
      var detailHtml=extra?'<details class="edu-transfer-doc__disclosure" data-primary-details="'+id+'"'+(primaryOpenDetails[d.id]?' open':'')+'><summary>Περισσότερες πληροφορίες</summary><div class="edu-transfer-doc__body">'+extra+'</div></details>':'';
      return '<article class="primary-doc-row edu-transfer-doc'+(!d.action?' is-informational':'')+'" data-primary-doc="'+id+'">'+
        '<div class="edu-transfer-doc__head">'+
          (d.action?'<input class="edu-transfer-doc__check" type="checkbox" id="primaryDocCheck-'+id+'" data-primary-check="'+id+'"'+(checklistState.isComplete(d.id)?' checked':'')+' aria-label="Ετοιμάστηκε: '+title+'">':'<span class="edu-transfer-doc__info-icon" aria-hidden="true">ⓘ</span>')+
          '<div class="edu-transfer-doc__main">'+
            (d.action?'<label class="edu-transfer-doc__title" for="primaryDocCheck-'+id+'">'+title+'</label>':'<div class="edu-transfer-doc__title">'+title+'</div>')+
            '<p class="edu-transfer-doc__channel">'+escapeText(d.desc)+'</p>'+detailHtml+
          '</div></div></article>';
    }).join('');
    checklist.querySelectorAll('details[data-primary-details]').forEach(function(details){
      details.addEventListener('toggle',function(){primaryOpenDetails[details.getAttribute('data-primary-details')]=details.open;});
    });
    $('primaryChecklistProgress').textContent=checklistState.progressText(counts,'primary');
    updateMail(docs);
  }

  function numeric(el,max){
    var raw=el.value;
    if(raw==='')return 0;
    var n=Number(raw);
    if(!Number.isFinite(n)|| n<0 || n>max || !Number.isInteger(n))return null;
    return n;
  }
  function interQualifications(){return Array.from(document.querySelectorAll('[data-primary-inter-qual]:checked')).map(function(el){return el.getAttribute('data-primary-inter-qual');});}
  function qualifications(){
    return {phd:$('primaryEaePhd').checked,masters:$('primaryEaeMasters').checked,
      retraining:$('primaryEaeRetraining').checked,experience:$('primaryEaeExperience').checked,pe11:$('primaryEaePe11').checked};
  }
  function updateEaePrecheck(){
    updateEaeRoute();
    var mode=primaryEffectiveMode();
    $('primaryEaeFields').hidden=mode==='region'||mode==='minority'||mode==='intercultural'||isPlacement();
    $('primaryGeneralOrganicField').hidden=mode!=='region' && mode!=='minority';
    $('primaryEligibility').hidden=mode!=='region' && mode!=='minority';
    $('primaryKedasyGeoNote').hidden=mode!=='kedasy';
    var isCertificate=mode==='deaf'||mode==='blind';
    $('primaryEaeCertificationField').hidden=!isCertificate;
    $('primaryEaeCertificationDossierField').hidden=!isCertificate || $('primaryEaeCertification').value!=='yes';
    $('primaryEaeCertificationLabel').textContent=mode==='deaf'?'Πιστοποιημένη γνώση Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)':'Πιστοποιημένη γνώση γραφής Braille';
    if(mode==='region'||mode==='minority'||mode==='intercultural'||isPlacement())return;
    var origin=$('primaryEaeOrigin').value;
    $('primaryEaeGeneralYearsField').hidden=origin!=='general';
    $('primaryEaeAppointmentYearsField').hidden=origin!=='appointed-eae';
    $('primaryEaeYearsField').hidden=!$('primaryEaeExperience').checked;
    $('primaryPreviousDossierField').hidden=origin==='appointed-eae';
    var data={mode:mode,branch:$('primaryEaeBranch').value,origin:origin,
      organic:$('primaryEaeOrganic').value,generalYears:$('primaryEaeGeneralYears').value,
      appointmentAreaYears:$('primaryEaeAppointmentYears').value,eaeYears:$('primaryEaeYears').value,
      qualifications:qualifications(),certification:$('primaryEaeCertification').value,
      exception80:$('primaryEaeDisability80').value==='lifetime80' && mode!=='kedasy'};
    var outcome=eae.evaluate(data);
    var labels={problem:'⚠ Δεν πληρούται ελεγχόμενη προϋπόθεση',review:'ⓘ Απαιτούνται στοιχεία ή υπηρεσιακή επαλήθευση',positive:'✓ Θετικός προκαταρκτικός έλεγχος'};
    $('primaryEaeEligibility').setAttribute('data-status',outcome.status);
    var list=outcome.problems.concat(outcome.checks);
    if(!list.length)list=outcome.positive;
    $('primaryEaeEligibility').innerHTML='<strong>'+escapeText(labels[outcome.status])+'</strong>'+
      (list.length?'<ul>'+list.map(function(x){return '<li>'+escapeText(x)+'</li>';}).join('')+'</ul>':'');
  }
  function updateInterculturalVacancies(){
    var branchSelect=$('primaryInterVacancyBranch');
    var schoolSelect=$('primaryInterVacancySchool');
    if(!branchSelect.dataset.initialized){
      var names=global.EducationTransferPrimarySpecialtyLabels||{};
      interVacancies.branches().forEach(function(item){
        var opt=document.createElement('option');opt.value=item.code;
        var title=names[item.code] || (item.code==='ΠΕ70'?'Δάσκαλοι':item.code==='ΠΕ71'?'Δάσκαλοι ΕΑΕ':'');
        opt.textContent=item.code+(title?' — '+title:'')+' ('+item.positions+(item.shared?' κοινά κενά ΠΕ70+ΠΕ71':' κενά')+')';
        branchSelect.appendChild(opt);
      });
      branchSelect.dataset.initialized='yes';
    }
    var branch=branchSelect.value;
    var entries=interVacancies.schoolsForBranch(branch);
    if(schoolSelect.dataset.branch!==branch){
      schoolSelect.replaceChildren(new Option('Επιλέξτε σχολείο',''));
      entries.forEach(function(item){schoolSelect.add(new Option(item.school+' ('+item.vacancies+' θέσ'+(item.vacancies===1?'η':'εις')+')',item.school));});
      schoolSelect.dataset.branch=branch;
    }
    $('primaryInterVacancySchoolField').hidden=!branch;
    var selected=entries.find(function(row){return row.school===schoolSelect.value;});
    var summary=$('primaryInterVacancySummary');
    if(!branch){summary.textContent='Επιλέξτε κλάδο και σχολείο από τις 16 καταγεγραμμένες οργανικές θέσεις Διαπολιτισμικών Π.Ε. 2026–2027.';return;}
    if(!entries.length){summary.textContent='Δεν καταγράφεται οργανικό κενό για τον επιλεγμένο κλάδο στον παρεχόμενο πίνακα.';return;}
    var total=entries.reduce(function(sum,row){return sum+row.vacancies;},0);
    var shared=branch==='ΠΕ70'||branch==='ΠΕ71';
    summary.textContent=selected?
      selected.school+' — '+selected.directorate+' · '+selected.vacancies+' '+(selected.vacancies===1?'οργανικό κενό':'οργανικά κενά')+
      ' · Γλώσσα σχολείου στον πίνακα: '+selected.language+(shared?' · Κοινή κατηγορία ΠΕ70+ΠΕ71.':''):
      entries.length+' σχολεία · '+total+(shared?' ΚΟΙΝΑ κενά για ΠΕ70+ΠΕ71 — όχι '+total+' ανά κλάδο.':' οργανικά κενά στον κλάδο.')+' Επιλέξτε σχολείο.';
  }
  function updateIntercultural(){
    var active=$('primaryMode').value==='intercultural';
    $('primaryInterculturalFields').hidden=!active;
    if(!active)return;
    updateInterculturalVacancies();
    $('primaryInterCurrentYearsField').hidden=$('primaryInterCurrent').value!=='yes';
    $('primaryInterEaeQualificationField').hidden=$('primaryInterInclusion').value!=='yes';
    var outcome=intercultural.evaluate({organic:$('primaryInterOrganic').value,
      current:$('primaryInterCurrent').value,currentYears:$('primaryInterCurrentYears').value,
      inclusion:$('primaryInterInclusion').value,eaeQualification:$('primaryInterEaeQualification').value,
      qualifications:interQualifications()});
    var label={problem:'⚠ Προϋπόθεση που δεν πληρούται',review:'ⓘ Απαιτείται επαλήθευση',positive:'✓ Θετικός προκαταρκτικός έλεγχος'};
    $('primaryInterStatus').setAttribute('data-status',outcome.status);
    $('primaryInterStatus').innerHTML='<strong>'+escapeText(label[outcome.status])+'</strong><ul>'+
      outcome.problems.concat(outcome.checks).map(function(x){return '<li>'+escapeText(x)+'</li>';}).join('')+'</ul>';
  }
  function updateMinority(){
    var active=isMinority(), panel=$('primaryMinorityFields');
    panel.hidden=!active;
    if(!active)return;
    var route=$('primaryMinorityRoute').value;
    $('primaryMinorityDestinationField').hidden=route==='within';
    $('primaryMinorityLocalityNote').hidden=route!=='within';
    if(!minority || !minority.evaluate){
      $('primaryMinorityStatus').textContent='Ο προέλεγχος ΠΕ73 δεν φορτώθηκε. Επαναφορτώστε τη σελίδα.';
      $('primaryMinorityStatus').dataset.status='review';
      return;
    }
    var outcome=minority.evaluate({branch:$('primaryMinorityBranch').value,route:route,
      origin:$('primaryMinorityOrigin').value,destination:$('primaryMinorityDestination').value,
      organic:$('primaryOrganic').value});
    $('primaryMinorityStatus').dataset.status=outcome.status;
    var lead=outcome.status==='problem'?'⚠ Χρειάζεται αλλαγή ή υπηρεσιακή διευκρίνιση':
      outcome.status==='positive'?'✓ Συμβατή βασική διαδρομή (ενδεικτικός προέλεγχος)':'ⓘ Χρειάζεται συμπλήρωση / επαλήθευση';
    $('primaryMinorityStatus').innerHTML='<strong>'+escapeText(lead)+'</strong><ul>'+
      outcome.issues.concat(outcome.notes).map(function(line){return '<li>'+escapeText(line)+'</li>';}).join('')+'</ul>';
  }
  function update() {
    syncPrimaryDestination();
    $('primaryPlacementNotice').hidden=!isPlacement();
    updateReturnFromEae();
    updateMinority();
    // Official application codes from the Primary Education application list.
    // EEEEK shares the SΜΚΔ application with SΜΕΑΕ; Deaf/Blind shares the language-specialization application.
    // This is a UI reference, NOT an alternate electronic filing path.
    var sectionError='';
    try {updateEaePrecheck();} catch(err) {
      sectionError='Ο έλεγχος Ειδικής Αγωγής δεν φορτώθηκε σωστά. Επαναφορτώστε τη σελίδα.';
    }
    try {updateIntercultural();} catch(err) {
      if($('primaryMode').value==='intercultural')
        sectionError='Ο έλεγχος Διαπολιτισμικών δεν φορτώθηκε σωστά. Επαναφορτώστε τη σελίδα.';
    }
    var interculturalMode=$('primaryMode').value==='intercultural';
    $('primaryScoringCard').hidden=interculturalMode;
    $('primarySocialCriteriaCard').hidden=interculturalMode;
    $('primaryInterculturalScoreNote').hidden=!interculturalMode;
    $('primaryScoreMobile').hidden=interculturalMode;
    $('primaryScoreResults').parentElement.hidden=interculturalMode;
    var special=$('primarySpecialCaseField');
    if(special){ var allowSpecial=$('primaryMode').value==='region'||(isMinority()&&!isMinorityWithin());
      special.style.display=allowSpecial?'':'none';
      if(!allowSpecial)$('primarySpecialCase').value='none'; }
    var firstRow=$('primaryFirstPreferenceRow');
    if(firstRow){ var hideFirst=interculturalMode||isPlacement()||isMinorityWithin(); firstRow.classList.toggle('hidden',hideFirst); if(hideFirst)$('primaryFirst').checked=false; }
    var specialityChoice=$('primarySpecialty');
    if(specialityChoice){
      if(isMinority()){
        if(!specialityChoice.disabled)specialityChoice.dataset.beforeMinority=specialityChoice.value;
        specialityChoice.value='ΠΕ73'; specialityChoice.disabled=true;
      }else if(specialityChoice.disabled){
        specialityChoice.disabled=false; specialityChoice.value=specialityChoice.dataset.beforeMinority||'';
        delete specialityChoice.dataset.beforeMinority;
      }
    }
    var invalidSpouse=syncPrimaryCoService();
    $('primaryChecklist').closest('section.card').querySelector('h2').textContent=(interculturalMode?'Β.':'Δ.')+' Προσωπική λίστα δικαιολογητικών';
    var mode=$('primaryServiceMode').value;
    var periods=global.EducationTransferPrimaryPeriods;
    if(periods)periods.sync();
    else { $('primaryServicePointsPanel').hidden=mode!=='points';$('primaryServiceDurationPanel').hidden=mode!=='direct'; }
    var children=numeric($('primaryChildren'),20),years=numeric($('primaryYears'),50),months=numeric($('primaryMonths'),11),days=numeric($('primaryDays'),29);
    var childField=$('primaryChildren');
    var childOutOfRange=children===null;
    childField.setAttribute('aria-invalid',String(childOutOfRange));
    if(children===0){$('primaryChildrenExtra').checked=false;$('primaryChildrenExtra').disabled=true;}
    else $('primaryChildrenExtra').disabled=false;
    var raw={mode:'region',serviceMode:mode,serviceDirectPoints:$('primaryServicePoints').value,
      serviceYears:years,serviceMonths:months,serviceDays:days,
      msdMode:$('primaryMsdMode').value,msdDirectPoints:$('primaryMsd').value,
      eligibleChildren:children===null?0:children,familyStatusEligible:$('primaryFamily').checked,
      coService:$('primaryCoservice').checked && !invalidSpouse,locality:$('primaryLocality').checked,firstPreference:$('primaryFirst').checked};
    var serviceMissing=mode==='points'&&!String(raw.serviceDirectPoints).trim();
    var msdMissing=raw.msdMode==='direct'&&!String(raw.msdDirectPoints).trim();
    var servicePeriods=null,msdPeriods=null;
    if(mode==='periods'&&periods){servicePeriods=periods.readService();raw.servicePeriods=servicePeriods.periods;}
    if(raw.msdMode==='periods'&&periods){msdPeriods=periods.readMsd();raw.msdPeriods=msdPeriods.periods;}
    var validSocial=children!==null;
    var validDuration=mode!=='direct'||(years!==null&&months!==null&&days!==null);
    // Invalid inputs never silently become zeros. Missing official totals remain unknown.
    var result=validSocial&&validDuration?scoring.calculate(raw):null;
    var serviceValid=!!result&&result.serviceInputValid&&!serviceMissing&&(!servicePeriods||servicePeriods.valid);
    var msdValid=!!result&&result.msdInputValid&&!msdMissing&&(!msdPeriods||msdPeriods.valid);
    var social=validSocial?scoring.calculate({mode:'region',serviceMode:'points',serviceDirectPoints:'0',
      msdMode:'direct',msdDirectPoints:'0',eligibleChildren:children,
      familyStatusEligible:raw.familyStatusEligible,coService:raw.coService,locality:raw.locality,
      firstPreference:raw.firstPreference}):null;
    var knownSocial=social?scoring.truncate2(social.coServicePoints+social.familyPoints+social.localityPoints+social.firstPreferencePoints):null;
    var complete=serviceValid&&msdValid;
    var sum=social?scoring.truncate2(knownSocial+(serviceValid?result.servicePoints:0)+(msdValid?result.msdPoints:0)):null;
    var scoreCard=global.EducationTransferScoreCard;
    {
      var f=function(v){return v===null||v===undefined?'—':format(v);};
      var msg=interculturalMode?'Δεν εφαρμόζεται αριθμητική κατάταξη για τη διαδικασία Διαπολιτισμικών.':
        !validSocial||!validDuration?'Διορθώστε τα μη έγκυρα αριθμητικά πεδία.':
        complete?'Ενδεικτική συνολική βαθμολογία. Επαληθεύστε κάθε υπηρεσία και τις επίσημες Μ.Σ.Δ. στο μητρώο.':
        'Μερικό άθροισμα μόνο από τα γνωστά κριτήρια. '+(!serviceValid?'Η προϋπηρεσία δεν έχει προσμετρηθεί. ':'')+(!msdValid?'Οι Μ.Σ.Δ. δεν έχουν προσμετρηθεί. ':'')+'Άγνωστα πεδία δεν λογίζονται ως μηδέν.';
      var label=complete?'συνολικές μονάδες':'γνωστές μονάδες (μερικό άθροισμα)';
      var scores={total:f(sum),label:label,
        service:serviceValid?f(result.servicePoints):'—',msd:msdValid?f(result.msdPoints):'—',
        coservice:social?f(social.coServicePoints):'—',family:social?f(social.familyPoints):'—',
        locality:social?f(social.localityPoints):'—',first:social?f(social.firstPreferencePoints):'—',status:msg};
      if(scoreCard && typeof scoreCard.render==='function')scoreCard.render('primary',scores);
      else { // The shared presentation asset is optional; never suppress a valid calculation.
        var ids={total:'primaryTotal',label:'primaryTotalLabel',service:'primaryServiceResult',
          msd:'primaryMsdResult',coservice:'primaryCoserviceResult',family:'primaryFamilyResult',
          locality:'primaryLocalityResult',first:'primaryFirstResult',status:'primaryTotalStatus'};
        Object.keys(ids).forEach(function(k){var node=$(ids[k]);if(node)node.textContent=scores[k];});
      }
      $('primaryMobileTotal').textContent=f(sum);
      $('primaryMobileLabel').textContent=label;
      $('primaryMobileStatus').textContent=msg;
    }
    $('primaryValidation').textContent=interculturalMode?'':!validSocial||!validDuration?
      'Μη έγκυρα αριθμητικά στοιχεία. Διορθώστε τα για να εμφανιστεί ο υπολογισμός.':
      complete?'':!serviceValid||!msdValid?'Η βαθμολογία είναι μερική: συμπληρώστε τις ελλείπουσες ή μη έγκυρες καταχωρίσεις.':'';
    var state=$('primaryOrganic').value;
    $('primaryEligibility').textContent=state==='yes'
      ? '✓ Δηλώθηκε συμπλήρωση έτους οργανικής. Ο οριστικός έλεγχος και οι ειδικές εξαιρέσεις ανήκουν στη ΔΠΕ.'
      : state==='no'
        ? '⚠ Η μη συμπλήρωση έτους οργανικής απαιτεί ειδικό υπηρεσιακό έλεγχο· ενδέχεται να ισχύουν νόμιμες εξαιρέσεις. Μην θεωρείτε την αίτηση αυτομάτως επιλέξιμη.'
        : ''; // No generic status panel before the user chooses an answer.
    // A dossier/composer fault must not erase an already calculated score.
    try {updateCheckList(buildDocuments());}catch(err){
      if(!sectionError)sectionError='Δεν φορτώθηκαν όλες οι οδηγίες δικαιολογητικών. Επαναφορτώστε τη σελίδα.';
    }
    var warning=$('primaryRuntimeWarning');
    if(warning){warning.hidden=!sectionError;warning.textContent=sectionError;}
    global.EducationTransferPrimaryReady=true;
  }
  // Section 2.2.1 (general) and 2.2.5 (EAE) prescribe distinct, exact
  // comma-separated subject/body field orders. The secondary composer uses
  // the same visual component, but PRIMARY legal wording is defined here.
  function messageParts(docs) {
    var actions=docs.filter(function(d){return d.action && d.channel==='email';});
    var general=$('primaryMode').value==='region'||$('primaryMode').value==='minority'||/^placement-/.test($('primaryMode').value);
    var name=$('primaryName').value.trim()||'[Ονοματεπώνυμο]';
    var branch=$('primaryMode').value==='minority'?'ΠΕ73':$('primarySpecialty').value.trim()||($('primaryMode').value!=='region'&&$('primaryEaeBranch').value)||'[Κλάδος/ειδικότητα]';
    var registry=$('primaryRegistry').value.trim()||'[Αριθμός μητρώου]';
    var protocol=$('primaryProtocol').value.trim()||'[Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης]';
    var dpe=$('primaryDpe').value.trim()||'[ΔΠΕ οργανικής]';
    var labels={
      region:'Μετάθεση από περιοχή σε περιοχή',
      'placement-general':'Βελτίωση / Οριστική τοποθέτηση Γενικής Εκπαίδευσης',
      'placement-eae':'Βελτίωση / Οριστική τοποθέτηση Ειδικής Αγωγής',
      eae:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      kedasy:'μετάθεση σε ΚΕΔΑΣΥ',
      eeeek:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      deaf:'μετάθεση με εξειδίκευση στην ΕΝΓ',
      blind:'μετάθεση με εξειδίκευση στη γραφή Braille',
      intercultural:'μετάθεση σε Διαπολιτισμικό Σχολείο'
    };
    var typ=labels[primaryEffectiveMode()]||labels.region;
    if($('primaryMode').value==='minority')typ=$('primaryMinorityRoute').value==='within'?'Βελτίωση θέσης ΠΕ73 μειονοτικού προγράμματος':'Μετάθεση ΠΕ73 μειονοτικού προγράμματος από περιοχή σε περιοχή';
    return {
      subject:general?[typ,protocol,registry].join(', '):['ΠΕ',typ,protocol,registry,branch].join(', '),
      body:general?[name,branch,typ,protocol,registry].join(', '):[name,branch,typ,protocol,registry,dpe].join(', '),
      actions:actions,general:general
    };
  }
  function uiText(key,defaultText){return global.EducationTransferEmailComponent&&global.EducationTransferEmailComponent.label(key)||defaultText;}
  function updateMail(docs){
    var email=messageParts(docs);
    var hasActions=email.actions.length>0;
    var mydocActions=docs.filter(function(d){return d.action && d.channel==='mydocs';});
    var isIntercultural=$('primaryMode').value==='intercultural';
    var isMinorityPath=$('primaryMode').value==='minority';
    var special=!email.general;
    var hasMydocs=mydocActions.length>0;
    var judicialMydocs=mydocActions.some(function(d){return d.id==='judicial-spouse';});
    $('primaryMydocsLink').hidden=!(special || judicialMydocs);
    $('primaryMydocsLink').innerHTML='<a href="https://mydocs.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">mydocs ΥΠΑΙΘΑ ↗</a> — '+(judicialMydocs?'ειδικά δικαιολογητικά συζύγων δικαστικών λειτουργών (§2.2.4).':'για προσόντα '+(isIntercultural?'Διαπολιτισμικών':'ΕΑΕ')+' όταν απαιτείται νέος/συμπληρωματικός φάκελος.');
    $('primaryMydocsNotice').hidden=!hasMydocs;
    $('primaryMydocsNotice').textContent=judicialMydocs?'Σύζυγοι δικαστικών λειτουργών: βεβαίωση εργασίας και έγγραφο περιφέρειας αρμοδιότητας δικαστηρίου υποβάλλονται μέσω mydocs (§2.2.4). Το πιστοποιητικό οικογενειακής κατάστασης αναζητείται αυτεπάγγελτα.':hasMydocs?'Έχετε '+mydocActions.length+' δικαιολογητικό/ά προσόντων '+(isIntercultural?'Διαπολιτισμικών':'ΕΑΕ')+' για υποβολή στο mydocs.minedu.gov.gr (όχι με email). Δεν επανυποβάλλονται ήδη κατατεθειμένα στοιχεία.':'';
    var sharedState=global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.stateCopy(hasActions&&hasMydocs?'mixed':hasActions?'email':hasMydocs?'mydocs':'optional'):null;
    $('primaryEmailHeading').textContent=judicialMydocs&&!hasActions?'Ειδική υποβολή μέσω mydocs — σύζυγοι δικαστικών λειτουργών':sharedState?sharedState.title:hasActions?'Προετοιμασία μηνύματος υποβολής δικαιολογητικών':hasMydocs?'Έτοιμο υπόδειγμα υποβολής μέσω mydocs':'Υπόδειγμα μηνύματος — αν χρειαστεί';
    $('primaryEmailLead').textContent=judicialMydocs?(hasActions?'Τα συνήθη αποδεικτικά μοριοδότησης αποστέλλονται με email στη ΔΠΕ οργανικής. Τα ειδικά δικαιολογητικά συζύγων δικαστικών λειτουργών υποβάλλονται χωριστά μέσω mydocs, σύμφωνα με τη §2.2.4.':'Τα ειδικά δικαιολογητικά συζύγων δικαστικών λειτουργών υποβάλλονται μέσω mydocs (§2.2.4), χωρίς να ορίζεται υποχρεωτικό θέμα ή σώμα μηνύματος.'):isIntercultural?'Τα προσόντα Διαπολιτισμικών υποβάλλονται μέσω mydocs, όταν απαιτείται νέος/συμπληρωματικός φάκελος. Δεν αποστέλλονται δικαιολογητικά μοριοδότησης.':hasActions?'Δημιουργήστε θέμα και σώμα μηνύματος για τα δικαιολογητικά μοριοδότησης που αποστέλλονται στη ΔΠΕ οργανικής.':hasMydocs?'Τα προσόντα ΕΑΕ υποβάλλονται μέσω mydocs όταν απαιτείται νέος ή συμπληρωματικός φάκελος — όχι με email.':'Δεν προκύπτει αυτή τη στιγμή δικαιολογητικό για αποστολή με email. Το υπόδειγμα παραμένει διαθέσιμο για προεπισκόπηση.';
    $('primaryEmailOpen').textContent=judicialMydocs&&!hasActions?'Προβολή οδηγιών υποβολής μέσω mydocs':sharedState?sharedState.button:hasActions?'Δημιουργία email υποβολής':hasMydocs?'Προετοιμασία κειμένου για mydocs':'Προβολή προαιρετικού υποδείγματος';
    $('primarySubmissionInstruction').textContent=isMinorityPath?'Μειονοτικό πρόγραμμα ΠΕ73: η αίτηση υποβάλλεται στο επίσημο teachers.minedu.gov.gr και τυχόν αποδεικτικά γενικής μοριοδότησης αποστέλλονται στη ΔΠΕ οργανικής όπου απαιτείται. Αρμόδιο συμβούλιο: '+(isMinorityWithin()?'ΠΥΣΠΕ (βελτίωση εντός νομού).':$('primaryMinorityDestination').value===$('primaryMinorityOrigin').value?'απαιτείται επιβεβαίωση από τη ΔΠΕ για περιοχές του ίδιου νομού.':'ΑΠΥΣΠΕ Ανατολικής Μακεδονίας–Θράκης (από νομό σε νομό).'):judicialMydocs?'Κατά προτεραιότητα μετάθεση συζύγου δικαστικού λειτουργού: ειδικά δικαιολογητικά μόνο μέσω mydocs (§2.2.4). Τυχόν άλλα αποδεικτικά γενικής μοριοδότησης αποστέλλονται χωριστά με email στη ΔΠΕ (§2.2.1), όπου απαιτούνται.':isIntercultural?'Διαπολιτισμικά: μόνο τα δικαιολογητικά προσόντων μέσω mydocs, όταν απαιτείται νέος/συμπληρωματικός φάκελος· όχι μοριοδότηση και όχι email.':email.general?/^placement-/.test($('primaryMode').value)?'Βελτίωση/οριστική τοποθέτηση: δικαιολογητικά που απαιτούν αποστολή υποβάλλονται με email στη ΔΠΕ οργανικής. Οι σχολικές προτιμήσεις θα δηλωθούν μεταγενέστερα.':'Γενική μετάθεση: δικαιολογητικά μέσω email στη ΔΠΕ οργανικής, εφόσον απαιτούνται.':'ΕΑΕ: τα αποδεικτικά μοριοδότησης αποστέλλονται με email στη ΔΠΕ οργανικής, ενώ τα αποδεικτικά προσόντων υποβάλλονται στο mydocs μόνο όπου απαιτείται νέα/συμπληρωματική υποβολή.';
    $('primaryTemplateNote').textContent=isMinorityPath?'Ενδεικτικό θέμα/σώμα για τυχόν email δικαιολογητικών προς ΔΠΕ με τη σειρά της §2.2.1. Το Κεφ. Ε΄ δεν προβλέπει ειδικό κωδικό αίτησης: χρησιμοποιήστε τον ακριβή τύπο από το επίσημο σύστημα.':judicialMydocs?'Η §2.2.4 δεν ορίζει υποχρεωτικό θέμα ή σώμα για το mydocs. Παρακάτω προβάλλεται μόνο το γενικό υπόδειγμα της §2.2.1, για τυχόν ξεχωριστό email με δικαιολογητικά μοριοδότησης προς τη ΔΠΕ· δεν αποτελεί υποχρεωτική μορφή υποβολής στο mydocs.':isIntercultural?'Το θέμα και το σώμα ακολουθούν τη σειρά της §2.2.5 για υποβολή μέσω mydocs.':email.general?/^placement-/.test($('primaryMode').value)?'Ενδεικτικό θέμα/σώμα για τυχόν email προς ΔΠΕ με βάση τη δομή της §2.2.1· ελέγξτε την εκάστοτε τοπική οδηγία για τη βελτίωση ή οριστική τοποθέτηση.':'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.1.':'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.5. Στο mydocs το σώμα μπορεί να χρησιμοποιηθεί ως κείμενο «Αιτούμαι».';
    $('primaryEmailSource').textContent=isMinorityPath?'Πηγή: εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026, Μέρος Γ΄ Κεφ. Ε΄ (ΠΕ73) και Μέρος Α΄ §2.2.1 (γενικό υπόδειγμα email). Η μορφή του ηλεκτρονικού τύπου αίτησης επαληθεύεται στο teachers.minedu.gov.gr.':judicialMydocs?'Πηγές: εγκύκλιος Π.Ε. 138023/Ε2/15-10-2026, §2.2.4 (ειδική υποβολή mydocs, χωρίς υποχρεωτικό πρότυπο θέματος/σώματος) και §2.2.1 (γενικό υπόδειγμα email μόνο για τυχόν άλλα δικαιολογητικά προς ΔΠΕ).':'Πηγή: εγκύκλιος μεταθέσεων Π.Ε. 138023/Ε2/15-10-2026, §'+(email.general?'2.2.1':'2.2.5')+'. Το υπόδειγμα δεν αντικαθιστά ειδικές διαδικασίες ή προηγούμενο φάκελο.';
    $('primaryDpeField').hidden=email.general;
    $('primaryEmailSubject').textContent=email.subject;
    $('primaryEmailBody').textContent=email.body;
    $('primaryMailAction').hidden=!hasActions || isIntercultural;
    $('primaryOpenMail').href=hasActions&&!isIntercultural?(global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.mailto(email):'mailto:?subject='+encodeURIComponent(email.subject)+'&body='+encodeURIComponent(email.body)):'#';
  }
  function legacyCopy(text){
    var node=document.createElement('textarea');node.value=text;node.setAttribute('readonly','');
    node.style.position='fixed';node.style.left='-9999px';document.body.appendChild(node);
    node.select();var success=false;
    try{success=!!document.execCommand('copy');}catch(err){}
    document.body.removeChild(node);return success;
  }
  function copyPart(which){
    var content=$('primaryEmail'+which).textContent;
    var feedback=$('primaryCopyFeedback');
    function done(ok){feedback.textContent=ok?(/\[[^\]]+\]/.test(content)?'Αντιγράφηκε. Συμπληρώστε όσα πεδία παραμένουν σε αγκύλες.':'Το κείμενο αντιγράφηκε.'):'Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.';}
    if(global.EducationTransferEmailComponent){
      global.EducationTransferEmailComponent.copy(content,{
        nativeSuccess:uiText('copySuccess','Το κείμενο αντιγράφηκε.'),
        nativeBrackets:uiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'),
        fallbackSuccess:uiText('copySuccess','Το κείμενο αντιγράφηκε.'),
        fallbackBrackets:uiText('copyBrackets','Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.'),
        fallbackFailure:uiText('copyNativeFailure','Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.')
      },feedback);
    }else if(navigator.clipboard && navigator.clipboard.writeText){navigator.clipboard.writeText(content).then(function(){done(true);},function(){done(legacyCopy(content));});}
    else done(legacyCopy(content));
  }
  // The same safe printer is used by P.E. and D.E. It receives complete
  // advisory notes without turning informational rows into checklist tasks.
  function printPrimaryChecklist(){
    var output=$('primaryPrintFeedback');
    if(!global.EducationTransferChecklistPrint){
      if(output)output.textContent='Δεν φορτώθηκε ο μηχανισμός εκτύπωσης. Επαναφορτώστε τη σελίδα.';
      return;
    }
    var docs=buildDocuments();
    var subject=messageParts(docs);
    var mode=$('primaryMode');
    var label=mode.selectedOptions && mode.selectedOptions[0] ? mode.selectedOptions[0].textContent : mode.value;
    if(mode.value==='eae'||mode.value==='sensory'){
      var dest=$('primaryDestination');
      if(dest.selectedOptions && dest.selectedOptions[0])label+=' — '+dest.selectedOptions[0].textContent;
    }
    var lines=[$('primarySubmissionInstruction').textContent];
    if(!$('primaryMydocsNotice').hidden)lines.push($('primaryMydocsNotice').textContent);
    var sections=[
      {title:'Πού αποστέλλονται τα δικαιολογητικά;',lines:lines},
      {title:'Θέμα και σώμα μηνύματος — υπόδειγμα',lines:[
        $('primaryTemplateNote').textContent,
        'Θέμα: '+subject.subject,
        'Σώμα: '+subject.body,
        $('primaryEmailSource').textContent
      ]}
    ];
    var rows=docs.map(function(d){
      var extra='';
      if(d.id==='children-guide' && global.EducationTransferChildrenDocuments){
        extra=global.EducationTransferChildrenDocuments.detail('primary');
      } else if(d.id==='locality' && global.EducationTransferLocalityResources){
        extra=global.EducationTransferLocalityResources.detail('primary')+'\nΠηγή: '+global.EducationTransferLocalityResources.source('primary');
      } else if(d.id==='coservice' && global.EducationTransferCoServiceResources){
        var resources=global.EducationTransferCoServiceResources,choice=$('primaryCoServiceCase').value;
        var legal=resources.legalDetail ? resources.legalDetail(choice) : '';
        extra=[legal,resources.evidenceRecency(),resources.legalSource('primary',choice),resources.printText(choice)].filter(Boolean).join('\n');
      }
      return {title:d.title,detail:d.desc,channel:d.channel==='email'?'Email προς ΔΠΕ οργανικής — όπου απαιτείται':
        d.channel==='mydocs'?'Υποβολή μέσω mydocs — εφόσον απαιτείται':'Ενημερωτικό / υπηρεσιακός έλεγχος',
        extra:extra,action:!!d.action,done:!!d.action && checklistState.isComplete(d.id)};
    });
    var ok=global.EducationTransferChecklistPrint.print({
      grade:'Πρωτοβάθμια Εκπαίδευση',mode:label,sections:sections,rows:rows,
      progress:$('primaryChecklistProgress').textContent,
      notice:'Ενδεικτική λίστα — όχι βεβαίωση υποχρεωτικών εγγράφων. Τα υπηρεσιακά/αυτεπάγγελτα στοιχεία δεν αποστέλλονται αυτομάτως. Ελέγξτε την εγκύκλιο Π.Ε. 138023/Ε2/15-10-2026 και τη ΔΠΕ οργανικής.'
    });
    if(!ok && output)output.textContent='Το πρόγραμμα περιήγησης απέκλεισε το παράθυρο εκτύπωσης. Επιτρέψτε τα αναδυόμενα παράθυρα και δοκιμάστε ξανά.';
    else if(output)output.textContent='';
  }
  $('primaryPrintChecklist').addEventListener('click',printPrimaryChecklist);
  $('primaryChecklist').addEventListener('change',function(e){
    if(e.target && e.target.matches('[data-primary-check]')){
      checklistState.set(e.target.getAttribute('data-primary-check'),e.target.checked);
      update();
    }
  });
  document.querySelectorAll('input,select').forEach(function(el){
    if(el.closest('#primaryEmailDetails'))return;
    el.addEventListener('input',function(){
      capPrimaryDirectPoints(el);
      if(el.id==='primaryChildren'&&global.EducationTransferSocialInput)
        global.EducationTransferSocialInput.clampChildren(el);
      update();
    });
    el.addEventListener('change',function(){capPrimaryDirectPoints(el);update();});
  });
  if(global.EducationTransferPrimaryPeriods)global.EducationTransferPrimaryPeriods.bind(update);
  if(global.EducationTransferEmailComponent&&global.EducationTransferEmailComponent.fillSpecialtyDropdown){
    global.EducationTransferEmailComponent.fillSpecialtyDropdown(
      $('primarySpecialty'),global.EducationTransferPrimarySpecialtyLabels||{},'');
  }
  ['primaryName','primarySpecialty','primaryRegistry','primaryProtocol','primaryDpe'].forEach(function(id){
    $(id).addEventListener('input',function(){updateMail(buildDocuments());});
  });
  $('primaryEmailOpen').addEventListener('click',function(){
    if(global.EducationTransferEmailComponent){
      global.EducationTransferEmailComponent.open($('primaryEmailDetails'),this,'#primaryPersonalize');
    }else{
      $('primaryEmailDetails').open=true;
      $('primaryPersonalize').open=true;
      this.setAttribute('aria-expanded','true');
      if($('primaryEmailDetails').scrollIntoView)$('primaryEmailDetails').scrollIntoView({block:'start',behavior:'smooth'});
    }
  });
  if(global.EducationTransferEmailComponent){
    global.EducationTransferEmailComponent.bindState($('primaryEmailDetails'),$('primaryEmailOpen'));
  }else $('primaryEmailDetails').addEventListener('toggle',function(){
    $('primaryEmailOpen').setAttribute('aria-expanded',String(this.open));
  });
  $('primaryClearEmail').addEventListener('click',function(){
    ['primaryName','primarySpecialty','primaryRegistry','primaryProtocol','primaryDpe'].forEach(function(id){$(id).value='';});
    $('primaryCopyFeedback').textContent='';updateMail(buildDocuments());
  });
  $('primaryCopySubject').addEventListener('click',function(){copyPart('Subject');});
  $('primaryCopyBody').addEventListener('click',function(){copyPart('Body');});
  if(global.EducationTransferEmailComponent){
    global.EducationTransferEmailComponent.syncPrimaryStatic($('primaryEmailFeature'));
  }
  update();
}(typeof window!=='undefined'?window:this));
