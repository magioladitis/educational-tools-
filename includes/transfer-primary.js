/* Isolated PRIMARY general, EAE and special structure transfer UI. Shared scoring module is unchanged. */
(function (global) {
  'use strict';
  var scoring = global.EducationTransfer;
  var eae = global.EducationTransferPrimaryEae;
  var intercultural = global.EducationTransferPrimaryIntercultural;
  var interVacancies = global.EducationTransferPrimaryInterculturalVacancies;
  if (!scoring || !eae || !intercultural || !interVacancies) return;
  var $ = function (id) { return document.getElementById(id); };
  if (!$('primaryMode')) return;
  var checkedDocs = {};
  function escapeText(s) { return String(s).replace(/[&<>"']/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
  function format(n) { return Number(n || 0).toLocaleString('el-GR', { maximumFractionDigits: 2 }); }
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
    var municipalDisposal=choice.value==='teacher_disposal' && $('primaryMode').value==='kedasy';
    if(municipalDisposal)data={summary:'Στα ΚΕΔΑΣΥ η συνυπηρέτηση εξετάζεται στον δήμο του ΚΕΔΑΣΥ. Η διάθεση σε ΠΥΣΠΕ/ΠΥΣΔΕ δεν τεκμηριώνει οργανική θέση στον συγκεκριμένο δήμο.',blocked:true};
    var blocked=!!data.blocked, review=!!data.review;
    if(blocked)check.checked=false;
    check.disabled=blocked;
    var field=$('primaryCoServiceCaseField');
    if(field)field.style.display=(check.checked || choice.value!=='unspecified')?'':'none';
    var note=$('primaryCoServiceAdvice');
    if(note)note.textContent=(check.checked || choice.value!=='unspecified')?(data.summary + (review?' Τα +4 μόρια δεν προστίθενται μέχρι επαλήθευσης.':'')):'';
    check.setAttribute('aria-describedby',blocked||review?'primaryCoServiceAdvice':'');
    var row=check.closest && check.closest('.check-row');
    if(row)row.classList.toggle('transfer-criterion-unavailable',blocked);
    return blocked || review;
  }
  function buildDocuments() {
    var mode=$('primaryMode').value;
    var docs=mode==='intercultural'?[]:[info('registry','Υπηρεσιακό μητρώο και μονάδες','Έλεγχος στο επίσημο μητρώο — χωρίς αποστολή')];
    if (mode!=='intercultural' && (Number($('primaryChildren').value)>0 || $('primaryFamily').checked)) {
      docs.push(info('family','Πιστοποιητικό οικογενειακής κατάστασης','Αυτεπάγγελτα από ΔΠΕ — δεν το προσκομίζετε'));
    }
    if(mode!=='intercultural' && $('primaryChildrenExtra').checked && Number($('primaryChildren').value)>0) docs.push(action('children','Συμπληρωματικά στοιχεία τέκνων','Email στη ΔΠΕ οργανικής — μόνο όπου δεν επαρκεί η αυτεπάγγελτη αναζήτηση / απαιτείται ειδικό αποδεικτικό'));
    if(mode!=='intercultural' && $('primaryCoservice').checked && !syncPrimaryCoService()) docs.push(action('coservice','Δικαιολογητικά συνυπηρέτησης','Αποστολή με email στη ΔΠΕ οργανικής — όπου απαιτούνται αποδεικτικά'));
    if(mode==='region' && $('primarySpecialCase') && $('primarySpecialCase').value!=='none') {
      var special=$('primarySpecialCase').value;
      if(special==='health_committee') docs.push(info('health-committee','Ειδική κατηγορία υγείας — γνωμάτευση υγειονομικής επιτροπής','Κλειστός φυσικός φάκελος προς ΥΠΑΙΘΑ με ένδειξη εμπιστευτικού πρωτοκόλλου· δεν αποστέλλεται με κοινό email. Εγκύκλιος Π.Ε. §2.2.3.'));
      if(special==='health_kepa') docs.push(info('health-kepa','Ειδική κατηγορία υγείας — ΚΕΠΑ','Καταχώριση Αριθμού Επιτροπής και Αριθμού Μητρώου ΚΕΠΑ στο επίσημο σύστημα, χωρίς αποστολή γνωμάτευσης. Εγκύκλιος Π.Ε. §2.2.3.'));
      if(special==='judicial_spouse') docs.push(mydocs('judicial-spouse','Σύζυγος δικαστικού λειτουργού — κατά προτεραιότητα','Μέσω mydocs: βεβαίωση εργασίας και έγγραφο περιφέρειας αρμοδιότητας του δικαστηρίου. Πιστοποιητικό οικογενειακής κατάστασης αυτεπάγγελτα. Εγκύκλιος Π.Ε. §2.2.4.'));
    }
    if(mode!=='intercultural' && $('primaryLocality').checked) docs.push(info('locality','Εντοπιότητα','Αυτεπάγγελτη αναζήτηση πιστοποιητικού δημοτικότητας/γέννησης από ΔΠΕ, όπου περιέχεται η ημερομηνία κτήσης δημοτικότητας· επιβεβαιώστε τα στοιχεία'));
    if(mode==='intercultural'){
      var existing=$('primaryInterDossier').value;
      var chosenQuals=interQualifications();
      if(existing==='unknown' && chosenQuals.length)docs.push(info('inter-dossier-review','Προηγούμενος φάκελος Διαπολιτισμικών','Ελέγξτε εάν απαιτείται νέα ή συμπληρωματική υποβολή.'));
      if(existing==='new')chosenQuals.forEach(function(k){docs.push(mydocs('inter-'+k,intercultural.qualificationNames[k],'Υποβολή μέσω mydocs — μόνο για νέα ή συμπληρωματικά αποδεικτικά προσόντων.'));});
      if($('primaryInterInclusion').value==='yes' && $('primaryInterEaeQualification').value==='yes')docs.push(info('inter-inclusion-eae','Προσόντα ΕΑΕ για Τμήμα Ένταξης','Επαληθεύστε εάν ο σχετικός φάκελος ΕΑΕ απαιτεί συμπλήρωση.'));
      return docs;
    }
    if(mode!=='region') {
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
  function updateCheckList(docs) {
    var checklist=$('primaryChecklist');
    checklist.innerHTML=docs.map(function(d){return '<div class="primary-doc-row" data-primary-doc="'+escapeText(d.id)+'">' +
      (d.action ? '<label><input type="checkbox" data-primary-check="'+escapeText(d.id)+'"'+(checkedDocs[d.id]?' checked':'')+'><strong>'+escapeText(d.title)+'</strong></label>' : '<div><strong>ⓘ '+escapeText(d.title)+'</strong></div>') +
      '<p class="primary-doc-kind">'+escapeText(d.desc)+'</p></div>';}).join('');
    var actionable=docs.filter(function(d){return d.action;});
    Object.keys(checkedDocs).forEach(function(k){ if (!actionable.some(function(d){return d.id===k;})) delete checkedDocs[k]; });
    var completed=actionable.filter(function(d){return checkedDocs[d.id];}).length;
    $('primaryChecklistProgress').textContent= completed+' από '+actionable.length+' ενέργειες δικαιολογητικών έχουν ολοκληρωθεί';
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
    var mode=$('primaryMode').value;
    $('primaryEaeFields').hidden=mode==='region'||mode==='intercultural';
    $('primaryGeneralOrganicField').hidden=mode!=='region';
    $('primaryEligibility').hidden=mode!=='region';
    $('primaryKedasyGeoNote').hidden=mode!=='kedasy';
    var isCertificate=mode==='deaf'||mode==='blind';
    $('primaryEaeCertificationField').hidden=!isCertificate;
    $('primaryEaeCertificationDossierField').hidden=!isCertificate || $('primaryEaeCertification').value!=='yes';
    $('primaryEaeCertificationLabel').textContent=mode==='deaf'?'Πιστοποιημένη γνώση Ελληνικής Νοηματικής Γλώσσας (ΕΝΓ)':'Πιστοποιημένη γνώση γραφής Braille';
    if(mode==='region'||mode==='intercultural')return;
    var origin=$('primaryEaeOrigin').value;
    $('primaryEaeGeneralYearsField').hidden=origin!=='general';
    $('primaryEaeAppointmentYearsField').hidden=origin!=='appointed-eae';
    $('primaryEaeYearsField').hidden=!$('primaryEaeExperience').checked;
    $('primaryPreviousDossierField').hidden=origin==='appointed-eae';
    var data={mode:mode,branch:$('primaryEaeBranch').value,origin:origin,
      organic:$('primaryEaeOrganic').value,generalYears:$('primaryEaeGeneralYears').value,
      appointmentAreaYears:$('primaryEaeAppointmentYears').value,eaeYears:$('primaryEaeYears').value,
      qualifications:qualifications(),certification:$('primaryEaeCertification').value};
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
  function update() {
    // Official application codes from the Primary Education application list.
    // EEEEK shares the SΜΚΔ application with SΜΕΑΕ; Deaf/Blind shares the language-specialization application.
    // This is a UI reference, NOT an alternate electronic filing path.
    updateEaePrecheck();
    updateIntercultural();
    var interculturalMode=$('primaryMode').value==='intercultural';
    $('primaryScoringCard').hidden=interculturalMode;
    $('primarySocialCriteriaCard').hidden=interculturalMode;
    var special=$('primarySpecialCaseField');
    if(special){ special.style.display=$('primaryMode').value==='region'?'':'none';
      if($('primaryMode').value!=='region')$('primarySpecialCase').value='none'; }
    var firstRow=$('primaryFirstPreferenceRow');
    if(firstRow){ firstRow.classList.toggle('hidden',interculturalMode); if(interculturalMode)$('primaryFirst').checked=false; }
    var invalidSpouse=syncPrimaryCoService();
    $('primaryChecklist').closest('section.card').querySelector('h2').textContent=(interculturalMode?'Β.':'Δ.')+' Προσωπική λίστα δικαιολογητικών';
    $('primaryScoreHeading').textContent=interculturalMode?'Κρίση βάσει προσόντων':'Ενδεικτική μοριοδότηση';
    var mode=$('primaryServiceMode').value;
    $('primaryServicePointsPanel').hidden=mode!=='points';
    $('primaryServiceDurationPanel').hidden=mode!=='direct';
    var children=numeric($('primaryChildren'),20),years=numeric($('primaryYears'),50), months=numeric($('primaryMonths'),11), days=numeric($('primaryDays'),29);
    var childHint=$('primaryChildrenHint'), childField=$('primaryChildren');
    var childOutOfRange=children===null;
    if(childHint){ childHint.textContent=childOutOfRange?'⚠ Εισαγάγετε ακέραιο αριθμό τέκνων από 0 έως 20.':'Επιτρεπτές τιμές: 0–20 τέκνα.'; childHint.setAttribute('data-invalid',String(childOutOfRange)); }
    childField.setAttribute('aria-invalid',String(childOutOfRange));
    if (children === 0) { $('primaryChildrenExtra').checked = false; $('primaryChildrenExtra').disabled = true; }
    else $('primaryChildrenExtra').disabled = false;
    var numericValid=[children,years,months,days].every(function(v){return v!==null;});
    var raw={mode:'region',serviceMode:mode,serviceDirectPoints:$('primaryServicePoints').value,
      serviceYears:years,serviceMonths:months,serviceDays:days,msdMode:'direct',msdDirectPoints:$('primaryMsd').value,
      eligibleChildren:children,familyStatusEligible:$('primaryFamily').checked,coService:$('primaryCoservice').checked && !invalidSpouse,
      locality:$('primaryLocality').checked,firstPreference:$('primaryFirst').checked};
    // No empty-as-zero interpretation for official-register inputs.
    if (!$('primaryMsd').value.trim() || (mode==='points' && !$('primaryServicePoints').value.trim())) numericValid=false;
    var result=numericValid?scoring.calculate(raw):null;
    var valid=!!result && result.serviceInputValid && result.msdInputValid;
    $('primaryTotal').textContent=interculturalMode?'—':valid?format(result.total):'—';
    $('primaryTotalStatus').textContent=interculturalMode?'Δεν εφαρμόζεται μοριοδότηση. Η μετάθεση εξετάζεται βάσει προσόντων και διαθέσιμων οργανικών κενών.':valid?'Πληροφοριακό αποτέλεσμα — ελέγξτε τα κριτήρια και το επίσημο μητρώο.':'Συμπληρώστε έγκυρα στοιχεία (έως 2 δεκαδικά, Μ.Σ.Δ. έως 1.000 και προϋπηρεσία έως 125).';
    $('primaryValidation').textContent=interculturalMode?'':!valid?'Δεν εμφανίζεται σύνολο όσο λείπουν ή είναι άκυρα βασικά αριθμητικά πεδία.':'';
    $('primaryBreakdown').innerHTML=!interculturalMode&&valid?[
      ['Συνολική υπηρεσία',result.servicePoints],['Μονάδες συνθηκών διαβίωσης',result.msdPoints],
      ['Συνυπηρέτηση',result.coServicePoints],['Οικογενειακοί λόγοι',result.familyPoints],
      ['Εντοπιότητα',result.localityPoints],['Πρώτη προτίμηση',result.firstPreferencePoints]
    ].map(function(x){return '<p>'+escapeText(x[0])+': <strong>'+escapeText(format(x[1]))+'</strong></p>';}).join(''):'';
    var state=$('primaryOrganic').value;
    $('primaryEligibility').textContent=state==='yes'
      ? '✓ Δηλώθηκε συμπλήρωση έτους οργανικής. Ο οριστικός έλεγχος και οι ειδικές εξαιρέσεις ανήκουν στη ΔΠΕ.'
      : state==='no'
        ? '⚠ Η μη συμπλήρωση έτους οργανικής απαιτεί ειδικό υπηρεσιακό έλεγχο· ενδέχεται να ισχύουν νόμιμες εξαιρέσεις. Μην θεωρείτε την αίτηση αυτομάτως επιλέξιμη.'
        : 'ⓘ Ελέγξτε την υπηρεσία στην οργανική έως 31/08/2027 και τις ενδεχόμενες εξαιρέσεις της εγκυκλίου.';
    updateCheckList(buildDocuments());
  }
  // Section 2.2.1 (general) and 2.2.5 (EAE) prescribe distinct, exact
  // comma-separated subject/body field orders. The secondary composer uses
  // the same visual component, but PRIMARY legal wording is defined here.
  function messageParts(docs) {
    var actions=docs.filter(function(d){return d.action && d.channel==='email';});
    var general=$('primaryMode').value==='region';
    var name=$('primaryName').value.trim()||'[Ονοματεπώνυμο]';
    var branch=$('primarySpecialty').value.trim()||($('primaryMode').value!=='region'&&$('primaryEaeBranch').value)||'[Κλάδος/ειδικότητα]';
    var registry=$('primaryRegistry').value.trim()||'[Αριθμός μητρώου]';
    var protocol=$('primaryProtocol').value.trim()||'[Αριθμός πρωτοκόλλου οριστικοποιημένης αίτησης]';
    var dpe=$('primaryDpe').value.trim()||'[ΔΠΕ οργανικής]';
    var labels={
      region:'Μετάθεση από περιοχή σε περιοχή',
      eae:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      kedasy:'μετάθεση σε ΚΕΔΑΣΥ',
      eeeek:'μετάθεση σε ΣΜΕΑΕ–ΕΕΕΕΚ',
      deaf:'μετάθεση με εξειδίκευση στην ΕΝΓ',
      blind:'μετάθεση με εξειδίκευση στη γραφή Braille',
      intercultural:'μετάθεση σε Διαπολιτισμικό Σχολείο'
    };
    var typ=labels[$('primaryMode').value]||labels.region;
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
    var special=!email.general;
    var hasMydocs=mydocActions.length>0;
    $('primaryMydocsLink').hidden=!special;
    $('primaryMydocsLink').innerHTML='<a href="https://mydocs.minedu.gov.gr/" target="_blank" rel="noopener noreferrer">mydocs ΥΠΑΙΘΑ ↗</a> — για προσόντα '+(isIntercultural?'Διαπολιτισμικών':'ΕΑΕ')+' όταν απαιτείται νέος/συμπληρωματικός φάκελος.';
    $('primaryMydocsNotice').hidden=!hasMydocs;
    $('primaryMydocsNotice').textContent=hasMydocs?'Έχετε '+mydocActions.length+' δικαιολογητικό/ά προσόντων '+(isIntercultural?'Διαπολιτισμικών':'ΕΑΕ')+' για υποβολή στο mydocs.minedu.gov.gr (όχι με email). Δεν επανυποβάλλονται ήδη κατατεθειμένα στοιχεία.':'';
    var sharedState=global.EducationTransferEmailComponent?global.EducationTransferEmailComponent.stateCopy(hasActions?'email':hasMydocs?'mydocs':'optional'):null;
    $('primaryEmailHeading').textContent=sharedState?sharedState.title:hasActions?'Έτοιμο email υποβολής δικαιολογητικών':hasMydocs?'Έτοιμο υπόδειγμα υποβολής μέσω mydocs':'Υπόδειγμα μηνύματος — αν χρειαστεί';
    $('primaryEmailLead').textContent=isIntercultural?'Τα προσόντα Διαπολιτισμικών υποβάλλονται μέσω mydocs, όταν απαιτείται νέος/συμπληρωματικός φάκελος. Δεν αποστέλλονται δικαιολογητικά μοριοδότησης.':hasActions?'Δημιουργήστε θέμα και σώμα μηνύματος για τα δικαιολογητικά μοριοδότησης που αποστέλλονται στη ΔΠΕ οργανικής.':hasMydocs?'Τα προσόντα ΕΑΕ υποβάλλονται μέσω mydocs όταν απαιτείται νέος ή συμπληρωματικός φάκελος — όχι με email.':'Δεν προκύπτει αυτή τη στιγμή δικαιολογητικό για αποστολή με email. Το υπόδειγμα παραμένει διαθέσιμο για προεπισκόπηση.';
    $('primaryEmailOpen').textContent=sharedState?sharedState.button:hasActions?'Δημιουργία email υποβολής':hasMydocs?'Προετοιμασία κειμένου για mydocs':'Προβολή προαιρετικού υποδείγματος';
    $('primarySubmissionInstruction').textContent=isIntercultural?'Διαπολιτισμικά: μόνο τα δικαιολογητικά προσόντων μέσω mydocs, όταν απαιτείται νέος/συμπληρωματικός φάκελος· όχι μοριοδότηση και όχι email.':email.general?'Γενική μετάθεση: δικαιολογητικά μέσω email στη ΔΠΕ οργανικής, εφόσον απαιτούνται.':'ΕΑΕ: τα αποδεικτικά μοριοδότησης αποστέλλονται με email στη ΔΠΕ οργανικής, ενώ τα αποδεικτικά προσόντων υποβάλλονται στο mydocs μόνο όπου απαιτείται νέα/συμπληρωματική υποβολή.';
    $('primaryTemplateNote').textContent=isIntercultural?'Το θέμα και το σώμα ακολουθούν τη σειρά της §2.2.5 για υποβολή μέσω mydocs.':email.general?'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.1.':'Το θέμα και το σώμα τηρούν ακριβώς τη σειρά πεδίων της §2.2.5. Στο mydocs το σώμα μπορεί να χρησιμοποιηθεί ως κείμενο «Αιτούμαι».';
    $('primaryEmailSource').textContent='Πηγή: εγκύκλιος μεταθέσεων Π.Ε. 138023/Ε2/15-10-2026, §'+(email.general?'2.2.1':'2.2.5')+'. Το υπόδειγμα δεν αντικαθιστά ειδικές διαδικασίες ή προηγούμενο φάκελο.';
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
  $('primaryChecklist').addEventListener('change',function(e){
    if(e.target && e.target.matches('[data-primary-check]')){
      checkedDocs[e.target.getAttribute('data-primary-check')]=e.target.checked;
      update();
    }
  });
  document.querySelectorAll('input,select').forEach(function(el){
    if(el.closest('#primaryEmailDetails'))return;
    el.addEventListener('input',function(){
      if(el.id==='primaryChildren') global.EducationTransferSocialInput.clampChildren(el);
      update();
    });
    el.addEventListener('change',update);
  });
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
