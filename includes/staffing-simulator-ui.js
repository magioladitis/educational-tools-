(function(){
  const staffingRuntimeConfigNode=document.getElementById('staffingRuntimeConfig');
  let staffingRuntimeConfig={};
  if(staffingRuntimeConfigNode){
    try{ staffingRuntimeConfig=JSON.parse(staffingRuntimeConfigNode.textContent||'{}'); }
    catch(error){ console.error('Αποτυχία φόρτωσης ρυθμίσεων στελέχωσης.',error); }
    staffingRuntimeConfigNode.textContent='';
  }
  const type=document.getElementById('school_type');
  const gym=document.getElementById('gymProfileFields');
  const gel=document.getElementById('gelProfileFields');
  const reset=document.getElementById('staffingReset');
  const staffingPrintButton=document.getElementById('staffingPrintButton');
  if(staffingPrintButton){
    staffingPrintButton.addEventListener('click',function(){
      const stamp=document.querySelector('[data-print-generated-at]');
      if(stamp){
        const now=new Date();
        stamp.textContent='Εκτύπωση: '+now.toLocaleDateString('el-GR')+' '+now.toLocaleTimeString('el-GR',{hour:'2-digit',minute:'2-digit'});
      }
      window.print();
    });
  }
  function compactRepeatedFormState(form,prefix,payloadName){
    if(!form) return;
    const payloadInput=form.querySelector('input[name="'+payloadName+'"]');
    if(!payloadInput) return;
    const payload={};
    const fields=Array.from(form.querySelectorAll('[name^="'+prefix+'"]')).filter(function(el){
      return el.name!==payloadName && /\[\]$/.test(el.name);
    });
    // Κρατάμε θέση και για disabled πεδία. Έτσι οι παράλληλοι πίνακες
    // slot/person/hours (και τα αντίστοιχα personnel arrays) δεν μπορούν να
    // μετατοπιστούν μεταξύ τους όταν ένα control είναι προσωρινά disabled.
    fields.forEach(function(el){
      const key=el.name.replace(/\[\]$/,'');
      if(!payload[key]) payload[key]=[];
      payload[key].push(el.disabled?'':el.value);
    });
    payloadInput.value=JSON.stringify(payload);
    fields.forEach(function(el){el.disabled=true;});
  }
  function installExplicitRequestGate(form){
    if(!form || form.dataset.requestGateBound==='1') return;
    form.dataset.requestGateBound='1';
    const actionInput=form.querySelector('input[name="staffing_action"]');
    const requestButtons=Array.from(form.querySelectorAll('[data-staffing-request-action]'));
    requestButtons.forEach(function(button){
      button.addEventListener('click',function(event){
        if(form.dataset.requestInFlight==='1'){
          event.preventDefault();
          return;
        }
        const action=button.getAttribute('data-staffing-request-action')||'';
        if(action===''){
          event.preventDefault();
          return;
        }
        if(typeof form.reportValidity==='function' && !form.reportValidity()){
          event.preventDefault();
          return;
        }
        if(form.id==='staffingPersonnelForm') compactRepeatedFormState(form,'personnel_','personnel_payload_json');
        if(form.id==='staffingAllocationForm') compactRepeatedFormState(form,'allocation_','allocation_payload_json');
        if(actionInput) actionInput.value=action;
        form.dataset.explicitRequest='1';
      });
    });
    form.addEventListener('submit',function(event){
      var submitter=event.submitter || null;
      var submitterAction=submitter && submitter.getAttribute ? (submitter.getAttribute('data-staffing-request-action')||'') : '';
      if(actionInput && actionInput.value==='' && submitterAction!=='') actionInput.value=submitterAction;
      const armed=(actionInput && actionInput.value!=='') || submitterAction!=='' || form.dataset.explicitRequest==='1';
      if(!armed || form.dataset.requestInFlight==='1'){
        event.preventDefault();
        return;
      }
      form.dataset.requestInFlight='1';
      requestButtons.forEach(function(button){button.disabled=true;});
    });
  }
  ['staffingProfileForm','staffingPersonnelForm','staffingAllocationForm'].forEach(function(id){ installExplicitRequestGate(document.getElementById(id)); });

  const tabs=Array.from(document.querySelectorAll('[data-staffing-tab]'));
  const panels=Array.from(document.querySelectorAll('[data-staffing-panel]'));
  function activatePanel(name){
    tabs.forEach(function(tab){
      const active=tab.getAttribute('data-staffing-tab')===name;
      tab.classList.toggle('is-active',active);
      tab.setAttribute('aria-selected',active?'true':'false');
      tab.tabIndex=active?0:-1;
    });
    panels.forEach(function(panel){ panel.hidden=panel.getAttribute('data-staffing-panel')!==name; });
    if(name==='specialties' && typeof allocationCollectState==='function' && typeof renderSpecialtyBalance==='function') renderSpecialtyBalance(allocationCollectState());
  }
  function moveTabFocus(current,key){
    const enabled=tabs.filter(function(tab){return !tab.disabled;});
    const index=enabled.indexOf(current);
    if(index<0||enabled.length<1) return;
    let next=index;
    if(key==='ArrowRight'||key==='ArrowDown') next=(index+1)%enabled.length;
    else if(key==='ArrowLeft'||key==='ArrowUp') next=(index-1+enabled.length)%enabled.length;
    else if(key==='Home') next=0;
    else if(key==='End') next=enabled.length-1;
    else return;
    const target=enabled[next];
    activatePanel(target.getAttribute('data-staffing-tab'));
    target.focus();
  }
  tabs.forEach(function(tab){
    tab.addEventListener('click',function(){ if(!tab.disabled) activatePanel(tab.getAttribute('data-staffing-tab')); });
    tab.addEventListener('keydown',function(event){
      if(['ArrowRight','ArrowDown','ArrowLeft','ArrowUp','Home','End'].indexOf(event.key)<0) return;
      event.preventDefault();
      moveTabFocus(tab,event.key);
    });
  });
  const maxBasicSections=Number(staffingRuntimeConfig.maxBasicSections||150);
  function sync(){
    const isGel=type.value==='gel';
    const isEveningGel=type.value==='esperino_gel';
    const isEveningGym=type.value==='esperino_gymnasio';
    const isComposite=type.value==='gymnasio_lt';
    const showGym=type.value==='gymnasio'||isEveningGym||isComposite;
    const showGel=isGel||isEveningGel||isComposite;
    gym.hidden=!showGym;
    gel.hidden=!showGel;
    gym.querySelectorAll('input,select').forEach(el=>{ el.disabled=!showGym; });
    gel.querySelectorAll('input,select').forEach(el=>{ el.disabled=!showGel; });
    gym.querySelectorAll('[data-day-gym-only]').forEach(function(panel){
      const visible=showGym&&!isEveningGym;
      panel.hidden=!visible;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!visible;});
      if(!visible && panel.tagName==='DETAILS') panel.open=false;
    });
    gel.querySelectorAll('[data-day-gel-only]').forEach(function(panel){
      const visible=showGel&&!isEveningGel;
      panel.hidden=!visible;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!visible;});
    });
    gel.querySelectorAll('[data-evening-gel-only]').forEach(function(panel){
      panel.hidden=!isEveningGel;
      panel.querySelectorAll('input,select').forEach(function(el){el.disabled=!isEveningGel;});
    });
    syncBasicSectionLimit();
  }
  type.addEventListener('change',sync); sync();
  function syncSplitMaximums(){
    document.querySelectorAll('[data-max-source]').forEach(function(input){
      const source=document.getElementById(input.getAttribute('data-max-source'));
      const max=source ? Math.max(0,parseInt(source.value||'0',10)||0) : 0;
      input.max=String(max);
      document.querySelectorAll('[data-max-label="'+input.getAttribute('data-max-source')+'"]').forEach(function(label){ label.textContent=String(max); });
      if((parseInt(input.value||'0',10)||0)>max) input.value=String(max);
    });
  }
  document.querySelectorAll('[id^="gym_general_"]').forEach(function(input){ input.addEventListener('input',syncSplitMaximums); });
  syncSplitMaximums();
  function syncLanguageGroupMaximums(){
    document.querySelectorAll('[data-language-max-source]').forEach(function(input){
      const source=document.getElementById(input.getAttribute('data-language-max-source'));
      const max=source ? Math.max(0,parseInt(source.value||'0',10)||0) : 0;
      const value=input.value==='' ? 0 : Math.max(0,parseInt(input.value||'0',10)||0);
      const grade=input.getAttribute('data-language-grade')||'';
      const language=input.getAttribute('data-language-name')||'Η γλώσσα';
      const error=document.querySelector('[data-language-error-for="'+input.id+'"]');
      input.max=String(max);
      if(value>max){
        const message='Οι ομάδες «'+language+'» της '+grade+' τάξης ('+value+') δεν μπορούν να ξεπερνούν τα '+max+' κανονικά τμήματα της ίδιας τάξης.';
        input.setCustomValidity(message);
        input.setAttribute('aria-invalid','true');
        if(error){error.hidden=false;error.textContent=message;}
      }else{
        input.setCustomValidity('');
        input.removeAttribute('aria-invalid');
        if(error){error.hidden=true;error.textContent='';}
      }
    });
  }
  document.querySelectorAll('[data-language-max-source]').forEach(function(input){ input.addEventListener('input',syncLanguageGroupMaximums); });
  document.querySelectorAll('[id^="gym_general_"],[id^="gel_general_"]').forEach(function(input){ input.addEventListener('input',syncLanguageGroupMaximums); });
  syncLanguageGroupMaximums();

  function basicSectionSafeInteger(input){
    const raw=String(input.value==null?'':input.value).trim();
    if(raw==='') return 0;
    if(!/^\d+$/.test(raw)){
      input.value=input.dataset.lastBasicSectionValue||'0';
      return parseInt(input.value||'0',10)||0;
    }
    const digits=raw.replace(/^0+(?=\d)/,'');
    const maxDigits=String(maxBasicSections);
    let value=0;
    if(digits.length>maxDigits.length || (digits.length===maxDigits.length && digits>maxDigits)){
      value=maxBasicSections;
    }else{
      value=parseInt(digits||'0',10)||0;
    }
    input.value=String(value);
    input.dataset.lastBasicSectionValue=String(value);
    return value;
  }
  function syncBasicSectionLimit(changedInput){
    const isComposite=type && type.value==='gymnasio_lt';
    const groups=isComposite ? [['composite',Array.from(document.querySelectorAll('[data-basic-section]'))]] : ['gym','gel'].map(function(kind){return [kind,Array.from(document.querySelectorAll('[data-basic-section="'+kind+'"]'))];});
    groups.forEach(function(entry){
      const kind=entry[0], inputs=entry[1];
      if(!inputs.length) return;
      if(changedInput && inputs.indexOf(changedInput)<0) return;
      let clamped=false;
      if(changedInput){
        const value=basicSectionSafeInteger(changedInput);
        const otherTotal=inputs.reduce(function(sum,input){
          if(input===changedInput) return sum;
          return sum+basicSectionSafeInteger(input);
        },0);
        const allowed=Math.max(0,maxBasicSections-otherTotal);
        if(value>allowed){
          changedInput.value=String(allowed);
          changedInput.dataset.lastBasicSectionValue=String(allowed);
          clamped=true;
        }
      }else{
        let remaining=maxBasicSections;
        inputs.forEach(function(input){
          const value=basicSectionSafeInteger(input);
          const allowed=Math.min(value,remaining);
          if(value!==allowed){
            input.value=String(allowed);
            input.dataset.lastBasicSectionValue=String(allowed);
            clamped=true;
          }
          remaining-=allowed;
        });
      }
      const total=inputs.reduce(function(sum,input){return sum+(parseInt(input.value||'0',10)||0);},0);
      inputs.forEach(function(input){
        const current=parseInt(input.value||'0',10)||0;
        const others=total-current;
        input.max=String(Math.max(0,maxBasicSections-others));
        input.setCustomValidity('');
        input.removeAttribute('aria-invalid');
      });
      const errorTargets=isComposite ? Array.from(document.querySelectorAll('[data-basic-sections-error]')) : Array.from(document.querySelectorAll('[data-basic-sections-error="'+kind+'"]'));
      errorTargets.forEach(function(error){
        error.hidden=!clamped;
        error.textContent=clamped?'Η τιμή περιορίστηκε αυτόματα ώστε το σύνολο των βασικών τμημάτων της σχολικής μονάδας να μην υπερβαίνει τα '+maxBasicSections+'.':'';
      });
    });
    syncSplitMaximums();
    syncLanguageGroupMaximums();
  }
  document.querySelectorAll('[data-basic-section]').forEach(function(input){
    input.addEventListener('keydown',function(event){
      if(['e','E','+','-','.'].indexOf(event.key)>=0) event.preventDefault();
    });
    input.addEventListener('input',function(){syncBasicSectionLimit(input);},true);
  });
  syncBasicSectionLimit();

  const schoolProfileForm=document.getElementById('staffingProfileForm');
  const staffingContextSchool=document.getElementById('staffingContextSchool');
  const staffingContextType=document.getElementById('staffingContextType');
  const staffingContextCode=document.getElementById('staffingContextCode');
  const staffingContextSections=document.getElementById('staffingContextSections');
  const staffingContextState=document.getElementById('staffingContextState');
  const staffingContextAssigned=document.getElementById('staffingContextAssigned');
  const staffingContextUnassigned=document.getElementById('staffingContextUnassigned');
  const staffingContextInitialAllocation=!!staffingRuntimeConfig.initialAllocation;
  function staffingContextTypeLabel(value){
    const labels={
      gymnasio:'Ημερήσιο Γυμνάσιο',
      esperino_gymnasio:'Εσπερινό Γυμνάσιο',
      gel:'Ημερήσιο ΓΕΛ',
      esperino_gel:'Εσπερινό ΓΕΛ',
      gymnasio_lt:'Γυμνάσιο με Λ.Τ.'
    };
    return labels[value]||value||'—';
  }
  function refreshStaffingContextFromForm(stateText){
    if(!schoolProfileForm) return;
    const nameField=schoolProfileForm.elements.namedItem('school_name');
    const codeField=schoolProfileForm.elements.namedItem('school_code');
    const typeField=schoolProfileForm.elements.namedItem('school_type');
    const name=String(nameField&&nameField.value||'').trim();
    const code=String(codeField&&codeField.value||'').trim();
    const typeValue=String(typeField&&typeField.value||'').trim();
    const sectionTotal=Array.from(schoolProfileForm.querySelectorAll('[data-basic-section]')).reduce(function(sum,input){
      if(input.disabled) return sum;
      return sum+(parseInt(input.value||'0',10)||0);
    },0);
    if(staffingContextSchool) staffingContextSchool.textContent=name||staffingContextTypeLabel(typeValue)||'Δεν έχει φορτωθεί σχολική μονάδα';
    if(staffingContextType) staffingContextType.textContent=staffingContextTypeLabel(typeValue);
    if(staffingContextCode){
      staffingContextCode.hidden=code==='';
      staffingContextCode.innerHTML=code===''?'':'κωδ. <strong>'+escapeHtml(code)+'</strong>';
    }
    if(staffingContextSections){
      staffingContextSections.hidden=sectionTotal<=0;
      staffingContextSections.innerHTML=sectionTotal>0?'<strong>'+sectionTotal+'</strong> τμήματα':'';
    }
    if(staffingContextState && stateText) staffingContextState.textContent=stateText;
  }
  const openSchoolCsv=document.getElementById('openSchoolCsv');
  const schoolCsvFile=document.getElementById('schoolCsvFile');
  const chooseSchoolCsvFile=document.getElementById('chooseSchoolCsvFile');
  const clearSchoolCsvRegistry=document.getElementById('clearSchoolCsvRegistry');
  const schoolCsvPanel=document.getElementById('schoolCsvPanel');
  const closeSchoolCsv=document.getElementById('closeSchoolCsv');
  const schoolCsvMeta=document.getElementById('schoolCsvMeta');
  const schoolCsvPreview=document.getElementById('schoolCsvPreview');
  const schoolCsvStatus=document.getElementById('schoolCsvStatus');
  const schoolCsvActive=document.getElementById('schoolCsvActive');
  const downloadSchoolCsvTemplate=document.getElementById('downloadSchoolCsvTemplate');
  const loadCorfuSchoolDirectory=document.getElementById('loadCorfuSchoolDirectory');
  const downloadCorfuSchoolDirectory=document.getElementById('downloadCorfuSchoolDirectory');
  const schoolRegistrySearch=document.getElementById('schoolRegistrySearch');
  let schoolCsvRegistry=[];
  const schoolCsvStorageKey='education_school_registry_v1';

  function schoolCsvSetStatus(message,kind){
    if(!schoolCsvStatus) return;
    schoolCsvStatus.className='personnel-csv-status'+(kind?' is-'+kind:'');
    schoolCsvStatus.textContent=message||'';
  }
  function persistSchoolCsvRegistry(){
    try{
      if(schoolCsvRegistry.length) sessionStorage.setItem(schoolCsvStorageKey,JSON.stringify(schoolCsvRegistry));
      else sessionStorage.removeItem(schoolCsvStorageKey);
    }catch(e){}
  }
  function restoreSchoolCsvRegistry(){
    try{
      const raw=sessionStorage.getItem(schoolCsvStorageKey);
      if(!raw) return false;
      const parsed=JSON.parse(raw);
      if(!Array.isArray(parsed)) return false;
      const problems=schoolRegistryValidationProblems(parsed);
      if(schoolRegistryHasProblems(problems)){
        sessionStorage.removeItem(schoolCsvStorageKey);
        schoolCsvRegistry=[];
        return false;
      }
      schoolCsvRegistry=parsed;
      return schoolCsvRegistry.length>0;
    }catch(e){return false;}
  }
  function schoolCsvKnownPlaceholder(typeValue){
    return !!(window.EducationSchoolCsv && Array.isArray(window.EducationSchoolCsv.placeholderTypes) && window.EducationSchoolCsv.placeholderTypes.indexOf(typeValue)>=0);
  }
  function schoolCsvTotalSections(record){
    let total=['general_a','general_b','general_c'].reduce(function(sum,key){return sum+(parseInt(record[key]||0,10)||0);},0);
    if(record && record.school_type==='gymnasio_lt') total+=['lt_general_a','lt_general_b','lt_general_c'].reduce(function(sum,key){return sum+(parseInt(record[key]||0,10)||0);},0);
    return total;
  }
  function schoolRegistryValidationProblems(records){
    const seenIds=new Map(), seenCodes=new Map(), duplicateIds=new Set(), duplicateCodes=new Set(), oversized=[];
    (records||[]).forEach(function(record,index){
      const id=String(record&&record.school_id||'').trim();
      const code=String(record&&record.school_code||'').trim();
      if(id){ if(seenIds.has(id)) duplicateIds.add(id); else seenIds.set(id,index); }
      if(code){ if(seenCodes.has(code)) duplicateCodes.add(code); else seenCodes.set(code,index); }
      const total=schoolCsvTotalSections(record||{});
      if(total>maxBasicSections) oversized.push({index:index,name:String(record&&record.school_name||id||code||('Σχολείο '+(index+1))),total:total});
    });
    return {duplicateIds:Array.from(duplicateIds),duplicateCodes:Array.from(duplicateCodes),oversized:oversized};
  }
  function schoolRegistryProblemsMessage(problems){
    const messages=[];
    if(problems.duplicateIds.length) messages.push('διπλό αναγνωριστικό σχολείου: '+problems.duplicateIds.join(', '));
    if(problems.duplicateCodes.length) messages.push('διπλό κωδικό Υπουργείου / myschool: '+problems.duplicateCodes.join(', '));
    if(problems.oversized.length) messages.push('υπέρβαση του ορίου των '+maxBasicSections+' βασικών τμημάτων: '+problems.oversized.map(function(item){return item.name+' ('+item.total+')';}).join(', '));
    return messages.join(' · ');
  }
  function schoolRegistryHasProblems(problems){
    return !!(problems.duplicateIds.length || problems.duplicateCodes.length || problems.oversized.length);
  }
  function normalizeSchoolRegistrySearch(value){
    return String(value==null?'':value).toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  }
  function renderSchoolCsvRegistry(){
    if(!schoolCsvPreview) return;
    schoolCsvPreview.innerHTML='';
    if(!schoolCsvRegistry.length){
      const empty=document.createElement('div');
      empty.className='empty-personnel';
      empty.textContent='Το μητρώο δεν περιέχει σχολικές μονάδες.';
      schoolCsvPreview.appendChild(empty);
      return;
    }
    const query=normalizeSchoolRegistrySearch(schoolRegistrySearch?schoolRegistrySearch.value:'').trim();
    const table=document.createElement('table');
    table.className='school-registry-table';
    const thead=document.createElement('thead');
    thead.innerHTML='<tr><th>Σχολική μονάδα</th><th>Κωδικός</th><th>Είδος</th><th>Α΄</th><th>Β΄</th><th>Γ΄</th><th title="Σύνολο τμημάτων" aria-label="Σύνολο τμημάτων">Σ</th><th>Κατάσταση</th><th></th></tr>';
    table.appendChild(thead);
    const tbody=document.createElement('tbody');
    let visibleCount=0;
    schoolCsvRegistry.forEach(function(record,index){
      const hay=normalizeSchoolRegistrySearch([record.school_name,record.school_code,record.school_type_label,record.school_type,record.school_address].join(' '));
      if(query!=='' && !hay.includes(query)) return;
      visibleCount++;
      const tr=document.createElement('tr');
      const displaySchoolType=(window.EducationSchoolCsv&&typeof window.EducationSchoolCsv.typeLabel==='function')?window.EducationSchoolCsv.typeLabel(record.school_type):(record.school_type_label||record.school_type);
      const knownSoon=schoolCsvKnownPlaceholder(record.school_type);
      let status='';
      if(record.supported){
        if(record.directory_only) status='Ταυτότητα μόνο · συμπλήρωσε τμήματα';
        else if(record.pending_fields) status='Δομικά στοιχεία 2026-27 · θέλει συμπλήρωση';
        else status='Έτοιμο για φόρτωση';
      }else status=knownSoon?'Προσεχώς':'Μη αναγνωρισμένο είδος';
      const statusClass=record.supported?'':(knownSoon?'school-type-soon':'school-type-unknown');
      const schoolTd=document.createElement('td');
      schoolTd.textContent=String(record.school_name||record.school_id||('Σχολείο '+(index+1)));
      if(record.school_address){
        const address=document.createElement('small');
        address.className='school-address';
        address.textContent=record.school_address;
        schoolTd.appendChild(address);
      }
      tr.appendChild(schoolTd);
      [record.school_code||'—',displaySchoolType,record.general_a,record.general_b,record.general_c,schoolCsvTotalSections(record)].forEach(function(value){const td=document.createElement('td');td.textContent=String(value==null?'':value);tr.appendChild(td);});
      const statusTd=document.createElement('td');
      statusTd.className=statusClass;
      statusTd.textContent=status;
      if(record.completeness_status || record.pending_fields){
        statusTd.title=[record.completeness_status,record.pending_fields?('Εκκρεμούν: '+record.pending_fields):''].filter(Boolean).join(' · ');
      }
      tr.appendChild(statusTd);
      const actionTd=document.createElement('td');
      const button=document.createElement('button');
      button.type='button';
      button.className='edu-btn-secondary school-load-btn';
      button.textContent=record.supported?'Φόρτωση':'Ανενεργό';
      button.disabled=!record.supported;
      button.dataset.schoolRegistryIndex=String(index);
      actionTd.appendChild(button);
      tr.appendChild(actionTd);
      tbody.appendChild(tr);
    });
    if(visibleCount===0){
      const tr=document.createElement('tr');
      const td=document.createElement('td');
      td.colSpan=9;
      td.className='empty-personnel';
      td.textContent='Δεν βρέθηκε σχολική μονάδα με αυτό το κριτήριο.';
      tr.appendChild(td);
      tbody.appendChild(tr);
    }
    table.appendChild(tbody);
    schoolCsvPreview.appendChild(table);
  }
  function loadSchoolRegistryRecord(record){
    if(!record || !record.supported || !schoolProfileForm || !window.EducationSchoolCsv) return;
    if(schoolCsvTotalSections(record)>maxBasicSections){
      schoolCsvSetStatus('Δεν φορτώθηκε το «'+(record.school_name||record.school_id)+'»: το σύνολο των βασικών τμημάτων της σχολικής μονάδας υπερβαίνει τα '+maxBasicSections+' βασικά τμήματα.','error');
      return;
    }
    const values=window.EducationSchoolCsv.schoolToFormValues(record);
    Object.keys(values).forEach(function(name){
      const field=schoolProfileForm.elements.namedItem(name);
      if(field) field.value=values[name];
    });
    sync();
    syncSplitMaximums();
    syncLanguageGroupMaximums();
    syncBasicSectionLimit();
    const techPanel=document.getElementById('technologyInformaticsPanel');
    if(techPanel){
      techPanel.open=(record.school_type==='gymnasio' || record.school_type==='gymnasio_lt') && ['tech_split_a','tech_split_b','tech_split_c'].some(function(key){return (parseInt(record[key]||0,10)||0)>0;});
    }
    const ethicsPanel=document.getElementById('ethicsPanel')||document.getElementById('ethicsPanelGym');
    if(ethicsPanel){
      ethicsPanel.open=['a','b','c'].some(function(g){return record['ethics_'+g+'_exempt']!=='' || record['ethics_'+g+'_timely']!=='' || record['ethics_'+g+'_equivalent']!=='';});
    }
    const ethicsPanelLt=document.getElementById('ethicsPanelLt');
    if(ethicsPanelLt){
      ethicsPanelLt.open=['a','b','c'].some(function(g){return record['lt_ethics_'+g+'_exempt']!=='' || record['lt_ethics_'+g+'_timely']!=='' || record['lt_ethics_'+g+'_equivalent']!=='';});
    }
    if(schoolCsvActive){
      schoolCsvActive.hidden=false;
      const displaySchoolType=(window.EducationSchoolCsv&&typeof window.EducationSchoolCsv.typeLabel==='function')?window.EducationSchoolCsv.typeLabel(record.school_type):(record.school_type_label||record.school_type);
      const profileYear=record.school_year?' · '+escapeHtml(record.school_year):'';
      const pending=record.pending_fields?' <br><strong>Προς συμπλήρωση:</strong> '+escapeHtml(record.pending_fields)+'.':'';
      schoolCsvActive.innerHTML='<strong>Τρέχουσα εγγραφή μητρώου:</strong> '+escapeHtml(record.school_name||record.school_id)+(record.school_code?' · κωδ. '+escapeHtml(record.school_code):'')+' · '+escapeHtml(displaySchoolType)+profileYear+(record.school_address?' · '+escapeHtml(record.school_address):'')+'. Τα διαθέσιμα στοιχεία τμημάτων/ομάδων φορτώθηκαν στη φόρμα χωρίς server request.'+pending+' Πάτησε «Υπολόγισε διδακτικές ανάγκες» όταν θέλεις νέο υπολογισμό.';
    }
    schoolCsvSetStatus('Φορτώθηκε το «'+(record.school_name||record.school_id)+'» με τα διαθέσιμα δομικά στοιχεία του 2026-2027.'+(record.pending_fields?' Συμπλήρωσε τα πεδία που παραμένουν εκκρεμή.':'') ,'success');
    refreshStaffingContextFromForm('Χρειάζεται υπολογισμός');
    markSchoolProfileDirty();
  }
  function escapeHtml(value){
    return String(value==null?'':value).replace(/[&<>"']/g,function(ch){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch];});
  }
  function schoolCsvImporterSupportsRegistry(){
    return !!(window.EducationSchoolCsv && window.EducationSchoolCsv.supportsMultipleSchools===true && window.EducationSchoolCsv.registrySchemaVersion==='school_registry_v1');
  }
  function decodeSchoolCsvBuffer(buffer){
    let text='';
    try{text=new TextDecoder('utf-8',{fatal:false}).decode(buffer);}catch(e){text='';}
    if(text.indexOf('\uFFFD')>=0){
      try{const alt=new TextDecoder('windows-1253').decode(buffer); if(alt && alt.indexOf('\uFFFD')<0) text=alt;}catch(e){}
    }
    return text.replace(/^\uFEFF/,'');
  }
  function parseSchoolCsvFile(file){
    if(!file || !schoolCsvImporterSupportsRegistry()){
      schoolCsvSetStatus('Δεν φορτώθηκε η έκδοση του CSV importer που υποστηρίζει το school_registry_v1. Κάνε ανανέωση της σελίδας και δοκίμασε ξανά.','error');
      return;
    }
    const reader=new FileReader();
    reader.onload=function(){
      try{
        const parsed=window.EducationSchoolCsv.parse(decodeSchoolCsvBuffer(reader.result));
        const mapping=window.EducationSchoolCsv.autoMap(parsed.headers);
        if(!mapping.school_name || !mapping.school_type){
          schoolCsvRegistry=[];
          renderSchoolCsvRegistry();
          schoolCsvSetStatus('Δεν βρέθηκαν οι απαιτούμενες στήλες «Ονομασία σχολείου» και «Είδος σχολείου». Χρησιμοποίησε το πρότυπο school_registry_v1 ή αντίστοιχες ονομασίες στηλών.','error');
          return;
        }
        const identityOnly=!mapping.general_a && !mapping.general_b && !mapping.general_c;
        schoolCsvRegistry=parsed.rows.map(function(row,index){const record=window.EducationSchoolCsv.rowToSchool(row,mapping,index);record.directory_only=identityOnly;return record;});
        const registryProblems=schoolRegistryValidationProblems(schoolCsvRegistry);
        if(schoolRegistryHasProblems(registryProblems)){
          schoolCsvRegistry=[];
          renderSchoolCsvRegistry();
          schoolCsvSetStatus('Το CSV απορρίφθηκε: '+schoolRegistryProblemsMessage(registryProblems)+'. Κάθε σχολική μονάδα πρέπει να έχει μοναδικό αναγνωριστικό και μοναδικό πραγματικό κωδικό, ενώ το σύνολο Α΄ + Β΄ + Γ΄ δεν μπορεί να υπερβαίνει τα '+maxBasicSections+' βασικά τμήματα.','error');
          return;
        }
        persistSchoolCsvRegistry();
        renderSchoolCsvRegistry();
        const supported=schoolCsvRegistry.filter(function(r){return r.supported;}).length;
        const soon=schoolCsvRegistry.filter(function(r){return !r.supported && schoolCsvKnownPlaceholder(r.school_type);}).length;
        if(schoolCsvMeta) schoolCsvMeta.textContent=file.name+' · '+schoolCsvRegistry.length+' σχολικές μονάδες · delimiter '+(parsed.delimiter==='\t'?'tab':parsed.delimiter);
        schoolCsvSetStatus('Διαβάστηκαν '+schoolCsvRegistry.length+' σχολικές μονάδες: '+supported+' μπορούν να φορτωθούν τώρα'+(soon?' και '+soon+' ανήκουν σε προσωρινά ανενεργούς τύπους.':'.'),'success');
      }catch(error){
        schoolCsvRegistry=[];
        renderSchoolCsvRegistry();
        schoolCsvSetStatus('Αποτυχία ανάγνωσης CSV: '+(error&&error.message?error.message:'άγνωστο σφάλμα')+'.','error');
      }
    };
    reader.onerror=function(){schoolCsvSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου CSV.','error');};
    reader.readAsArrayBuffer(file);
  }
  function schoolRegistryIdentity(record){
    return String((record&&record.school_code)||(record&&record.school_id)||'').trim();
  }
  function loadBuiltinSchoolDirectory(directoryId){
    if(!schoolCsvImporterSupportsRegistry() || typeof window.EducationSchoolCsv.getBuiltinDirectory!=='function'){
      schoolCsvSetStatus('Δεν είναι διαθέσιμος ο ενσωματωμένος κατάλογος σχολικών μονάδων. Κάνε ανανέωση της σελίδας και δοκίμασε ξανά.','error');
      return;
    }
    const directory=window.EducationSchoolCsv.getBuiltinDirectory(directoryId);
    if(!directory || !Array.isArray(directory.schools)){
      schoolCsvSetStatus('Δεν βρέθηκε ο ζητούμενος ενσωματωμένος κατάλογος.','error');
      return;
    }
    const existingByIdentity=new Map();
    schoolCsvRegistry.forEach(function(record){
      const id=schoolRegistryIdentity(record);
      if(id) existingByIdentity.set(id,record);
    });
    const used=new Set();
    const merged=directory.schools.map(function(record){
      const id=schoolRegistryIdentity(record);
      const existing=id?existingByIdentity.get(id):null;
      if(!existing) return record;
      used.add(id);
      /* A previous built-in/identity-only registry must refresh to the richer 2026-2027 dataset.
         A genuinely imported populated CSV keeps the user's values and only inherits missing directory metadata. */
      if(existing.directory_id===directoryId || existing.directory_only===true || schoolCsvTotalSections(existing)===0){
        const refreshed=Object.assign({},existing,record);
        refreshed.school_id=existing.school_id||record.school_id;
        refreshed.directory_id=directoryId;
        return refreshed;
      }
      const combined=Object.assign({},record,existing);
      combined.school_id=existing.school_id||record.school_id;
      combined.school_code=existing.school_code||record.school_code;
      combined.school_name=existing.school_name||record.school_name;
      combined.school_address=existing.school_address||record.school_address;
      combined.school_type=record.school_type;
      combined.school_type_label=record.school_type_label;
      combined.supported=record.supported;
      combined.directory_only=false;
      combined.directory_id=directoryId;
      combined.registry_dataset_version=directory.dataset_version||record.registry_dataset_version||'';
      return combined;
    });
    schoolCsvRegistry.forEach(function(record){
      const id=schoolRegistryIdentity(record);
      if(!id || !used.has(id) && !directory.schools.some(function(item){return schoolRegistryIdentity(item)===id;})) merged.push(record);
    });
    schoolCsvRegistry=merged;
    persistSchoolCsvRegistry();
    if(schoolRegistrySearch) schoolRegistrySearch.value='';
    renderSchoolCsvRegistry();
    if(schoolCsvPanel) schoolCsvPanel.hidden=false;
    const supported=schoolCsvRegistry.filter(function(r){return r.supported;}).length;
    const withSections=directory.schools.filter(function(r){return schoolCsvTotalSections(r)>0;}).length;
    if(schoolCsvMeta) schoolCsvMeta.textContent=directory.label+' · '+directory.schools.length+' σχολικές μονάδες · πλήρες μητρώο 2026-2027 · πραγματικοί κωδικοί · ανάκτηση '+directory.retrieved_on;
    schoolCsvSetStatus('Φορτώθηκε ο εμπλουτισμένος κατάλογος '+directory.label+' με '+directory.schools.length+' σχολικές μονάδες. Σε '+withSections+' μονάδες υπάρχουν ήδη διαθέσιμα στοιχεία βασικών τμημάτων και, όπου προβλέπονται, χωρισμών, ομάδων προσανατολισμού και πραγματικών ομάδων 2ης ξένης γλώσσας από myschool stat3_10. '+supported+' εγγραφές είναι τύπων που υποστηρίζονται σήμερα· τα υπόλοιπα μη διαθέσιμα πεδία παραμένουν κενά για συμπλήρωση και οι υπόλοιποι τύποι παραμένουν προσωρινά ανενεργοί.','success');
  }
  function openSchoolCsvPicker(){
    if(!schoolCsvFile) return;
    schoolCsvFile.value='';
    schoolCsvFile.click();
  }
  if(openSchoolCsv && schoolCsvPanel){
    openSchoolCsv.addEventListener('click',function(){schoolCsvPanel.hidden=false;if(!schoolCsvRegistry.length) openSchoolCsvPicker();});
  }
  if(chooseSchoolCsvFile) chooseSchoolCsvFile.addEventListener('click',openSchoolCsvPicker);
  if(loadCorfuSchoolDirectory) loadCorfuSchoolDirectory.addEventListener('click',function(){loadBuiltinSchoolDirectory('dde_corfu_2026');});
  if(schoolRegistrySearch) schoolRegistrySearch.addEventListener('input',renderSchoolCsvRegistry);
  if(schoolCsvFile){
    schoolCsvFile.addEventListener('change',function(){if(schoolCsvFile.files && schoolCsvFile.files[0]) parseSchoolCsvFile(schoolCsvFile.files[0]);});
  }
  if(clearSchoolCsvRegistry){
    clearSchoolCsvRegistry.addEventListener('click',function(){
      schoolCsvRegistry=[];
      persistSchoolCsvRegistry();
      renderSchoolCsvRegistry();
      if(schoolCsvMeta) schoolCsvMeta.textContent='Δεν υπάρχει ενεργό μητρώο σχολικών μονάδων.';
      schoolCsvSetStatus('Το προσωρινό μητρώο σχολικών μονάδων καθαρίστηκε.','');
      if(schoolCsvActive) schoolCsvActive.hidden=true;
    });
  }
  if(closeSchoolCsv && schoolCsvPanel){closeSchoolCsv.addEventListener('click',function(){schoolCsvPanel.hidden=true;});}
  if(schoolCsvPreview){
    schoolCsvPreview.addEventListener('click',function(event){
      const button=event.target.closest('[data-school-registry-index]');
      if(!button) return;
      const index=parseInt(button.dataset.schoolRegistryIndex||'-1',10);
      if(index>=0 && schoolCsvRegistry[index]) loadSchoolRegistryRecord(schoolCsvRegistry[index]);
    });
  }
  if(restoreSchoolCsvRegistry()){
    renderSchoolCsvRegistry();
    if(schoolCsvMeta) schoolCsvMeta.textContent='Προσωρινό μητρώο browser · '+schoolCsvRegistry.length+' σχολικές μονάδες · school_registry_v1';
    schoolCsvSetStatus('Το μητρώο αποκαταστάθηκε από την τρέχουσα καρτέλα του browser. Μπορείς να φορτώσεις άλλο σχολείο χωρίς να επιλέξεις ξανά το CSV.','success');
  }

  function csvSpreadsheetSafeText(value){
    const text=String(value==null?'':value);
    if(typeof value==='number' || typeof value==='bigint') return text;
    const probe=text.replace(/^[\u0000-\u0020]+/,'');
    if(/^[=+\-@]/.test(probe) && !/^[+\-]?\d+(?:[.,]\d+)?$/.test(probe)) return "'"+text;
    return text;
  }
  function schoolCsvEscape(value){
    const text=csvSpreadsheetSafeText(value);
    return /[;"\r\n]/.test(text)?'"'+text.replace(/"/g,'""')+'"':text;
  }
  function downloadSchoolRegistryTemplate(){
    const headers=['Έκδοση μητρώου','Αναγνωριστικό σχολείου','Κωδικός Υπουργείου','Ονομασία σχολείου','Είδος σχολείου','Διεύθυνση σχολείου','Α τμήματα','Β τμήματα','Γ τμήματα','Α Γαλλικά ομάδες','Α Γερμανικά ομάδες','Α Ιταλικά ομάδες','Β Γαλλικά ομάδες','Β Γερμανικά ομάδες','Β Ιταλικά ομάδες','Γ Γαλλικά ομάδες','Γ Γερμανικά ομάδες','Γ Ιταλικά ομάδες','Α τμήματα άνω 21','Β τμήματα άνω 21','Γ τμήματα άνω 21','Β ομάδες Ανθρωπιστικών','Β ομάδες Θετικών','Γ ομάδες Ανθρωπιστικών','Γ ομάδες Θετικών Υγείας','Γ ομάδες Οικονομίας Πληροφορικής','Γ Μαθηματικά 2ου πεδίου','Γ Βιολογία 3ου πεδίου','Γ Μαθηματικά Γενικής Παιδείας','Γ Ιστορία Γενικής Παιδείας','Α απαλλασσόμενοι','Α Ηθική εντός 5ης','Α τμήματα Ηθικής','Β απαλλασσόμενοι','Β Ηθική εντός 5ης','Β τμήματα Ηθικής','Γ απαλλασσόμενοι','Γ Ηθική εντός 5ης','Γ τμήματα Ηθικής','ΛΤ Α Γενικής','ΛΤ Β Γενικής','ΛΤ Γ Γενικής','ΛΤ Α Γαλλικά ομάδες','ΛΤ Α Γερμανικά ομάδες','ΛΤ Β Γαλλικά ομάδες','ΛΤ Β Γερμανικά ομάδες','ΛΤ Β Ανθρωπιστικών','ΛΤ Β Θετικών','ΛΤ Γ Ανθρωπιστικών','ΛΤ Γ Θετικών Υγείας','ΛΤ Γ Οικονομίας Πληροφορικής','ΛΤ Γ Μαθηματικά 2ου πεδίου','ΛΤ Γ Βιολογία 3ου πεδίου','ΛΤ Γ Μαθηματικά Γενικής Παιδείας','ΛΤ Γ Ιστορία Γενικής Παιδείας','ΛΤ Α απαλλασσόμενοι','ΛΤ Α Ηθική εντός 5ης','ΛΤ Α τμήματα Ηθικής','ΛΤ Β απαλλασσόμενοι','ΛΤ Β Ηθική εντός 5ης','ΛΤ Β τμήματα Ηθικής','ΛΤ Γ απαλλασσόμενοι','ΛΤ Γ Ηθική εντός 5ης','ΛΤ Γ τμήματα Ηθικής'];
    const blank=new Array(headers.length).fill('');
    function exampleRow(values){ const row=blank.slice(); Object.keys(values).forEach(function(key){ const i=headers.indexOf(key); if(i>=0) row[i]=values[key]; }); return row; }
    const gym=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-001','Κωδικός Υπουργείου':'','Ονομασία σχολείου':'Παράδειγμα Γυμνασίου','Είδος σχολείου':'Ημερήσιο Γυμνάσιο','Α τμήματα':'2','Β τμήματα':'2','Γ τμήματα':'2','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Γ Γαλλικά ομάδες':'1','Γ Γερμανικά ομάδες':'1'});
    const gelRow=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-002','Κωδικός Υπουργείου':'','Ονομασία σχολείου':'Παράδειγμα ΓΕΛ','Είδος σχολείου':'Ημερήσιο Γενικό Λύκειο','Α τμήματα':'3','Β τμήματα':'2','Γ τμήματα':'3','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Β ομάδες Ανθρωπιστικών':'1','Β ομάδες Θετικών':'1','Γ ομάδες Ανθρωπιστικών':'1','Γ ομάδες Θετικών Υγείας':'2','Γ ομάδες Οικονομίας Πληροφορικής':'1','Γ Μαθηματικά 2ου πεδίου':'1','Γ Βιολογία 3ου πεδίου':'1'});
    const composite=exampleRow({'Έκδοση μητρώου':'school_registry_v1','Αναγνωριστικό σχολείου':'school-003','Ονομασία σχολείου':'Παράδειγμα Γυμνασίου με Λ.Τ.','Είδος σχολείου':'Γυμνάσιο με Λ.Τ.','Α τμήματα':'2','Β τμήματα':'2','Γ τμήματα':'1','Α Γαλλικά ομάδες':'1','Α Γερμανικά ομάδες':'1','Β Γαλλικά ομάδες':'1','Β Γερμανικά ομάδες':'1','Γ Γαλλικά ομάδες':'1','ΛΤ Α Γενικής':'1','ΛΤ Β Γενικής':'1','ΛΤ Γ Γενικής':'1','ΛΤ Α Γαλλικά ομάδες':'1','ΛΤ Β Γερμανικά ομάδες':'1','ΛΤ Β Ανθρωπιστικών':'1','ΛΤ Β Θετικών':'1','ΛΤ Γ Ανθρωπιστικών':'1','ΛΤ Γ Θετικών Υγείας':'1','ΛΤ Γ Μαθηματικά 2ου πεδίου':'1','ΛΤ Γ Μαθηματικά Γενικής Παιδείας':'1','ΛΤ Γ Ιστορία Γενικής Παιδείας':'1'});
    const csv='\uFEFF'+[headers,gym,gelRow,composite].map(function(row){return row.map(schoolCsvEscape).join(';');}).join('\r\n');
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url; a.download='school_registry_v1-template.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  function downloadBuiltinSchoolDirectory(directoryId){
    if(!window.EducationSchoolCsv || typeof window.EducationSchoolCsv.getBuiltinDirectory!=='function') return;
    const directory=window.EducationSchoolCsv.getBuiltinDirectory(directoryId);
    if(!directory) return;
    let csv='';
    if(directory.raw_csv){
      csv='\uFEFF'+String(directory.raw_csv).replace(/^\uFEFF/,'');
    }else{
      const headers=['Έκδοση μητρώου','Αναγνωριστικό σχολείου','Κωδικός Υπουργείου','Ονομασία σχολείου','Είδος σχολείου','Διεύθυνση σχολείου'];
      const rows=directory.schools.map(function(record){return ['school_registry_v1',record.school_id||record.school_code,record.school_code,record.school_name,record.school_type_label||record.school_type,record.school_address||''];});
      csv='\uFEFF'+[headers].concat(rows).map(function(row){return row.map(schoolCsvEscape).join(';');}).join('\r\n');
    }
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url; a.download=directory.filename||'school_registry_v1-dde-kerkyras.csv';
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  if(downloadCorfuSchoolDirectory){downloadCorfuSchoolDirectory.addEventListener('click',function(){downloadBuiltinSchoolDirectory('dde_corfu_2026');});}
  if(downloadSchoolCsvTemplate){downloadSchoolCsvTemplate.addEventListener('click',downloadSchoolRegistryTemplate);}

  if(reset){ reset.addEventListener('click',function(){ setTimeout(function(){ type.value='gymnasio'; sync(); syncSplitMaximums(); syncLanguageGroupMaximums(); syncBasicSectionLimit(); if(schoolCsvActive) schoolCsvActive.hidden=true; },0); }); }
  const filter=document.getElementById('staffingResultFilter');
  if(filter){
    const rows=Array.from(document.querySelectorAll('.staffing-code-row'));
    filter.addEventListener('input',function(){
      const q=(filter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      rows.forEach(function(row){
        const hay=(row.getAttribute('data-search')||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
        row.hidden=q!=='' && !hay.includes(q);
      });
    });
  }

  const schoolProfileHasCalculatedResults=!!staffingRuntimeConfig.hasCalculatedResults;
  const schoolProfileStaleNotice=document.getElementById('schoolProfileStaleNotice');
  function markSchoolProfileDirty(){
    refreshStaffingContextFromForm(schoolProfileHasCalculatedResults?'Αλλαγμένα στοιχεία · υπολόγισε ξανά':'Χρειάζεται υπολογισμός');
    if(!schoolProfileHasCalculatedResults) return;
    ['results','personnel','allocation','vacancies','specialties'].forEach(function(name){
      const tab=document.querySelector('[data-staffing-tab="'+name+'"]');
      if(!tab) return;
      tab.disabled=true;
      tab.title='Τα στοιχεία της σχολικής μονάδας άλλαξαν. Υπολόγισε ξανά τις διδακτικές ανάγκες.';
    });
    if(schoolProfileStaleNotice) schoolProfileStaleNotice.hidden=false;
    activatePanel('school');
  }
  if(schoolProfileForm){
    schoolProfileForm.addEventListener('input',function(event){
      if(event.target && event.target.matches('input[name]:not([type="hidden"]), select[name]')) markSchoolProfileDirty();
    });
    schoolProfileForm.addEventListener('change',function(event){
      if(event.target && event.target.matches('input[name]:not([type="hidden"]), select[name]')) markSchoolProfileDirty();
    });
    schoolProfileForm.addEventListener('reset',function(){ setTimeout(markSchoolProfileDirty,0); });
  }

  const personnelList=document.getElementById('personnelList');
  const allocationTab=document.querySelector('[data-staffing-tab="allocation"]');
  const vacanciesTab=document.querySelector('[data-staffing-tab="vacancies"]');
  const specialtiesTab=document.querySelector('[data-staffing-tab="specialties"]');
  function markPersonnelDirty(){
    if(allocationTab){ allocationTab.disabled=true; allocationTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από νέα κατανομή.'; }
    if(vacanciesTab){ vacanciesTab.disabled=true; vacanciesTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από τον έλεγχο κενών.'; }
    if(specialtiesTab){ specialtiesTab.disabled=true; specialtiesTab.title='Υπολόγισε ξανά τα ωράρια προσωπικού πριν από τη δήλωση κενών / πλεονασμάτων ειδικοτήτων.'; }
  }
  const personnelTemplate=document.getElementById('personnelRowTemplate');
  const addPersonnel=document.getElementById('addPersonnelRow');
  const personnelFilter=document.getElementById('personnelFilter');
  const openPersonnelCsv=document.getElementById('openPersonnelCsv');
  const personnelCsvFile=document.getElementById('personnelCsvFile');
  const personnelCsvPanel=document.getElementById('personnelCsvPanel');
  const closePersonnelCsv=document.getElementById('closePersonnelCsv');
  const personnelCsvMeta=document.getElementById('personnelCsvMeta');
  const personnelCsvMappings=document.getElementById('personnelCsvMappings');
  const personnelCsvPreview=document.getElementById('personnelCsvPreview');
  const personnelCsvStatus=document.getElementById('personnelCsvStatus');
  const personnelCsvMode=document.getElementById('personnelCsvMode');
  const importPersonnelCsv=document.getElementById('importPersonnelCsv');
  const exportPersonnelRegistryCsv=document.getElementById('exportPersonnelRegistryCsv');
  const downloadPersonnelCsvTemplate=document.getElementById('downloadPersonnelCsvTemplate');
  const openMySchoolStaff=document.getElementById('openMySchoolStaff');
  const mySchoolStaffFile=document.getElementById('mySchoolStaffFile');
  const mySchoolStaffPanel=document.getElementById('mySchoolStaffPanel');
  const mySchoolStaffMeta=document.getElementById('mySchoolStaffMeta');
  const mySchoolStaffStatus=document.getElementById('mySchoolStaffStatus');
  const closeMySchoolStaff=document.getElementById('closeMySchoolStaff');
  const pickMySchoolStaffFile=document.getElementById('pickMySchoolStaffFile');
  const loadMySchoolStaffForSchool=document.getElementById('loadMySchoolStaffForSchool');
  const downloadCleanMySchoolStaff=document.getElementById('downloadCleanMySchoolStaff');
  const clearMySchoolStaff=document.getElementById('clearMySchoolStaff');
  let personnelCounter=Date.now();
  let personnelCsvData=null;
  let personnelCsvMapping={};

  const personnelCsvFields=[
    {key:'person_id',label:'Αναγνωριστικό εκπαιδευτικού'},
    {key:'specialty_code',label:'Κλάδος / ειδικότητα',required:true},
    {key:'secondary_specialty_code',label:'2η ειδικότητα'},
    {key:'display_name',label:'Ονοματεπώνυμο'},
    {key:'surname',label:'Επώνυμο'},
    {key:'given_name',label:'Όνομα'},
    {key:'required_teaching_hours',label:'Υποχρεωτικό διδακτικό ωράριο'},
    {key:'role',label:'Ρόλος'},
    {key:'service_years',label:'Έτη υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_months',label:'Μήνες υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_days',label:'Ημέρες υπηρεσίας (μόνο διοίκηση)'},
    {key:'service_combined',label:'Προϋπηρεσία ενιαία (μόνο διοίκηση)'},
    {key:'assigned_external_hours',label:'Ώρες σε άλλη μονάδα'},
    {key:'obligation_source',label:'Πηγή ωραρίου'},
    {key:'source_base_required_hours',label:'Υ.Ω. πηγής'},
    {key:'source_reduction_hours',label:'Μείωση πηγής'},
    {key:'source_hours_at_unit',label:'Ώρες Υ.Ω. στον φορέα'}
  ];

  function schoolGeneralSectionCount(){
    const typeEl=document.getElementById('school_type');
    const schoolType=typeEl ? typeEl.value : 'gymnasio';
    let prefixes=['gym_general_'];
    if(schoolType==='gel' || schoolType==='esperino_gel') prefixes=['gel_general_'];
    else if(schoolType==='gymnasio_lt') prefixes=['gym_general_','gel_general_'];
    return prefixes.reduce(function(total,prefix){
      return total+['a','b','c'].reduce(function(subtotal,suffix){
        const input=document.querySelector('[name="'+prefix+suffix+'"]');
        return subtotal+Math.max(0,parseInt(input&&input.value?input.value:'0',10)||0);
      },0);
    },0);
  }
  function directorSectionsBandFromCount(count){
    count=Math.max(0,parseInt(count,10)||0);
    if(count<3) return '';
    if(count<=5) return '3-5';
    if(count<=9) return '6-9';
    if(count<=12) return '10-12';
    return '13+';
  }
  function updateDirectorSectionInfo(row){
    const count=schoolGeneralSectionCount();
    const band=directorSectionsBandFromCount(count);
    const countEl=row.querySelector('[data-director-section-count]');
    const bandEl=row.querySelector('[data-director-section-band]');
    if(countEl) countEl.textContent=String(count);
    if(bandEl) bandEl.textContent=band ? 'κλίμακα '+band : 'χρειάζονται τα κανονικά τμήματα';
    return {count:count,band:band};
  }

  const singleDirectorMessage='Μπορεί να δηλωθεί μόνο ένας/μία Διευθυντής/ντρια στη σχολική μονάδα.';
  function refreshDirectorRoleConstraints(){
    if(!personnelList) return;
    const rows=Array.from(personnelList.querySelectorAll('[data-personnel-row]'));
    const directorRows=rows.filter(function(row){
      const role=row.querySelector('.personnel-role');
      return role && role.value==='director';
    });
    const hasDirector=directorRows.length>0;
    rows.forEach(function(row){
      const role=row.querySelector('.personnel-role');
      if(!role) return;
      const directorOption=role.querySelector('option[value="director"]');
      const isDirector=role.value==='director';
      const duplicateDirector=isDirector && directorRows.indexOf(row)>0;
      if(directorOption) directorOption.disabled=hasDirector && !isDirector;
      role.setCustomValidity(duplicateDirector?singleDirectorMessage:'');
      if(duplicateDirector) role.setAttribute('aria-invalid','true');
      else role.removeAttribute('aria-invalid');
      const error=row.querySelector('[data-personnel-error]');
      if(duplicateDirector && error){
        error.hidden=false;
        error.textContent=singleDirectorMessage;
      }else if(error && error.textContent===singleDirectorMessage){
        error.hidden=true;
        error.textContent='';
      }
    });
  }

  function updatePersonnelRow(row){
    if(!row) return;
    const specialty=row.querySelector('.personnel-specialty');
    const secondarySpecialty=row.querySelector('.personnel-secondary-specialty');
    const years=row.querySelector('.personnel-years');
    const months=row.querySelector('.personnel-months');
    const days=row.querySelector('.personnel-days');
    const role=row.querySelector('.personnel-role');
    const requiredInput=row.querySelector('.personnel-required');
    const external=row.querySelector('.personnel-external');
    const serviceWrap=row.querySelector('.personnel-service-fields');
    const directorBandWrap=row.querySelector('.personnel-director-band');
    const rule=row.querySelector('[data-personnel-rule]');
    const error=row.querySelector('[data-personnel-error]');
    const availableEl=row.querySelector('[data-available-hours]');
    const roleValue=role?role.value:'teacher';
    const managementRole=roleValue==='director'||roleValue==='vice_or_sector';
    const obligationSourceEl=row.querySelector('.personnel-obligation-source');
    const obligationSource=obligationSourceEl?String(obligationSourceEl.value||'').trim():'';
    const mySchoolSource=obligationSource==='myschool_stat4_8';
    if(serviceWrap) serviceWrap.hidden=!managementRole||mySchoolSource;
    if(directorBandWrap) directorBandWrap.hidden=roleValue!=='director'||mySchoolSource;
    if(requiredInput){
      if(mySchoolSource){
        requiredInput.readOnly=true;
        requiredInput.required=false;
        requiredInput.setCustomValidity('');
      }else if(managementRole){
        if(!requiredInput.readOnly) requiredInput.dataset.manualValue=requiredInput.value||'';
        requiredInput.readOnly=true;
        requiredInput.required=false;
        requiredInput.setCustomValidity('');
      }else{
        if(requiredInput.readOnly){
          requiredInput.readOnly=false;
          requiredInput.value=requiredInput.dataset.manualValue||'';
        }
        requiredInput.required=true;
      }
    }
    const code=specialty?specialty.value:'';
    const secondaryCode=secondarySpecialty?secondarySpecialty.value:'';
    const manualHoursMax=code.indexOf('ΠΕ')===0 ? 23 : 35;
    if(requiredInput && !managementRole) requiredInput.max=String(manualHoursMax);
    const name=(row.querySelector('.personnel-name')||{}).value||'';
    row.setAttribute('data-search',code+' '+secondaryCode+' '+name);
    if(!specialty || !specialty.value){
      if(managementRole && requiredInput) requiredInput.value='';
      if(availableEl) availableEl.textContent='—';
      if(rule) rule.textContent='';
      if(error){error.hidden=false;error.textContent='Επίλεξε κλάδο / ειδικότητα.';}
      return;
    }

    let required=0;
    if(mySchoolSource){
      const raw=requiredInput?String(requiredInput.value||'').trim():'';
      const parsedRaw=/^\d+$/.test(raw)?parseInt(raw,10):NaN;
      if(raw===''||!Number.isFinite(parsedRaw)||parsedRaw<1||parsedRaw>35||(code.indexOf('ΠΕ')===0&&parsedRaw>23)){
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent='Το ωράριο του myschool δεν είναι έγκυρο για τον συγκεκριμένο κλάδο.';}
        return;
      }
      required=parsedRaw;
      const baseEl=row.querySelector('.personnel-source-base-required');
      const reductionEl=row.querySelector('.personnel-source-reduction');
      const atUnitEl=row.querySelector('.personnel-source-at-unit');
      const base=Math.max(0,parseInt(baseEl&&baseEl.value?baseEl.value:'0',10)||0);
      const reduction=Math.max(0,parseInt(reductionEl&&reductionEl.value?reductionEl.value:'0',10)||0);
      const atUnit=Math.max(0,parseInt(atUnitEl&&atUnitEl.value?atUnitEl.value:'0',10)||0);
      if(rule) rule.textContent='myschool stat4_8 · Υ.Ω. '+base+' − μείωση '+reduction+' = '+required+' ώρες · ώρες Υ.Ω. στον φορέα '+atUnit+'.';
    }else if(managementRole){
      if(!window.EducationTeachingHours){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(error){error.hidden=false;error.textContent='Δεν φορτώθηκε ο υπολογισμός ωραρίου διοικητικών ρόλων.';}
        return;
      }
      const directorSectionInfo=updateDirectorSectionInfo(row);
      if(roleValue==='director' && !directorSectionInfo.band){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent='Για Διευθυντή/ντρια χρειάζονται τα δηλωμένα κανονικά τμήματα της σχολικής μονάδας.';}
        return;
      }
      const result=window.EducationTeachingHours.secondary({
        branch:'PE',
        role:roleValue,
        years:years?years.value:0,
        months:months?months.value:0,
        days:days?days.value:0,
        sections:directorSectionInfo.band
      });
      if(!result || !result.valid){
        if(requiredInput) requiredInput.value='';
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent=(result&&result.error)?result.error:'Δεν μπορεί να υπολογιστεί το ωράριο της διοικητικής θέσης.';}
        return;
      }
      required=Math.max(0,parseInt(result.hours,10)||0);
      if(requiredInput) requiredInput.value=String(required);
      if(rule) rule.textContent=result.rule||'';
    }else{
      const raw=requiredInput?String(requiredInput.value||'').trim():'';
      const parsedRaw=/^\d+$/.test(raw) ? parseInt(raw,10) : NaN;
      const invalidMessage=code.indexOf('ΠΕ')===0 && Number.isFinite(parsedRaw) && parsedRaw>23
        ? 'Για κλάδο ΠΕ το υποχρεωτικό διδακτικό ωράριο δεν μπορεί να ξεπερνά τις 23 ώρες.'
        : 'Το υποχρεωτικό ωράριο πρέπει να είναι ακέραιος αριθμός από 1 έως '+manualHoursMax+' ώρες.';
      if(raw==='' || !Number.isFinite(parsedRaw) || parsedRaw<1 || parsedRaw>manualHoursMax){
        if(requiredInput) requiredInput.setCustomValidity(raw===''?'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο.':invalidMessage);
        if(availableEl) availableEl.textContent='—';
        if(rule) rule.textContent='';
        if(error){error.hidden=false;error.textContent=raw===''?'Συμπλήρωσε το υποχρεωτικό διδακτικό ωράριο.':invalidMessage;}
        return;
      }
      required=parseInt(raw,10);
      requiredInput.setCustomValidity('');
      requiredInput.dataset.manualValue=String(required);
      if(rule) rule.textContent='Το υποχρεωτικό διδακτικό ωράριο δηλώνεται απευθείας από τον χρήστη.';
    }

    if(external) external.readOnly=mySchoolSource;
    const ext=Math.max(0,parseInt(external&&external.value?external.value:'0',10)||0);
    if(availableEl) availableEl.textContent=String(Math.max(0,required-ext));
    if(error){
      if(ext>required){error.hidden=false;error.textContent='Οι ώρες σε άλλη μονάδα υπερβαίνουν το υποχρεωτικό ωράριο κατά '+(ext-required)+' ώρες.';}
      else{error.hidden=true;error.textContent='';}
    }
  }

  function bindPersonnelRow(row){
    if(!row || row.dataset.initialized==='1') return;
    row.dataset.initialized='1';
    const hiddenId=row.querySelector('input[name="personnel_person_id[]"]');
    if(hiddenId && !hiddenId.value){ personnelCounter+=1; hiddenId.value='person-'+personnelCounter; }
    updatePersonnelRow(row);
    refreshDirectorRoleConstraints();
  }
  if(personnelList && personnelList.dataset.eventsBound!=='1'){
    personnelList.dataset.eventsBound='1';
    personnelList.addEventListener('input',function(event){
      if(!event.target.matches('input:not([type="hidden"])')) return;
      const row=event.target.closest('[data-personnel-row]');
      if(!row) return;
      updatePersonnelRow(row); markPersonnelDirty();
    });
    personnelList.addEventListener('change',function(event){
      if(!event.target.matches('select')) return;
      const row=event.target.closest('[data-personnel-row]');
      if(!row) return;
      updatePersonnelRow(row);
      if(event.target.matches('.personnel-role')) refreshDirectorRoleConstraints();
      markPersonnelDirty();
    });
    personnelList.addEventListener('click',function(event){
      const remove=event.target.closest('.personnel-remove');
      if(!remove || !personnelList.contains(remove)) return;
      const row=remove.closest('[data-personnel-row]');
      if(!row) return;
      const idInput=row.querySelector('input[name="personnel_person_id[]"]');
      const personId=idInput?idInput.value:'';
      const nameInput=row.querySelector('.personnel-name');
      const specialtyInput=row.querySelector('.personnel-specialty');
      const personLabel=((specialtyInput&&specialtyInput.value?specialtyInput.value+' · ':'')+(nameInput&&nameInput.value?nameInput.value:'τον/την εκπαιδευτικό')).trim();
      let linkedAllocationRows=[];
      if(personId){
        linkedAllocationRows=Array.from(document.querySelectorAll('[data-allocation-row]')).filter(function(allocationRow){
          const personSelect=allocationRow.querySelector('.allocation-person');
          return personSelect&&personSelect.value===personId;
        });
      }
      let message='Να αφαιρεθεί '+personLabel+' από το προσωπικό της σχολικής μονάδας;';
      if(linkedAllocationRows.length){
        message+='\n\nΥπάρχουν '+linkedAllocationRows.length+' γραμμές κατανομής που έχουν ανατεθεί σε αυτόν/ήν. Θα αφαιρεθούν και αυτές.';
      }
      if(!window.confirm(message)) return;
      linkedAllocationRows.forEach(function(allocationRow){allocationRow.remove();});
      row.remove();
      refreshDirectorRoleConstraints(); markPersonnelDirty(); ensurePersonnelEmptyState();
      if(typeof updateAllocationSummary==='function') updateAllocationSummary();
    });
  }
  function clearPersonnelRows(){
    if(!personnelList) return;
    personnelList.querySelectorAll('[data-personnel-row]').forEach(function(row){row.remove();});
    const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
    refreshDirectorRoleConstraints();
  }
  function ensurePersonnelEmptyState(){
    if(!personnelList || personnelList.querySelector('[data-personnel-row]')) return;
    const empty=document.createElement('div'); empty.id='emptyPersonnelState'; empty.className='empty-personnel'; empty.textContent='Δεν έχει προστεθεί ακόμη εκπαιδευτικός. Πάτησε «+ Προσθήκη εκπαιδευτικού» ή «Εισαγωγή CSV» για να ξεκινήσεις.'; personnelList.appendChild(empty);
  }
  function ensureImportedEaeSpecialtyOption(select,code){
    if(!select||!/\.50$/.test(String(code||''))) return false;
    const exists=Array.from(select.options||[]).some(function(opt){return opt.value===code;});
    if(exists){select.value=code;return true;}
    const option=document.createElement('option');
    option.value=code;
    option.textContent=code+' — Ειδική Αγωγή';
    select.appendChild(option);
    select.value=code;
    return true;
  }
  function addPersonnelFromData(person){
    if(!personnelTemplate || !personnelList) return {ok:false,unknownCode:false};
    const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
    const fragment=personnelTemplate.content.cloneNode(true);
    const row=fragment.querySelector('[data-personnel-row]');
    const hiddenId=row.querySelector('input[name="personnel_person_id[]"]');
    const sourceEl=row.querySelector('.personnel-obligation-source'); if(sourceEl) sourceEl.value=person.obligation_source||person.source_kind||'';
    const sourceBaseEl=row.querySelector('.personnel-source-base-required'); if(sourceBaseEl) sourceBaseEl.value=person.source_base_required_hours==null?'':String(person.source_base_required_hours);
    const sourceReductionEl=row.querySelector('.personnel-source-reduction'); if(sourceReductionEl) sourceReductionEl.value=person.source_reduction_hours==null?'':String(person.source_reduction_hours);
    const sourceAtUnitEl=row.querySelector('.personnel-source-at-unit'); if(sourceAtUnitEl) sourceAtUnitEl.value=person.source_hours_at_unit==null?'':String(person.source_hours_at_unit);
    const requestedId=String(person.person_id||'').trim();
    if(hiddenId && requestedId){
      const duplicateId=Array.from(personnelList.querySelectorAll('input[name="personnel_person_id[]"]')).some(function(input){return input.value===requestedId;});
      if(!duplicateId) hiddenId.value=requestedId;
    }
    const specialty=row.querySelector('.personnel-specialty');
    const code=person.specialty_code||'';
    let matched=false;
    if(specialty && code){
      Array.from(specialty.options).forEach(function(opt){ if(opt.value===code){ specialty.value=code; matched=true; } });
      if(!matched) matched=ensureImportedEaeSpecialtyOption(specialty,code);
    }
    const secondarySpecialty=row.querySelector('.personnel-secondary-specialty');
    const secondaryCode=person.secondary_specialty_code||'';
    let secondaryMatched=false;
    if(secondarySpecialty && secondaryCode){
      Array.from(secondarySpecialty.options).forEach(function(opt){ if(opt.value===secondaryCode){ secondarySpecialty.value=secondaryCode; secondaryMatched=true; } });
      if(!secondaryMatched) secondaryMatched=ensureImportedEaeSpecialtyOption(secondarySpecialty,secondaryCode);
    }
    const name=row.querySelector('.personnel-name'); if(name) name.value=person.display_name||'';
    const years=row.querySelector('.personnel-years'); if(years) years.value=String(person.service_years||0);
    const months=row.querySelector('.personnel-months'); if(months) months.value=String(person.service_months||0);
    const days=row.querySelector('.personnel-days'); if(days) days.value=String(person.service_days||0);
    const required=row.querySelector('.personnel-required'); if(required){ required.value=person.required_teaching_hours==null?'':String(person.required_teaching_hours); required.dataset.manualValue=required.value; }
    const role=row.querySelector('.personnel-role'); if(role) role.value=person.role||'teacher';
    const external=row.querySelector('.personnel-external'); if(external) external.value=String(person.assigned_external_hours||0);
    personnelList.appendChild(fragment);
    bindPersonnelRow(row);
    if(code && !matched){
      const error=row.querySelector('[data-personnel-error]');
      if(error){ error.hidden=false; error.textContent='Ο κλάδος «'+code+'» του CSV δεν αναγνωρίζεται από τις διαθέσιμες αναθέσεις. Επίλεξε κλάδο χειροκίνητα.'; }
    }
    if(secondaryCode && !secondaryMatched){
      const error=row.querySelector('[data-personnel-error]');
      if(error){ error.hidden=false; error.textContent='Η 2η ειδικότητα «'+secondaryCode+'» του CSV δεν αναγνωρίζεται. Επίλεξέ την χειροκίνητα.'; }
    }
    return {ok:true,unknownCode:!!((code&&!matched)||(secondaryCode&&!secondaryMatched))};
  }
  function personnelCsvSetStatus(message,type){
    if(!personnelCsvStatus) return;
    personnelCsvStatus.textContent=message||'';
    personnelCsvStatus.classList.toggle('is-error',type==='error');
    personnelCsvStatus.classList.toggle('is-success',type==='success');
  }
  function personnelCsvSelectOptions(select,headers,selected){
    select.innerHTML='';
    const empty=document.createElement('option'); empty.value=''; empty.textContent='— δεν χρησιμοποιείται —'; select.appendChild(empty);
    headers.forEach(function(header){ const opt=document.createElement('option'); opt.value=header; opt.textContent=header; if(header===selected) opt.selected=true; select.appendChild(opt); });
  }
  function personnelCsvCurrentMapping(){
    const map={};
    if(personnelCsvMappings) personnelCsvMappings.querySelectorAll('select[data-csv-map]').forEach(function(select){ if(select.value) map[select.getAttribute('data-csv-map')]=select.value; });
    return map;
  }
  function renderPersonnelCsvMappings(){
    if(!personnelCsvData || !personnelCsvMappings || !window.EducationPersonnelCsv) return;
    const auto=window.EducationPersonnelCsv.autoMap(personnelCsvData.headers);
    personnelCsvMappings.innerHTML='';
    personnelCsvFields.forEach(function(field){
      const wrap=document.createElement('div'); wrap.className='field';
      const label=document.createElement('label'); label.textContent=field.label+(field.required?' *':'');
      const select=document.createElement('select'); select.setAttribute('data-csv-map',field.key);
      personnelCsvSelectOptions(select,personnelCsvData.headers,auto[field.key]||'');
      wrap.appendChild(label); wrap.appendChild(select); personnelCsvMappings.appendChild(wrap);
    });
    personnelCsvMapping=personnelCsvCurrentMapping();
  }
  function renderPersonnelCsvPreview(){
    if(!personnelCsvPreview || !personnelCsvData) return;
    const headers=personnelCsvData.headers;
    const rows=personnelCsvData.rows.slice(0,5);
    if(!rows.length){ personnelCsvPreview.hidden=true; personnelCsvPreview.innerHTML=''; return; }
    const table=document.createElement('table');
    const thead=document.createElement('thead'); const hr=document.createElement('tr');
    headers.forEach(function(h){const th=document.createElement('th');th.textContent=h;hr.appendChild(th);}); thead.appendChild(hr); table.appendChild(thead);
    const tbody=document.createElement('tbody');
    rows.forEach(function(r){const tr=document.createElement('tr');headers.forEach(function(h){const td=document.createElement('td');td.textContent=r[h]||'';tr.appendChild(td);});tbody.appendChild(tr);});
    table.appendChild(tbody); personnelCsvPreview.innerHTML=''; personnelCsvPreview.appendChild(table); personnelCsvPreview.hidden=false;
  }
  function personnelCsvImporterSupportsRegistry(){
    return !!(window.EducationPersonnelCsv
      && window.EducationPersonnelCsv.supportsManualRequiredTeachingHours===true
      && window.EducationPersonnelCsv.supportsSecondarySpecialty===true);
  }
  function validatePersonnelCsvImport(){
    if(!personnelCsvImporterSupportsRegistry()){
      if(importPersonnelCsv) importPersonnelCsv.disabled=true;
      personnelCsvSetStatus('Έχει φορτωθεί παλαιότερη cached έκδοση του CSV importer. Κάνε ανανέωση της σελίδας για να φορτωθεί η έκδοση που υποστηρίζει το portable μητρώο και τη 2η ειδικότητα.','error');
      return;
    }
    const ready=!!(personnelCsvData && personnelCsvData.rows.length && personnelCsvMapping.specialty_code);
    if(importPersonnelCsv) importPersonnelCsv.disabled=!ready;
    if(personnelCsvData && !personnelCsvMapping.specialty_code) personnelCsvSetStatus('Χρειάζεται αντιστοίχιση της στήλης «Κλάδος / ειδικότητα».','error');
    else if(personnelCsvData) personnelCsvSetStatus('Έτοιμο για εισαγωγή. Θα εισαχθούν έως '+personnelCsvData.rows.length+' εγγραφές.','');
  }
  if(personnelCsvMappings && personnelCsvMappings.dataset.eventsBound!=='1'){
    personnelCsvMappings.dataset.eventsBound='1';
    personnelCsvMappings.addEventListener('change',function(event){
      if(!event.target.matches('select[data-csv-map]')) return;
      personnelCsvMapping=personnelCsvCurrentMapping(); renderPersonnelCsvPreview(); validatePersonnelCsvImport();
    });
  }
  function decodePersonnelCsvBuffer(buffer){
    let text='';
    try{text=new TextDecoder('utf-8',{fatal:false}).decode(buffer);}catch(e){text='';}
    if(text.indexOf('\uFFFD')>=0){
      try{const alt=new TextDecoder('windows-1253').decode(buffer); if(alt && alt.indexOf('\uFFFD')<0) text=alt;}catch(e){}
    }
    return text.replace(/^\uFEFF/,'');
  }
  function openCsvPicker(){ if(personnelCsvFile){ personnelCsvFile.value=''; personnelCsvFile.click(); } }
  if(openPersonnelCsv) openPersonnelCsv.addEventListener('click',openCsvPicker);
  if(closePersonnelCsv) closePersonnelCsv.addEventListener('click',function(){ if(personnelCsvPanel) personnelCsvPanel.hidden=true; });
  if(personnelCsvFile){
    personnelCsvFile.addEventListener('change',function(){
      const file=personnelCsvFile.files && personnelCsvFile.files[0]; if(!file) return;
      const reader=new FileReader();
      reader.onload=function(){
        if(!window.EducationPersonnelCsv){ personnelCsvSetStatus('Δεν φορτώθηκε ο μηχανισμός ανάγνωσης CSV.','error'); return; }
        if(!personnelCsvImporterSupportsRegistry()){
          personnelCsvSetStatus('Έχει φορτωθεί παλαιότερη cached έκδοση του CSV importer. Κάνε ανανέωση της σελίδας και επίλεξε ξανά το αρχείο.','error');
          if(importPersonnelCsv) importPersonnelCsv.disabled=true;
          return;
        }
        const text=decodePersonnelCsvBuffer(reader.result);
        personnelCsvData=window.EducationPersonnelCsv.parse(text);
        if(personnelCsvPanel) personnelCsvPanel.hidden=false;
        if(personnelCsvMeta){
          const delim=personnelCsvData.delimiter==='\t'?'tab':personnelCsvData.delimiter;
          personnelCsvMeta.textContent=file.name+' · '+personnelCsvData.rows.length+' εγγραφές · διαχωριστικό «'+delim+'»';
        }
        renderPersonnelCsvMappings(); renderPersonnelCsvPreview(); validatePersonnelCsvImport();
      };
      reader.onerror=function(){ personnelCsvSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου.','error'); };
      reader.readAsArrayBuffer(file);
    });
  }
  let mySchoolStaffRegistry=(window.EducationMySchoolStaff&&window.EducationMySchoolStaff.loadSession)?window.EducationMySchoolStaff.loadSession():null;
  function currentSchoolCodeForStaff(){
    const el=document.getElementById('school_code');
    return window.EducationMySchoolStaff?window.EducationMySchoolStaff.normalizeSchoolCode(el?el.value:''):String(el&&el.value||'').trim();
  }
  function mySchoolStaffSetStatus(message,type){
    if(!mySchoolStaffStatus) return;
    mySchoolStaffStatus.textContent=message||'';
    mySchoolStaffStatus.className='personnel-csv-status'+(type?' is-'+type:'');
  }
  function refreshMySchoolStaffPanel(){
    const code=currentSchoolCodeForStaff();
    const rows=(mySchoolStaffRegistry&&window.EducationMySchoolStaff)?window.EducationMySchoolStaff.forSchool(mySchoolStaffRegistry,code):[];
    if(mySchoolStaffMeta){
      if(mySchoolStaffRegistry){
        const unique=mySchoolStaffRegistry.unique_people_count?(' · '+mySchoolStaffRegistry.unique_people_count+' μοναδικοί εκπαιδευτικοί'):'';
        mySchoolStaffMeta.textContent=(mySchoolStaffRegistry.source_file||'stat4_8')+' · '+mySchoolStaffRegistry.placement_count+' τοποθετήσεις'+unique+' · '+mySchoolStaffRegistry.school_count+' μονάδες'+(code?' · '+rows.length+' εγγραφές στο '+code:' · επίλεξε σχολείο με πραγματικό κωδικό');
      }else{
        mySchoolStaffMeta.textContent='Φόρτωσε το αυθεντικό ZIP ή CSV του stat4_8. Το αρχείο επεξεργάζεται μόνο τοπικά στον browser.';
      }
    }
    if(loadMySchoolStaffForSchool) loadMySchoolStaffForSchool.disabled=!mySchoolStaffRegistry||!code||!rows.length;
    if(downloadCleanMySchoolStaff) downloadCleanMySchoolStaff.disabled=!mySchoolStaffRegistry;
    if(clearMySchoolStaff) clearMySchoolStaff.disabled=!mySchoolStaffRegistry;
    if(mySchoolStaffRegistry&&code&&!rows.length) mySchoolStaffSetStatus('Δεν βρέθηκε προσωπικό στο μητρώο stat4_8 για τον κωδικό '+code+'.','error');
    else if(mySchoolStaffRegistry&&code&&rows.length) mySchoolStaffSetStatus('Βρέθηκαν '+rows.length+' εγγραφές προσωπικού για το τρέχον σχολείο. Μπορείς να τις φορτώσεις με ένα κλικ.','success');
    else if(!mySchoolStaffRegistry) mySchoolStaffSetStatus('', '');
  }
  function openMySchoolStaffPanel(){ if(mySchoolStaffPanel) mySchoolStaffPanel.hidden=false; refreshMySchoolStaffPanel(); }
  if(openMySchoolStaff) openMySchoolStaff.addEventListener('click',openMySchoolStaffPanel);
  if(closeMySchoolStaff) closeMySchoolStaff.addEventListener('click',function(){if(mySchoolStaffPanel) mySchoolStaffPanel.hidden=true;});
  if(pickMySchoolStaffFile) pickMySchoolStaffFile.addEventListener('click',function(){if(mySchoolStaffFile){mySchoolStaffFile.value='';mySchoolStaffFile.click();}});
  if(mySchoolStaffFile){
    mySchoolStaffFile.addEventListener('change',function(){
      const file=mySchoolStaffFile.files&&mySchoolStaffFile.files[0]; if(!file) return;
      if(!window.EducationMySchoolStaff){mySchoolStaffSetStatus('Δεν φορτώθηκε ο importer myschool stat4_8.','error');return;}
      const reader=new FileReader();
      reader.onload=async function(){
        try{
          mySchoolStaffSetStatus('Ανάγνωση και καθαρισμός του stat4_8…','');
          const registry=await window.EducationMySchoolStaff.parseArrayBuffer(reader.result,file.name);
          window.EducationMySchoolStaff.saveSession(registry);
          mySchoolStaffRegistry=registry;
          refreshMySchoolStaffPanel();
          const code=currentSchoolCodeForStaff();
          const count=code?window.EducationMySchoolStaff.forSchool(registry,code).length:0;
          mySchoolStaffSetStatus('Το μητρώο καθαρίστηκε και αποθηκεύτηκε μόνο για τη συνεδρία: '+registry.placement_count+' τοποθετήσεις σε '+registry.school_count+' μονάδες.'+(code?' Για το τρέχον σχολείο βρέθηκαν '+count+'.':''),'success');
        }catch(err){
          mySchoolStaffSetStatus(err&&err.message?err.message:'Δεν ήταν δυνατή η ανάγνωση του stat4_8.','error');
        }
      };
      reader.onerror=function(){mySchoolStaffSetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου.','error');};
      reader.readAsArrayBuffer(file);
    });
  }
  if(loadMySchoolStaffForSchool){
    loadMySchoolStaffForSchool.addEventListener('click',function(){
      if(!mySchoolStaffRegistry||!window.EducationMySchoolStaff) return;
      const code=currentSchoolCodeForStaff();
      const people=window.EducationMySchoolStaff.forSchool(mySchoolStaffRegistry,code);
      if(!people.length){refreshMySchoolStaffPanel();return;}
      const existing=personnelList?personnelList.querySelectorAll('[data-personnel-row]').length:0;
      if(existing&&!window.confirm('Θα αντικατασταθούν οι '+existing+' υπάρχουσες εγγραφές προσωπικού με τις '+people.length+' εγγραφές του myschool για το σχολείο '+code+'. Συνέχεια;')) return;
      clearPersonnelRows();
      let imported=0,unknown=0;
      people.forEach(function(person){
        const result=addPersonnelFromData(person);
        if(result.ok) imported++;
        if(result.unknownCode) unknown++;
      });
      ensurePersonnelEmptyState(); refreshDirectorRoleConstraints(); markPersonnelDirty();
      mySchoolStaffSetStatus('Φορτώθηκαν '+imported+' εκπαιδευτικοί από το myschool για το '+code+'.'+(unknown?' '+unknown+' κλάδοι χρειάζονται χειροκίνητο έλεγχο.':'')+' Πάτησε «Έλεγχος ωραρίων προσωπικού» για να ενημερωθούν οι επόμενες καρτέλες.',unknown?'error':'success');
    });
  }
  if(downloadCleanMySchoolStaff){
    downloadCleanMySchoolStaff.addEventListener('click',function(){
      if(!mySchoolStaffRegistry||!window.EducationMySchoolStaff) return;
      const csv=window.EducationMySchoolStaff.cleanedCsv(mySchoolStaffRegistry);
      const blob=new Blob([csv],{type:'text/csv;charset=utf-8'}),url=URL.createObjectURL(blob),a=document.createElement('a');
      a.href=url;a.download='dde-staff-registry-v1-myschool-clean.csv';document.body.appendChild(a);a.click();a.remove();setTimeout(function(){URL.revokeObjectURL(url);},500);
    });
  }
  if(clearMySchoolStaff){
    clearMySchoolStaff.addEventListener('click',function(){
      if(window.EducationMySchoolStaff) window.EducationMySchoolStaff.clearSession();
      mySchoolStaffRegistry=null;refreshMySchoolStaffPanel();mySchoolStaffSetStatus('Το προσωρινό μητρώο myschool καθαρίστηκε από τη συνεδρία.','success');
    });
  }
  refreshMySchoolStaffPanel();

  if(importPersonnelCsv){
    importPersonnelCsv.addEventListener('click',function(){
      if(!personnelCsvData || !window.EducationPersonnelCsv) return;
      if(!personnelCsvImporterSupportsRegistry()){
        personnelCsvSetStatus('Η εισαγωγή σταμάτησε επειδή ο browser έχει παλιότερη cached έκδοση του CSV importer. Ανανέωσε τη σελίδα και δοκίμασε ξανά.','error');
        return;
      }
      personnelCsvMapping=personnelCsvCurrentMapping();
      if(!personnelCsvMapping.specialty_code){ validatePersonnelCsvImport(); return; }
      if(personnelCsvMode && personnelCsvMode.value==='replace') clearPersonnelRows();
      let imported=0,skipped=0,unknown=0;
      personnelCsvData.rows.forEach(function(raw){
        const person=window.EducationPersonnelCsv.rowToPersonnel(raw,personnelCsvMapping);
        if(!person.specialty_code && !person.display_name){ skipped++; return; }
        const result=addPersonnelFromData(person); if(result.ok) imported++; if(result.unknownCode) unknown++;
      });
      ensurePersonnelEmptyState();
      markPersonnelDirty();
      let msg='Εισήχθησαν '+imported+' εκπαιδευτικοί.';
      if(skipped) msg+=' Παραλείφθηκαν '+skipped+' κενές εγγραφές.';
      if(unknown) msg+=' '+unknown+' εγγραφές έχουν μη αναγνωρισμένο κλάδο και χρειάζονται χειροκίνητο έλεγχο.';
      refreshDirectorRoleConstraints();
      const importedDirectorCount=personnelList ? Array.from(personnelList.querySelectorAll('.personnel-role')).filter(function(role){return role.value==='director';}).length : 0;
      if(importedDirectorCount>1) msg+=' Έχουν δηλωθεί '+importedDirectorCount+' Διευθυντές/ντριες· επιτρέπεται μόνο ένας/μία.';
      msg+=' Πάτησε «Έλεγχος ωραρίων προσωπικού» για να ενημερωθεί και η σύνοψη ανά κλάδο.';
      personnelCsvSetStatus(msg,(unknown||importedDirectorCount>1)?'error':'success');
    });
  }
  function personnelRegistryCsvEscape(value){
    const text=csvSpreadsheetSafeText(value);
    return /[;"\r\n]/.test(text)?'"'+text.replace(/"/g,'""')+'"':text;
  }
  function personnelRegistryRoleLabel(value){
    if(value==='director') return 'Διευθυντής';
    if(value==='vice_or_sector') return 'Υποδιευθυντής';
    return 'Εκπαιδευτικός';
  }
  function personnelRegistryRows(){
    if(!personnelList) return [];
    return Array.from(personnelList.querySelectorAll('[data-personnel-row]')).map(function(row){
      const value=function(selector){ const el=row.querySelector(selector); return el?String(el.value||'').trim():''; };
      const role=value('.personnel-role')||'teacher';
      const source=value('.personnel-obligation-source');
      const sourceRequired=source==='myschool_stat4_8';
      return [
        'staff_registry_v1',
        value('input[name="personnel_person_id[]"]'),
        value('.personnel-specialty'),
        value('.personnel-secondary-specialty'),
        value('.personnel-name'),
        (role==='teacher'||sourceRequired)?value('.personnel-required'):'',
        personnelRegistryRoleLabel(role),
        (role==='teacher'||sourceRequired)?'':value('.personnel-years'),
        (role==='teacher'||sourceRequired)?'':value('.personnel-months'),
        (role==='teacher'||sourceRequired)?'':value('.personnel-days'),
        value('.personnel-external'),
        source,
        value('.personnel-source-base-required'),
        value('.personnel-source-reduction'),
        value('.personnel-source-at-unit')
      ];
    });
  }
  function downloadPersonnelRegistryCsv(rows,filename){
    const headers=['Έκδοση μητρώου','Αναγνωριστικό','Κλάδος','2η ειδικότητα','Ονοματεπώνυμο','Υποχρεωτικό ωράριο','Ρόλος','Έτη υπηρεσίας','Μήνες','Ημέρες','Ώρες αλλού','Πηγή ωραρίου','Υ.Ω. πηγής','Μείωση πηγής','Ώρες Υ.Ω. στον φορέα'];
    const lines=[headers].concat(rows).map(function(cols){return cols.map(personnelRegistryCsvEscape).join(';');});
    const csv='\uFEFF'+lines.join('\r\n')+'\r\n';
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'}); const url=URL.createObjectURL(blob); const a=document.createElement('a');
    a.href=url; a.download=filename; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function(){URL.revokeObjectURL(url);},500);
  }
  if(exportPersonnelRegistryCsv){
    exportPersonnelRegistryCsv.addEventListener('click',function(){
      const rows=personnelRegistryRows();
      if(!rows.length){ window.alert('Δεν υπάρχει προσωπικό για εξαγωγή.'); return; }
      downloadPersonnelRegistryCsv(rows,'mitroo-ekpaideftikon.csv');
    });
  }
  if(downloadPersonnelCsvTemplate){
    downloadPersonnelCsvTemplate.addEventListener('click',function(){
      downloadPersonnelRegistryCsv([
        ['staff_registry_v1','', 'ΠΕ03','ΠΕ86','Μαρία Παπαδοπούλου','20','Εκπαιδευτικός','','','','0','','','',''],
        ['staff_registry_v1','', 'ΠΕ02','','Γιώργος Διευθυντής','','Διευθυντής','20','0','0','0','','','','']
      ],'protypo-mitroou-ekpaideftikon.csv');
    });
  }

  if(personnelList) personnelList.querySelectorAll('[data-personnel-row]').forEach(bindPersonnelRow);
  function refreshDirectorPersonnelRows(){
    if(!personnelList)return;
    personnelList.querySelectorAll('.personnel-role').forEach(function(role){if(role.value==='director')updatePersonnelRow(role.closest('[data-personnel-row]'));});
  }
  document.querySelectorAll('[name="gym_general_a"],[name="gym_general_b"],[name="gym_general_c"],[name="gel_general_a"],[name="gel_general_b"],[name="gel_general_c"]').forEach(function(el){ el.addEventListener('input',refreshDirectorPersonnelRows); });
  if(type) type.addEventListener('change',refreshDirectorPersonnelRows);
  if(addPersonnel && personnelTemplate && personnelList){
    addPersonnel.addEventListener('click',function(){
      const empty=document.getElementById('emptyPersonnelState'); if(empty) empty.remove();
      const fragment=personnelTemplate.content.cloneNode(true);
      const row=fragment.querySelector('[data-personnel-row]');
      personnelList.appendChild(fragment);
      bindPersonnelRow(row);
      markPersonnelDirty();
      const first=row.querySelector('.personnel-specialty'); if(first) first.focus();
    });
  }
  if(personnelFilter && personnelList){
    personnelFilter.addEventListener('input',function(){
      const q=(personnelFilter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
      personnelList.querySelectorAll('[data-personnel-row]').forEach(function(row){
        const specialty=row.querySelector('.personnel-specialty');
        const secondary=row.querySelector('.personnel-secondary-specialty');
        const name=row.querySelector('.personnel-name');
        const hay=((specialty?specialty.value:'')+' '+(secondary?secondary.value:'')+' '+(name?name.value:'')).toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,'');
        row.hidden=q!==''&&!hay.includes(q);
      });
    });
  }

  const allocationPeopleData=staffingRuntimeConfig.allocationPeople&&typeof staffingRuntimeConfig.allocationPeople==='object'?staffingRuntimeConfig.allocationPeople:{};
  const allocationSlotsData=staffingRuntimeConfig.allocationSlots&&typeof staffingRuntimeConfig.allocationSlots==='object'?staffingRuntimeConfig.allocationSlots:{};
  const specialtyLabelsData=staffingRuntimeConfig.specialtyLabels&&typeof staffingRuntimeConfig.specialtyLabels==='object'?staffingRuntimeConfig.specialtyLabels:{};
  const specialtyReportSchemaVersion='staffing_balance_v1';
  const allocationList=document.getElementById('allocationList');
  const allocationTemplate=document.getElementById('allocationRowTemplate');
  const addAllocation=document.getElementById('addAllocationRow');
  const clearAllocation=document.getElementById('clearAllocationRows');
  const allocationBAssignmentWarning='Οι ώρες μαθημάτων Β΄ ανάθεσης, από τη βασική και τη δεύτερη ειδικότητα συνολικά, υπερβαίνουν το όριο των 10 διδακτικών ωρών. Υπέρβαση επιτρέπεται μόνο κατ’ εξαίρεση, ύστερα από απόφαση ΠΥΣΔΕ και υπό τις προβλεπόμενες προϋποθέσεις.';
  function allocationPriority(code,slot){
    if(!code||!slot||!slot.eligible_by_priority) return '';
    const order=['A','B','C','SPECIAL'];
    for(let i=0;i<order.length;i++){
      const p=order[i], arr=slot.eligible_by_priority[p]||[];
      if(arr.indexOf(code)>=0) return p;
    }
    return '';
  }
  function allocationPriorityRank(priority){
    const rank={A:1,SPECIAL:1,B:2,C:3};
    return rank[priority]||99;
  }
  function allocationPriorityLabel(priority){
    if(priority==='A') return 'Α΄';
    if(priority==='B') return 'Β΄';
    if(priority==='C') return 'Γ΄';
    if(priority==='SPECIAL') return 'Ειδική';
    return priority||'';
  }
  function allocationBestAssignment(person,slot){
    if(!person||!slot) return null;
    const candidates=[];
    const primary=person.specialty_code||'';
    const secondary=person.secondary_specialty_code||'';
    const p1=allocationPriority(primary,slot);
    if(p1) candidates.push({priority:p1,used_specialty_code:primary,specialty_source:'primary'});
    if(secondary&&secondary!==primary){
      const p2=allocationPriority(secondary,slot);
      if(p2) candidates.push({priority:p2,used_specialty_code:secondary,specialty_source:'secondary'});
    }
    if(!candidates.length) return null;
    candidates.sort(function(a,b){
      const d=allocationPriorityRank(a.priority)-allocationPriorityRank(b.priority);
      if(d) return d;
      if(a.specialty_source===b.specialty_source) return 0;
      return a.specialty_source==='primary'?-1:1;
    });
    return candidates[0];
  }
  const vacancyEligiblePeopleCache={};function vacancyEligiblePeopleForSlot(sid,slot){
    if(Object.prototype.hasOwnProperty.call(vacancyEligiblePeopleCache,sid))return vacancyEligiblePeopleCache[sid];
    const candidates=[];Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const match=allocationBestAssignment(allocationPeopleData[pid],slot);
      if(match)candidates.push({pid:pid,priority:match.priority});
    });
    vacancyEligiblePeopleCache[sid]=candidates;return candidates;
  }
  function allocationAssignmentLabel(match){
    if(!match) return '';
    let text=allocationPriorityLabel(match.priority)+' ανάθεση';
    if(match.specialty_source==='secondary') text+=' · μέσω 2ης ειδικότητας '+match.used_specialty_code;
    return text;
  }
  function allocationPopulatePeopleForSlot(row,preserveSelected){
    const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot');
    if(!personEl) return;
    const oldSelected=personEl.value||'';
    const slot=slotEl?allocationSlotsData[slotEl.value]||null:null;
    personEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent=slot?'— επιλογή επιλέξιμου εκπαιδευτικού —':'— επίλεξε πρώτα μάθημα / τμήμα —'; personEl.appendChild(placeholder);
    if(!slot){ personEl.disabled=true; return; }
    personEl.disabled=false;
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const person=allocationPeopleData[pid], match=allocationBestAssignment(person,slot);
      if(!match) return;
      const option=document.createElement('option'); option.value=pid; option.textContent=(person.label||pid)+' · '+allocationAssignmentLabel(match); if(oldSelected===pid) option.selected=true; personEl.appendChild(option);
    });
    if(preserveSelected&&oldSelected&&allocationPeopleData[oldSelected]&&!allocationBestAssignment(allocationPeopleData[oldSelected],slot)){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent=(allocationPeopleData[oldSelected].label||oldSelected)+' · ΜΗ ΕΠΙΤΡΕΠΤΟ'; invalid.selected=true; personEl.insertBefore(invalid,personEl.children[1]||null);
    }
    personEl.dataset.optionsLoaded='1';
  }
  function allocationPopulateAllSlots(row,preserveSelected){
    const slotEl=row.querySelector('.allocation-slot');
    if(!slotEl) return;
    const oldSelected=slotEl.value||'';
    slotEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent='— επιλογή τμήματος / ομάδας και μαθήματος —'; slotEl.appendChild(placeholder);
    let currentGroup='', group=null;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid];
      if(!slot||!slot.has_eligible_person) return;
      const groupLabel=((slot.structure_label||'')?slot.structure_label+' · ':'')+(slot.grade||'Άλλο')+' τάξη';
      if(groupLabel!==currentGroup){ group=document.createElement('optgroup'); group.label=groupLabel; slotEl.appendChild(group); currentGroup=groupLabel; }
      const option=document.createElement('option'); option.value=slot.slot_id; option.textContent=slot.label; option.setAttribute('data-capacity',String(slot.capacity_hours)); if(oldSelected===slot.slot_id) option.selected=true; group.appendChild(option);
    });
    if(preserveSelected&&oldSelected&&!allocationSlotsData[oldSelected]){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent='Μη έγκυρο μάθημα / τμήμα'; invalid.selected=true; invalid.disabled=true; slotEl.insertBefore(invalid,slotEl.children[1]||null);
    }
    slotEl.dataset.optionsLoaded='1';
  }
  function allocationEnsureSlotOptions(row){
    const slotEl=row&&row.querySelector('.allocation-slot');
    if(!slotEl||slotEl.dataset.optionsLoaded==='1') return;
    allocationPopulateAllSlots(row,true);
    const state=allocationCollectState();
    updateAllocationSlotOptionAvailability(state.slotAssigned||{});
  }
  function allocationEnsurePersonOptions(row){
    const personEl=row&&row.querySelector('.allocation-person');
    if(!personEl||personEl.dataset.optionsLoaded==='1') return;
    allocationPopulatePeopleForSlot(row,true);
    personEl.dataset.optionsLoaded='1';
  }
  // Διατηρείται ως μικρό compatibility helper, αλλά η κύρια προβολή είναι slot-first.
  function allocationPopulateSlotsForPerson(row,preserveSelected){
    const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot');
    if(!slotEl) return;
    const oldSelected=slotEl.value||'';
    const person=personEl?allocationPeopleData[personEl.value]||null:null;
    slotEl.innerHTML='';
    const placeholder=document.createElement('option'); placeholder.value=''; placeholder.textContent=person?'— επιλογή επιλέξιμου μαθήματος —':'— επιλογή τμήματος / ομάδας και μαθήματος —'; slotEl.appendChild(placeholder);
    let currentGrade=null, group=null;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid];
      if(!slot.has_eligible_person) return;
      if(person&&!allocationBestAssignment(person,slot)) return;
      if(slot.grade!==currentGrade){ group=document.createElement('optgroup'); group.label=(slot.grade||'Άλλο')+' τάξη'; slotEl.appendChild(group); currentGrade=slot.grade; }
      const option=document.createElement('option'); option.value=slot.slot_id; option.textContent=slot.label; option.setAttribute('data-capacity',String(slot.capacity_hours)); if(oldSelected===slot.slot_id) option.selected=true; group.appendChild(option);
    });
    if(preserveSelected&&oldSelected&&allocationSlotsData[oldSelected]&&person&&!allocationBestAssignment(person,allocationSlotsData[oldSelected])){
      const invalid=document.createElement('option'); invalid.value=oldSelected; invalid.textContent=allocationSlotsData[oldSelected].label+' · ΜΗ ΕΠΙΤΡΕΠΤΟ'; invalid.selected=true; slotEl.insertBefore(invalid,slotEl.children[1]||null);
    }
  }
  function allocationSetStatus(row,text,kind){
    const el=row.querySelector('[data-allocation-status]'); if(!el) return;
    el.textContent=text; el.classList.remove('is-ok','is-warning','is-error');
    if(kind) el.classList.add('is-'+kind);
  }
  function allocationRows(){ return allocationList ? Array.from(allocationList.querySelectorAll('[data-allocation-row]')) : []; }
  const vacancyRows=Array.from(document.querySelectorAll('[data-vacancy-row]'));
  const printVacancyRowsById={};
  Array.from(document.querySelectorAll('[data-print-vacancy-row]')).forEach(function(row){ printVacancyRowsById[row.getAttribute('data-print-vacancy-row')||'']=row; });
  const vacancyRowCache={};
  vacancyRows.forEach(function(row){
    const sid=row.getAttribute('data-vacancy-row')||'', printRow=printVacancyRowsById[sid]||null;
    vacancyRowCache[sid]={
      hours:row.querySelector('[data-vacancy-hours]'),
      status:row.querySelector('[data-vacancy-status]'),
      search:(row.getAttribute('data-search')||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,''),
      printRow:printRow,
      printHours:printRow?printRow.querySelector('[data-print-vacancy-hours]'):null,
      printStatus:printRow?printRow.querySelector('[data-print-vacancy-status]'):null
    };
  });
  const vacancyFilter=document.getElementById('vacancyFilter');
  const stat51Panel=document.getElementById('stat51ComparePanel');
  const stat51FileInput=document.getElementById('stat51FileInput');
  const pickStat51File=document.getElementById('pickStat51File');
  const clearStat51File=document.getElementById('clearStat51File');
  const stat51Status=document.getElementById('stat51Status');
  const stat51Summary=document.getElementById('stat51Summary');
  const stat51TableWrap=document.getElementById('stat51TableWrap');
  const stat51ComparisonBody=document.getElementById('stat51ComparisonBody');
  const stat51Empty=document.getElementById('stat51Empty');
  const stat51Footnote=document.getElementById('stat51Footnote');
  let stat51Registry=(window.EducationMySchoolStat51&&window.EducationMySchoolStat51.loadSession)?window.EducationMySchoolStat51.loadSession():null;
  function stat51CurrentSchoolCode(){
    const field=document.querySelector('[name="school_code"]'), raw=field?field.value:'';
    return window.EducationMySchoolStat51?window.EducationMySchoolStat51.normalizeSchoolCode(raw):String(raw||'').trim();
  }
  function stat51SetStatus(text,kind){
    if(!stat51Status) return;
    stat51Status.textContent=text;
    stat51Status.classList.remove('is-error','is-success','is-warning');
    if(kind) stat51Status.classList.add('is-'+kind);
  }
  function stat51DisplayGrade(value){
    const g=String(value||'').replace(/[΄’']/g,'').trim().toUpperCase();
    return ['Α','Β','Γ','Δ'].indexOf(g)>=0?g+'΄':(value||'—');
  }
  function stat51LocalGroups(slotAssigned){
    const api=window.EducationMySchoolStat51, groups={};
    if(!api) return groups;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid]||{}, remaining=Math.max(0,(slot.capacity_hours||0)-(slotAssigned&&slotAssigned[sid]||0));
      if(remaining<1) return;
      const subject=String(slot.choice_option||slot.subject||'').trim();
      if(!subject) return;
      const structure=api.localStructure(slot.school||''), strict=api.strictKey(structure,slot.grade||'',subject), loose=api.looseKey(slot.grade||'',subject);
      if(!groups[strict]) groups[strict]={key:strict,loose_key:loose,structure:structure,structure_label:slot.structure_label||'',grade:slot.grade||'',subject:subject,ours_hours:0,slot_count:0};
      groups[strict].ours_hours+=remaining; groups[strict].slot_count++;
    });
    return groups;
  }
  function stat51PairGroups(localGroups,statGroups){
    const pairs=[], usedLocal={}, usedStat={};
    Object.keys(localGroups).forEach(function(key){
      if(statGroups[key]){pairs.push({local:localGroups[key],stat:statGroups[key],match_kind:'strict'});usedLocal[key]=true;usedStat[key]=true;}
    });
    const localLoose={}, statLoose={};
    Object.keys(localGroups).forEach(function(key){if(!usedLocal[key]){const lk=localGroups[key].loose_key;(localLoose[lk]||(localLoose[lk]=[])).push(key);}});
    Object.keys(statGroups).forEach(function(key){if(!usedStat[key]){const lk=statGroups[key].loose_key;(statLoose[lk]||(statLoose[lk]=[])).push(key);}});
    Object.keys(localLoose).forEach(function(lk){
      if(localLoose[lk].length===1&&statLoose[lk]&&statLoose[lk].length===1){
        const lkey=localLoose[lk][0],skey=statLoose[lk][0];pairs.push({local:localGroups[lkey],stat:statGroups[skey],match_kind:'loose'});usedLocal[lkey]=true;usedStat[skey]=true;
      }
    });
    Object.keys(localGroups).forEach(function(key){if(!usedLocal[key])pairs.push({local:localGroups[key],stat:null,match_kind:'local_only'});});
    Object.keys(statGroups).forEach(function(key){if(!usedStat[key])pairs.push({local:null,stat:statGroups[key],match_kind:'stat_only'});});
    return pairs;
  }
  function stat51AppendCell(row,text,className){
    const td=document.createElement('td');td.textContent=String(text==null?'':text);if(className)td.className=className;row.appendChild(td);return td;
  }
  function stat51RenderComparison(slotAssigned){
    if(!stat51ComparisonBody||!window.EducationMySchoolStat51) return;
    const api=window.EducationMySchoolStat51, code=stat51CurrentSchoolCode();
    stat51ComparisonBody.innerHTML='';
    if(!stat51Registry){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Empty) stat51Empty.hidden=true;if(stat51Footnote)stat51Footnote.hidden=true;
      if(clearStat51File) clearStat51File.hidden=true;
      stat51SetStatus('Δεν έχει φορτωθεί stat5_1.','');
      return;
    }
    if(clearStat51File) clearStat51File.hidden=false;
    if(!code){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Empty) stat51Empty.hidden=true;if(stat51Footnote)stat51Footnote.hidden=true;
      stat51SetStatus('Το stat5_1 έχει φορτωθεί, αλλά χρειάζεται κωδικός myschool στην Καρτέλα 1 για να επιλεγεί η σωστή σχολική μονάδα.','warning');
      return;
    }
    const schoolRows=api.forSchool(stat51Registry,code);
    if(!schoolRows.length){
      if(stat51Summary) stat51Summary.hidden=true;if(stat51TableWrap) stat51TableWrap.hidden=true;if(stat51Footnote)stat51Footnote.hidden=false;
      if(stat51Empty){stat51Empty.hidden=false;stat51Empty.textContent='Δεν βρέθηκε σχολική μονάδα με κωδικό '+code+' στο φορτωμένο stat5_1.';}
      stat51SetStatus('Δεν υπάρχει εγγραφή stat5_1 για τον κωδικό '+code+'.','warning');
      return;
    }
    const localGroups=stat51LocalGroups(slotAssigned||{}), statGroups=api.aggregateRows(schoolRows), pairs=stat51PairGroups(localGroups,statGroups);
    const looseCount={};pairs.forEach(function(pair){const item=pair.local||pair.stat;if(item){const k=item.loose_key||'';looseCount[k]=(looseCount[k]||0)+1;}});
    pairs.sort(function(a,b){
      const ag=api.normalizeGrade((a.local||a.stat).grade),bg=api.normalizeGrade((b.local||b.stat).grade),order={Α:1,Β:2,Γ:3,Δ:4};
      if((order[ag]||9)!==(order[bg]||9))return (order[ag]||9)-(order[bg]||9);
      return String((a.local||a.stat).subject||'').localeCompare(String((b.local||b.stat).subject||''),'el',{numeric:true});
    });
    let oursTotal=0,statTotal=0,agreements=0,differences=0;
    pairs.forEach(function(pair){
      const local=pair.local,stat=pair.stat,ours=local?local.ours_hours:0,mys=stat?stat.myschool_gap_hours:0,diff=ours-mys;
      oursTotal+=ours;statTotal+=mys;
      let label='',cls='';
      if(local&&stat&&Math.abs(diff)<0.001){label='Συμφωνία';cls='is-agreement';agreements++;}
      else if(local&&stat){label='Διαφορά';cls='is-difference';differences++;}
      else if(local){label='Μόνο στο εργαλείο';cls='is-only';differences++;}
      else {label='Μόνο στο stat5_1';cls='is-only';differences++;}
      const item=local||stat,tr=document.createElement('tr');tr.className=cls;
      stat51AppendCell(tr,stat51DisplayGrade(item.grade));
      let subject=String(item.subject||'');
      if(looseCount[item.loose_key||'']>1){const structureLabel=local&&local.structure_label?local.structure_label:(stat&&stat.structure?stat.structure:'');if(structureLabel)subject+=' · '+structureLabel;}
      stat51AppendCell(tr,subject);
      stat51AppendCell(tr,ours,'num');stat51AppendCell(tr,mys,'num');stat51AppendCell(tr,(diff>0?'+':'')+String(diff),'num stat51-diff');
      const statusCell=stat51AppendCell(tr,label,'stat51-check');
      if(stat){
        const details=[];if(stat.assignment_a&&stat.assignment_a.length)details.push('Α΄: '+stat.assignment_a.join(' | '));if(stat.assignment_b&&stat.assignment_b.length)details.push('Β΄: '+stat.assignment_b.join(' | '));
        if(details.length)statusCell.title=details.join(' · ');
      }
      stat51ComparisonBody.appendChild(tr);
    });
    const oursEl=document.querySelector('[data-stat51-ours]'),mysEl=document.querySelector('[data-stat51-myschool]'),diffEl=document.querySelector('[data-stat51-difference]'),agreeEl=document.querySelector('[data-stat51-agreements]');
    if(oursEl)oursEl.textContent=String(oursTotal);if(mysEl)mysEl.textContent=String(statTotal);if(diffEl)diffEl.textContent=(oursTotal-statTotal>0?'+':'')+String(oursTotal-statTotal);if(agreeEl)agreeEl.textContent=String(agreements);
    if(stat51Summary)stat51Summary.hidden=false;if(stat51TableWrap)stat51TableWrap.hidden=pairs.length===0;if(stat51Empty)stat51Empty.hidden=pairs.length!==0;if(stat51Footnote)stat51Footnote.hidden=false;
    const schoolName=schoolRows[0].school_name||code, source=stat51Registry.source_inner_file||stat51Registry.source_file||'stat5_1';
    const mismatch=stat51Registry.formula_mismatch_count||0;
    stat51SetStatus(source+' · '+schoolName+' ('+code+') · '+schoolRows.length+' γραμμές κενών · '+statTotal+' ώρες στο stat5_1 · '+differences+' αποκλίσεις.'+(mismatch?' Προσοχή: '+mismatch+' γραμμές του αρχείου δεν συμφωνούν με τον τύπο συνολικές ώρες − κάλυψη.':''),differences?'warning':'success');
  }
  function stat51RefreshFromCurrentAllocation(){const state=allocationCollectState();stat51RenderComparison(state.slotAssigned);}
  if(pickStat51File&&stat51FileInput)pickStat51File.addEventListener('click',function(){stat51FileInput.click();});
  if(stat51FileInput)stat51FileInput.addEventListener('change',function(){
    const file=stat51FileInput.files&&stat51FileInput.files[0];if(!file)return;
    if(!window.EducationMySchoolStat51){stat51SetStatus('Δεν φορτώθηκε ο importer myschool stat5_1.','error');return;}
    stat51SetStatus('Ανάγνωση '+file.name+'…','');
    const reader=new FileReader();reader.onload=async function(){
      try{
        const registry=await window.EducationMySchoolStat51.parseArrayBuffer(reader.result,file.name);stat51Registry=registry;
        const saved=window.EducationMySchoolStat51.saveSession(registry);
        if(stat51Panel)stat51Panel.open=true;
        stat51RefreshFromCurrentAllocation();
        if(!saved)stat51SetStatus(stat51Status.textContent+' Η σύγκριση λειτουργεί, αλλά το αρχείο είναι πολύ μεγάλο για προσωρινή αποθήκευση στη συνεδρία του browser.','warning');
      }catch(error){stat51SetStatus('Αποτυχία ανάγνωσης stat5_1: '+(error&&error.message?error.message:'άγνωστο σφάλμα')+'.','error');}
      stat51FileInput.value='';
    };reader.onerror=function(){stat51SetStatus('Δεν ήταν δυνατή η ανάγνωση του αρχείου stat5_1.','error');};reader.readAsArrayBuffer(file);
  });
  if(clearStat51File)clearStat51File.addEventListener('click',function(){
    stat51Registry=null;if(window.EducationMySchoolStat51)window.EducationMySchoolStat51.clearSession();stat51RenderComparison({});
  });
  const stat51SchoolCodeField=document.querySelector('[name="school_code"]');if(stat51SchoolCodeField)stat51SchoolCodeField.addEventListener('input',function(){stat51RefreshFromCurrentAllocation();});
  function vacancyEligiblePeopleAvailability(sid,slot,personAssigned,personPriority){
    let normal=0, exceptionB=0;
    vacancyEligiblePeopleForSlot(sid,slot).forEach(function(candidate){
      const pid=candidate.pid, person=allocationPeopleData[pid];
      const remaining=Math.max(0,(person.available_here_hours||0)-(personAssigned[pid]||0));
      if(remaining<1) return;
      const bHours=personPriority&&personPriority[pid] ? (personPriority[pid].B||0) : 0;
      if(candidate.priority==='B' && bHours>=10) exceptionB++; else normal++;
    });
    return {normal:normal,exceptionB:exceptionB,total:normal+exceptionB};
  }
  function allocationCollectState(){
    const personAssigned={}, personPriority={}, personSource={}, slotAssigned={}, slotAttempted={}, rowState=[];
    Object.keys(allocationPeopleData||{}).forEach(function(id){
      personAssigned[id]=0;
      personPriority[id]={A:0,B:0,C:0,SPECIAL:0};
      personSource[id]={primary:0,secondary:0};
    });
    Object.keys(allocationSlotsData||{}).forEach(function(id){ slotAssigned[id]=0; slotAttempted[id]=0; });

    allocationRows().forEach(function(row){
      const personEl=row.querySelector('.allocation-person'), slotEl=row.querySelector('.allocation-slot'), hoursEl=row.querySelector('.allocation-hours');
      const pid=personEl?personEl.value:'', sid=slotEl?slotEl.value:'', hours=Math.max(0,parseInt(hoursEl&&hoursEl.value?hoursEl.value:'0',10)||0);
      const person=allocationPeopleData[pid]||null, slot=allocationSlotsData[sid]||null, match=person&&slot?allocationBestAssignment(person,slot):null;
      let error='', warnings=[];
      if((pid||sid||hours)&&!slot) error='Δεν έχει επιλεγεί έγκυρο τμήμα / ομάδα και μάθημα.';
      else if((pid||sid||hours)&&!person) error='Δεν έχει επιλεγεί έγκυρος εκπαιδευτικός.';
      else if((pid||sid)&&hours<1) error='Οι ώρες πρέπει να είναι θετικές.';
      else if(person&&slot&&hours>slot.capacity_hours) error='Οι ώρες υπερβαίνουν τις '+slot.capacity_hours+' ώρες του συγκεκριμένου τμήματος / ομάδας.';
      else if(person&&slot&&hours>0&&!match) error='Οι ειδικότητες '+person.specialty_code+(person.secondary_specialty_code?' / '+person.secondary_specialty_code:'')+' δεν έχουν ανάθεση στο συγκεκριμένο μάθημα.';
      if(!error&&person&&slot&&hours>0&&match){
        slotAttempted[sid]=(slotAttempted[sid]||0)+hours;
              }
      rowState.push({row:row,pid:pid,sid:sid,hours:hours,person:person,slot:slot,match:match,error:error,warnings:warnings,finalError:''});
    });

    const overallocatedSlots={};
    let overSlots=0;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const cap=allocationSlotsData[sid].capacity_hours||0, attempted=slotAttempted[sid]||0, over=Math.max(0,attempted-cap);
      if(over>0){ overallocatedSlots[sid]=over; overSlots+=over; }
    });

    let basicAssigned=0;
    rowState.forEach(function(st){
      st.finalError=st.error;
      if(!st.finalError&&st.sid&&overallocatedSlots[st.sid]){
        st.finalError='Το ίδιο τμήμα / ομάδα έχει συνολικά '+(slotAttempted[st.sid]||0)+' ώρες, ενώ διαθέτει '+st.slot.capacity_hours+'.';
      }
      if(st.finalError||!st.person||!st.slot||st.hours<1||!st.match) return;
      personAssigned[st.pid]=(personAssigned[st.pid]||0)+st.hours;
      personPriority[st.pid][st.match.priority]=(personPriority[st.pid][st.match.priority]||0)+st.hours;
      personSource[st.pid][st.match.specialty_source]=(personSource[st.pid][st.match.specialty_source]||0)+st.hours;
      slotAssigned[st.sid]=(slotAssigned[st.sid]||0)+st.hours;
      basicAssigned+=st.hours;
    });

    let unassigned=0;
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const cap=allocationSlotsData[sid].capacity_hours||0, assigned=slotAssigned[sid]||0;
      unassigned+=Math.max(0,cap-assigned);
    });
    return {
      rowState:rowState,
      personAssigned:personAssigned,
      personPriority:personPriority,
      personSource:personSource,
      slotAssigned:slotAssigned,
      slotAttempted:slotAttempted,
      overallocatedSlots:overallocatedSlots,
      overSlots:overSlots,
      unassigned:unassigned,
      basicAssigned:basicAssigned
    };
  }
  function currentAllocationTotals(){
    const state=allocationCollectState();
    return {personAssigned:state.personAssigned,slotAssigned:state.slotAssigned};
  }
  function specialtyTopCandidates(slot){
    if(!slot||!slot.eligible_by_priority) return {priority:'',codes:[],legal_codes:[],excluded_legacy_codes:[]};
    const legacyVacancyCodes={'ΠΕ04.03':true};
    const order=[], top=slot.top_priority||'';
    if(top) order.push(top);
    ['A','SPECIAL','B','C'].forEach(function(p){if(order.indexOf(p)<0) order.push(p);});
    let firstPriority='', firstLegal=[], excluded=[];
    for(let i=0;i<order.length;i++){
      const p=order[i], raw=slot.eligible_by_priority[p]||[];
      if(!raw.length) continue;
      const legal=Array.from(new Set(raw)).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});});
      if(!firstPriority){firstPriority=p;firstLegal=legal.slice();}
      const current=legal.filter(function(code){
        if(legacyVacancyCodes[code]){excluded.push(code);return false;}
        return true;
      });
      if(current.length) return {priority:p,codes:current,legal_priority:firstPriority,legal_codes:firstLegal.slice(),excluded_legacy_codes:Array.from(new Set(excluded)),used_lower_priority_because_legacy_only:firstPriority!==p};
    }
    return {priority:firstPriority,codes:firstLegal.slice(),legal_priority:firstPriority,legal_codes:firstLegal.slice(),excluded_legacy_codes:Array.from(new Set(excluded)),legacy_only_fallback:firstLegal.length>0};
  }
  function allocationObjectiveCompare(a,b){
    for(const key of ['covered','top','b','primary']){
      const av=a[key]||0,bv=b[key]||0;
      if(av!==bv) return av>bv?1:-1;
    }
    return 0;
  }
  function allocationObjectiveForRows(rows){
    const o={covered:0,top:0,b:0,primary:0};
    (rows||[]).forEach(function(row){
      const h=Math.max(0,parseInt(row.hours||0,10)||0), p=row.priority||'';
      o.covered+=h;
      if(p==='A'||p==='SPECIAL') o.top+=h; else if(p==='B') o.b+=h;
      if(row.specialty_source==='primary') o.primary+=h;
    });
    return o;
  }
  function allocationOptimizeRemaining(personStateInput,slotStateInput){
    const originalPeople=JSON.parse(JSON.stringify(personStateInput||{}));
    const originalSlots=JSON.parse(JSON.stringify(slotStateInput||{}));
    const peopleIds=Object.keys(originalPeople).filter(function(pid){return (originalPeople[pid].remaining_hours||0)>0;}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});});
    const routesBySlot={};
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const state=originalSlots[sid]||{}, need=Math.max(0,state.remaining_hours||0);
      if(need<1||state.atomic_blocked) return;
      const routes={};
      peopleIds.forEach(function(pid){
        const match=allocationBestAssignment(allocationPeopleData[pid],allocationSlotsData[sid]);
        if(!match) return;
        const ps=originalPeople[pid];
        if((ps.remaining_hours||0)<need) return;
        if(match.priority==='B'&&(ps.b_remaining_hours||0)<need) return;
        routes[pid]=match;
      });
      if(Object.keys(routes).length) routesBySlot[sid]=routes;
    });

    // Fast atomic seed. It is only the initial lower bound; the exact search
    // below is what fixes combinations such as 6 versus 3+2+2.
    const seedPeople=JSON.parse(JSON.stringify(originalPeople)), seedSlots=JSON.parse(JSON.stringify(originalSlots)), seed=[];
    Object.keys(routesBySlot).sort(function(a,b){
      const ca=Object.keys(routesBySlot[a]).length,cb=Object.keys(routesBySlot[b]).length;if(ca!==cb)return ca-cb;
      const ha=seedSlots[a].remaining_hours||0,hb=seedSlots[b].remaining_hours||0;if(ha!==hb)return hb-ha;
      return String(a).localeCompare(String(b),'el',{numeric:true});
    }).forEach(function(sid){
      const need=seedSlots[sid].remaining_hours||0;
      const candidates=Object.keys(routesBySlot[sid]).filter(function(pid){
        const m=routesBySlot[sid][pid],ps=seedPeople[pid];
        return ps&&(ps.remaining_hours||0)>=need&&(m.priority!=='B'||(ps.b_remaining_hours||0)>=need);
      }).sort(function(a,b){
        const ma=routesBySlot[sid][a],mb=routesBySlot[sid][b];
        const r=allocationPriorityRank(ma.priority)-allocationPriorityRank(mb.priority);if(r)return r;
        if(ma.specialty_source!==mb.specialty_source)return ma.specialty_source==='primary'?-1:1;
        const la=(seedPeople[a].remaining_hours||0)-need,lb=(seedPeople[b].remaining_hours||0)-need;if(la!==lb)return la-lb;
        return String(a).localeCompare(String(b),'el',{numeric:true});
      });
      if(!candidates.length)return;
      const pid=candidates[0],m=routesBySlot[sid][pid],slot=allocationSlotsData[sid];
      seed.push({person_id:pid,slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:need,priority:m.priority,used_specialty_code:m.used_specialty_code,specialty_source:m.specialty_source,source:'automatic_live_seed'});
      seedPeople[pid].remaining_hours-=need;
      if(m.priority==='B'){seedPeople[pid].b_assignment_hours=(seedPeople[pid].b_assignment_hours||0)+need;seedPeople[pid].b_remaining_hours=Math.max(0,10-seedPeople[pid].b_assignment_hours);}
      seedSlots[sid].remaining_hours=0;
    });

    const groupMap=new Map();
    Object.keys(routesBySlot).forEach(function(sid){
      const need=originalSlots[sid].remaining_hours||0,routes=routesBySlot[sid];
      const sig=Object.keys(routes).sort().map(function(pid){const m=routes[pid];return pid+'='+m.priority+'/'+m.specialty_source+'/'+m.used_specialty_code;}).join(';');
      const key=need+'|'+sig;
      if(!groupMap.has(key))groupMap.set(key,{need:need,routes:routes,slot_ids:[]});
      groupMap.get(key).slot_ids.push(sid);
    });
    const groups=Array.from(groupMap.values()).sort(function(a,b){const ca=Object.keys(a.routes).length,cb=Object.keys(b.routes).length;if(ca!==cb)return ca-cb;if(a.need!==b.need)return b.need-a.need;return String(a.slot_ids[0]).localeCompare(String(b.slot_ids[0]),'el',{numeric:true});});
    const personToGroups={};
    groups.forEach(function(g,gi){Object.keys(g.routes).forEach(function(pid){if(!personToGroups[pid])personToGroups[pid]=[];personToGroups[pid].push(gi);});});
    const visited=new Set(),components=[];
    groups.forEach(function(g,start){
      if(visited.has(start))return;
      const queue=[start],gis=[],pids=new Set();visited.add(start);
      while(queue.length){const gi=queue.shift();gis.push(gi);Object.keys(groups[gi].routes).forEach(function(pid){pids.add(pid);(personToGroups[pid]||[]).forEach(function(ngi){if(!visited.has(ngi)){visited.add(ngi);queue.push(ngi);}});});}
      components.push({group_indexes:gis,person_ids:Array.from(pids)});
    });
    const seedBySlot={};seed.forEach(function(row){seedBySlot[row.slot_id]=row;});
    let finalRows=[],allCertified=true,totalNodes=0;

    components.forEach(function(component){
      const cg=component.group_indexes.map(function(i){return groups[i];}), cpids=component.person_ids.slice().sort();
      if(cpids.length===1){
        const pid=cpids[0],cap=originalPeople[pid].remaining_hours||0,bcap=originalPeople[pid].b_remaining_hours||0,items=[];
        cg.forEach(function(g){const m=g.routes[pid];if(!m)return;g.slot_ids.forEach(function(sid){items.push({sid:sid,need:g.need,m:m});});});
        let dp=new Map();dp.set('0:0',{objective:{covered:0,top:0,b:0,primary:0},rows:[],used:0,bused:0});
        items.forEach(function(item){const next=new Map(dp);dp.forEach(function(st){totalNodes++;const nu=st.used+item.need,nb=st.bused+(item.m.priority==='B'?item.need:0);if(nu>cap||nb>bcap)return;const o={...st.objective};o.covered+=item.need;if(item.m.priority==='A'||item.m.priority==='SPECIAL')o.top+=item.need;else if(item.m.priority==='B')o.b+=item.need;if(item.m.specialty_source==='primary')o.primary+=item.need;const slot=allocationSlotsData[item.sid],rows=st.rows.concat([{person_id:pid,slot_id:item.sid,slot_label:slot.slot_label||slot.label||item.sid,subject:slot.subject||'',hours:item.need,priority:item.m.priority,used_specialty_code:item.m.used_specialty_code,specialty_source:item.m.specialty_source,source:'automatic_live_optimizer_dp'}]);const key=nu+':'+nb,prev=next.get(key);if(!prev||allocationObjectiveCompare(o,prev.objective)>0)next.set(key,{objective:o,rows:rows,used:nu,bused:nb});});dp=next;});
        let best={objective:{covered:0,top:0,b:0,primary:0},rows:[]};dp.forEach(function(st){if(allocationObjectiveCompare(st.objective,best.objective)>0)best=st;});finalRows=finalRows.concat(best.rows);return;
      }
      const counts=cg.map(function(g){return g.slot_ids.length;}),originalCounts=counts.slice();
      const rem={},brem={};cpids.forEach(function(pid){rem[pid]=originalPeople[pid].remaining_hours||0;brem[pid]=originalPeople[pid].b_remaining_hours||0;});
      const equiv={};cpids.forEach(function(pid){equiv[pid]=cg.map(function(g){const m=g.routes[pid];return m?(m.priority+'/'+m.specialty_source+'/'+m.used_specialty_code):'-';}).join(';');});
      let bestRows=[];cg.forEach(function(g){g.slot_ids.forEach(function(sid){if(seedBySlot[sid])bestRows.push(seedBySlot[sid]);});});
      let bestObj=allocationObjectiveForRows(bestRows),currentRows=[],cur={covered:0,top:0,b:0,primary:0},nodes=0,aborted=false;
      const memo=new Map(),nodeLimit=30000;
      function search(){
        if(aborted)return;if(++nodes>nodeLimit){aborted=true;return;}
        let remainingHours=0,done=true;cg.forEach(function(g,gi){if(counts[gi]>0){done=false;remainingHours+=counts[gi]*g.need;}});
        if(done){if(allocationObjectiveCompare(cur,bestObj)>0){bestObj={...cur};bestRows=currentRows.map(function(r){return {...r};});}return;}
        const personHours=cpids.reduce(function(t,pid){return t+(rem[pid]||0);},0),upper=cur.covered+Math.min(remainingHours,personHours);
        if(upper<bestObj.covered)return;if(upper===bestObj.covered&&cur.top+Math.min(remainingHours,personHours)<bestObj.top)return;
        const equivStates={};cpids.forEach(function(pid){const sig=equiv[pid]||pid;if(!equivStates[sig])equivStates[sig]=[];equivStates[sig].push(rem[pid]+':'+brem[pid]);});
        const key=counts.join(',')+'|'+Object.keys(equivStates).sort().map(function(sig){return sig+'='+equivStates[sig].sort().join(',');}).join('|');
        const seen=memo.get(key);if(seen&&(seen.top>cur.top||(seen.top===cur.top&&seen.primary>=cur.primary)))return;memo.set(key,{top:cur.top,primary:cur.primary});
        let chosen=-1,cands=[],few=1e9;
        cg.forEach(function(g,gi){if(counts[gi]<1)return;const local=Object.keys(g.routes).filter(function(pid){const m=g.routes[pid];return rem[pid]>=g.need&&(m.priority!=='B'||brem[pid]>=g.need);}).map(function(pid){return {pid:pid,m:g.routes[pid],left:rem[pid]-g.need};});if(chosen<0||local.length<few||(local.length===few&&g.need>cg[chosen].need)){chosen=gi;cands=local;few=local.length;}});
        if(chosen<0)return;
        if(!cands.length){const old=counts[chosen];counts[chosen]=0;search();counts[chosen]=old;return;}
        cands.sort(function(a,b){const r=allocationPriorityRank(a.m.priority)-allocationPriorityRank(b.m.priority);if(r)return r;if(a.m.specialty_source!==b.m.specialty_source)return a.m.specialty_source==='primary'?-1:1;if(a.left!==b.left)return a.left-b.left;return String(a.pid).localeCompare(String(b.pid),'el',{numeric:true});});
        const g=cg[chosen],idx=originalCounts[chosen]-counts[chosen],sid=g.slot_ids[idx],slot=allocationSlotsData[sid];counts[chosen]--;
        const sym=new Set();
        cands.forEach(function(c){const pid=c.pid,m=c.m,sk=equiv[pid]+'|'+rem[pid]+'|'+brem[pid];if(sym.has(sk))return;sym.add(sk);rem[pid]-=g.need;if(m.priority==='B')brem[pid]-=g.need;currentRows.push({person_id:pid,slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:g.need,priority:m.priority,used_specialty_code:m.used_specialty_code,specialty_source:m.specialty_source,source:'automatic_live_optimizer'});cur.covered+=g.need;if(m.priority==='A'||m.priority==='SPECIAL')cur.top+=g.need;else if(m.priority==='B')cur.b+=g.need;if(m.specialty_source==='primary')cur.primary+=g.need;search();if(m.specialty_source==='primary')cur.primary-=g.need;if(m.priority==='A'||m.priority==='SPECIAL')cur.top-=g.need;else if(m.priority==='B')cur.b-=g.need;cur.covered-=g.need;currentRows.pop();if(m.priority==='B')brem[pid]+=g.need;rem[pid]+=g.need;});
        search();counts[chosen]++;
      }
      search();totalNodes+=nodes;if(aborted)allCertified=false;finalRows=finalRows.concat(bestRows);
    });

    const finalPeople=JSON.parse(JSON.stringify(originalPeople)),finalSlots=JSON.parse(JSON.stringify(originalSlots));
    finalRows.forEach(function(row){const ps=finalPeople[row.person_id],ss=finalSlots[row.slot_id],h=row.hours||0;if(!ps||!ss)return;ps.remaining_hours=Math.max(0,(ps.remaining_hours||0)-h);if(row.priority==='B'){ps.b_assignment_hours=(ps.b_assignment_hours||0)+h;ps.b_remaining_hours=Math.max(0,10-ps.b_assignment_hours);}ss.remaining_hours=0;});
    return {allocations:finalRows,people:finalPeople,slots:finalSlots,summary:{auto_covered_hours:finalRows.reduce(function(t,r){return t+(r.hours||0);},0),maximum_coverage_certified:allCertified,optimizer_search_nodes:totalNodes}};
  }
  function specialtyBuildReport(state){
    const personState={}, slotState={};
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      const person=allocationPeopleData[pid];
      const assigned=state.personAssigned[pid]||0, bHours=(state.personPriority[pid]&&state.personPriority[pid].B)||0;
      personState[pid]={
        remaining_hours:Math.max(0,(person.available_here_hours||0)-assigned),
        b_assignment_hours:bHours,
        b_remaining_hours:Math.max(0,10-bHours),
        primary_code:person.specialty_code||'',
        secondary_code:person.secondary_specialty_code||''
      };
    });
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid],assigned=state.slotAssigned[sid]||0,remaining=Math.max(0,(slot.capacity_hours||0)-assigned);
      slotState[sid]={remaining_hours:remaining,atomic_blocked:assigned>0&&remaining>0};
    });
    const optimized=allocationOptimizeRemaining(personState,slotState);
    const autoAllocations=optimized.allocations;
    const autoCovered=optimized.summary.auto_covered_hours||0;
    Object.keys(personState).forEach(function(pid){personState[pid]=optimized.people[pid]||personState[pid];});
    Object.keys(slotState).forEach(function(sid){slotState[sid]=optimized.slots[sid]||slotState[sid];});

    const openRows=[], specialBuckets={}, coverageByCode={}, exclusiveByCode={};
    Object.keys(allocationSlotsData||{}).forEach(function(sid){
      const slot=allocationSlotsData[sid], hours=slotState[sid]?slotState[sid].remaining_hours:0;
      if(hours<1) return;
      const bucket=slot.reporting_bucket||null;
      if(bucket&&bucket.key){
        if(!specialBuckets[bucket.key]) specialBuckets[bucket.key]={key:bucket.key,label:bucket.label||bucket.key,gap_hours:0,slots:[]};
        specialBuckets[bucket.key].gap_hours+=hours;
        specialBuckets[bucket.key].slots.push({slot_id:sid,slot_label:slot.slot_label||'',subject:slot.subject||'',hours:hours});
        return;
      }
      const top=specialtyTopCandidates(slot), codes=top.codes.slice();
      openRows.push({slot_id:sid,slot_label:slot.slot_label||slot.label||sid,subject:slot.subject||'',hours:hours,priority:top.priority,candidate_codes:codes,legal_candidate_codes:(top.legal_codes||codes).slice(),excluded_legacy_candidate_codes:(top.excluded_legacy_codes||[]).slice()});
      codes.forEach(function(code){
        coverageByCode[code]=(coverageByCode[code]||0)+hours;
        if(codes.length===1) exclusiveByCode[code]=(exclusiveByCode[code]||0)+hours;
      });
    });
    openRows.sort(function(a,b){
      if(a.candidate_codes.length!==b.candidate_codes.length) return a.candidate_codes.length-b.candidate_codes.length;
      if(a.hours!==b.hours) return b.hours-a.hours;
      const s=String(a.subject).localeCompare(String(b.subject),'el',{numeric:true}); if(s) return s;
      return String(a.slot_id).localeCompare(String(b.slot_id),'el',{numeric:true});
    });
    const gapByCode={}, recommendations=[];
    openRows.forEach(function(row){
      const codes=row.candidate_codes.slice().sort(function(a,b){
        const ca=coverageByCode[a]||0, cb=coverageByCode[b]||0; if(ca!==cb) return cb-ca;
        const ga=gapByCode[a]||0, gb=gapByCode[b]||0; if(ga!==gb) return gb-ga;
        const ea=exclusiveByCode[a]||0, eb=exclusiveByCode[b]||0; if(ea!==eb) return eb-ea;
        return String(a).localeCompare(String(b),'el',{numeric:true});
      });
      const selected=codes.length?codes[0]:'';
      if(selected) gapByCode[selected]=(gapByCode[selected]||0)+row.hours;
      recommendations.push({slot_id:row.slot_id,slot_label:row.slot_label,subject:row.subject,hours:row.hours,priority:row.priority,selected_code:selected,candidate_codes:row.candidate_codes.slice(),legal_candidate_codes:(row.legal_candidate_codes||row.candidate_codes).slice(),excluded_legacy_candidate_codes:(row.excluded_legacy_candidate_codes||[]).slice(),selected_code_total_reachable_hours:selected?(coverageByCode[selected]||0):0,selection_kind:(row.excluded_legacy_candidate_codes&&row.excluded_legacy_candidate_codes.length)?'current_school_specialty_preference':(row.candidate_codes.length<=1?'unique_top_assignment':'smart_shared_top_assignment')});
    });
    const surplusByCode={}, surplusPeopleByCode={};
    Object.keys(personState).forEach(function(pid){
      const ps=personState[pid], hours=Math.max(0,ps.remaining_hours||0), code=ps.primary_code||'';
      if(hours<1||!code) return;
      surplusByCode[code]=(surplusByCode[code]||0)+hours;
      surplusPeopleByCode[code]=(surplusPeopleByCode[code]||0)+1;
    });
    const bySpecialty={};
    Array.from(new Set(Object.keys(gapByCode).concat(Object.keys(surplusByCode)))).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const gap=gapByCode[code]||0, surplus=surplusByCode[code]||0;
      bySpecialty[code]={code:code,label:specialtyLabelsData[code]||'',gap_hours:gap,surplus_hours:surplus,signed_balance_hours:surplus-gap,has_both_gap_and_surplus:gap>0&&surplus>0,surplus_people_count:surplusPeopleByCode[code]||0};
    });
    let finalUncovered=0, bucketGap=0, surplusTotal=0;
    Object.keys(slotState).forEach(function(sid){finalUncovered+=slotState[sid].remaining_hours||0;});
    Object.keys(specialBuckets).forEach(function(k){bucketGap+=specialBuckets[k].gap_hours||0;});
    Object.keys(surplusByCode).forEach(function(k){surplusTotal+=surplusByCode[k]||0;});
    return {
      automatic_balance:{allocations:autoAllocations,people:personState,slots:slotState,summary:{auto_covered_hours:autoCovered,remaining_slot_hours:finalUncovered,maximum_coverage_certified:optimized.summary.maximum_coverage_certified===true,optimizer_search_nodes:optimized.summary.optimizer_search_nodes||0}},
      vacancy_recommendations:recommendations,
      by_specialty:bySpecialty,
      special_reporting_buckets:specialBuckets,
      summary:{manual_unassigned_hours:state.unassigned||0,auto_internal_covered_hours:autoCovered,final_uncovered_hours:finalUncovered,specialty_gap_hours_total:Object.keys(gapByCode).reduce(function(t,k){return t+(gapByCode[k]||0);},0),special_reporting_bucket_gap_hours_total:bucketGap,surplus_hours_total:surplusTotal}
    };
  }
  let latestSpecialtyBalance=null;
  function specialtySignedText(value){ return String(value); }
  function specialtyAppendCell(row,text,className){ const td=document.createElement('td'); td.textContent=text; if(className) td.className=className; row.appendChild(td); return td; }
  function renderSpecialtyBalance(state){
    const body=document.getElementById('specialtyBalanceBody');
    if(!body) return;
    const specialtyPanel=document.querySelector('[data-staffing-panel="specialties"]');
    if(specialtyPanel&&specialtyPanel.hidden) return;
    const report=specialtyBuildReport(state); latestSpecialtyBalance=report;
    const manual=document.querySelector('[data-specialty-manual-uncovered]'), auto=document.querySelector('[data-specialty-auto-covered]'), final=document.querySelector('[data-specialty-final-uncovered]'), surplus=document.querySelector('[data-specialty-surplus-total]');
    if(manual) manual.textContent=String(report.summary.manual_unassigned_hours||0);
    if(auto) auto.textContent=String(report.summary.auto_internal_covered_hours||0);
    if(final) final.textContent=String(report.summary.final_uncovered_hours||0);
    if(surplus) surplus.textContent=String(report.summary.surplus_hours_total||0);
    body.innerHTML='';
    let rows=0;
    Object.keys(report.by_specialty||{}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const item=report.by_specialty[code]; if((item.gap_hours||0)<1&&(item.surplus_hours||0)<1) return;
      const tr=document.createElement('tr'); tr.setAttribute('data-specialty-balance-row',code);
      const c1=specialtyAppendCell(tr,code); const st=document.createElement('strong'); st.textContent=code; c1.textContent=''; c1.appendChild(st);
      specialtyAppendCell(tr,item.label||'');
      specialtyAppendCell(tr,String(item.gap_hours||0),'specialty-balance-value specialty-balance-deficit');
      specialtyAppendCell(tr,String(item.surplus_hours||0),'specialty-balance-value specialty-balance-surplus');
      specialtyAppendCell(tr,specialtySignedText(item.signed_balance_hours||0),'specialty-balance-value');
      specialtyAppendCell(tr,item.has_both_gap_and_surplus?'Ταυτόχρονο έλλειμμα και πλεόνασμα στον ίδιο κλάδο — χρειάζεται έλεγχος πριν από οριστική δήλωση.':'','specialty-balance-note');
      body.appendChild(tr); rows++;
    });
    Object.keys(report.special_reporting_buckets||{}).sort().forEach(function(key){
      const item=report.special_reporting_buckets[key]; if((item.gap_hours||0)<1) return;
      const tr=document.createElement('tr'); tr.className='specialty-balance-special'; tr.setAttribute('data-specialty-balance-row',key);
      const c1=specialtyAppendCell(tr,''); const st=document.createElement('strong'); st.textContent=item.label||key; c1.appendChild(st);
      specialtyAppendCell(tr,'Δεν αποδίδεται σε συγκεκριμένη ειδικότητα');
      specialtyAppendCell(tr,String(item.gap_hours||0),'specialty-balance-value specialty-balance-deficit');
      specialtyAppendCell(tr,'0','specialty-balance-value specialty-balance-surplus');
      specialtyAppendCell(tr,'-'+String(item.gap_hours||0),'specialty-balance-value');
      specialtyAppendCell(tr,'Ξεχωριστή γραμμή του υποδείγματος.','specialty-balance-note');
      body.appendChild(tr); rows++;
    });
    const empty=document.getElementById('specialtyBalanceEmpty'), wrap=document.getElementById('specialtyBalanceTableWrap');
    if(empty) empty.hidden=rows!==0; if(wrap) wrap.hidden=rows===0;

    const smartBody=document.getElementById('specialtySmartBody');
    if(smartBody){
      smartBody.innerHTML=''; let smartCount=0;
      (report.vacancy_recommendations||[]).forEach(function(item){
        const legalCodes=(item.legal_candidate_codes&&item.legal_candidate_codes.length)?item.legal_candidate_codes:item.candidate_codes;
        if(!legalCodes||legalCodes.length<2) return;
        const tr=document.createElement('tr'); specialtyAppendCell(tr,item.slot_label||''); specialtyAppendCell(tr,item.subject||''); specialtyAppendCell(tr,String(item.hours||0));
        const td=specialtyAppendCell(tr,''); const strong=document.createElement('strong'); strong.textContent=item.selected_code||'—'; td.appendChild(strong);
        specialtyAppendCell(tr,legalCodes.join(', ')); smartBody.appendChild(tr); smartCount++;
      });
      if(!smartCount){ const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=5; td.textContent='Δεν υπάρχουν κοινά κενά με περισσότερους από έναν ισότιμους κλάδους στην καλύτερη ανάθεση.'; tr.appendChild(td); smartBody.appendChild(tr); }
    }
    const autoBody=document.getElementById('specialtyAutoBody');
    if(autoBody){
      autoBody.innerHTML=''; let autoCount=0;
      (report.automatic_balance.allocations||[]).forEach(function(item){
        const tr=document.createElement('tr'), person=allocationPeopleData[item.person_id]||null;
        specialtyAppendCell(tr,person?(person.label||item.person_id):item.person_id); specialtyAppendCell(tr,item.slot_label||''); specialtyAppendCell(tr,item.subject||''); specialtyAppendCell(tr,String(item.hours||0));
        specialtyAppendCell(tr,allocationPriorityLabel(item.priority)+' ανάθεση'+(item.specialty_source==='secondary'?' · μέσω 2ης ειδικότητας '+item.used_specialty_code:''));
        autoBody.appendChild(tr); autoCount++;
      });
      if(!autoCount){ const tr=document.createElement('tr'); const td=document.createElement('td'); td.colSpan=5; td.textContent='Δεν εντοπίστηκαν πρόσθετες ώρες που να μπορούν να καλυφθούν αυτόματα από υπάρχον προσωπικό πέρα από την τρέχουσα κατανομή.'; tr.appendChild(td); autoBody.appendChild(tr); }
    }
    const printBody=document.getElementById('printSpecialtyBalanceBody');
    if(printBody){
      printBody.innerHTML='';
      Array.from(body.querySelectorAll('tr')).forEach(function(src){
        const cells=Array.from(src.children).map(function(td){return td.textContent||'';});
        const tr=document.createElement('tr'); cells.forEach(function(text,i){specialtyAppendCell(tr,text,(i>=2&&i<=4)?'num':'');}); printBody.appendChild(tr);
      });
    }
    const pm=document.querySelector('[data-print-specialty-manual-uncovered]'), pa=document.querySelector('[data-print-specialty-auto-covered]'), pf=document.querySelector('[data-print-specialty-final-uncovered]'), ps=document.querySelector('[data-print-specialty-surplus-total]');
    if(pm) pm.textContent=String(report.summary.manual_unassigned_hours||0); if(pa) pa.textContent=String(report.summary.auto_internal_covered_hours||0); if(pf) pf.textContent=String(report.summary.final_uncovered_hours||0); if(ps) ps.textContent=String(report.summary.surplus_hours_total||0);
    const pe=document.getElementById('printSpecialtyBalanceEmpty'); if(pe) pe.hidden=rows!==0;
  }
  function specialtyCsvCell(value){ return '"'+csvSpreadsheetSafeText(value).replace(/"/g,'""')+'"'; }
  function downloadSpecialtyBalanceCsv(){
    const state=allocationCollectState(), report=specialtyBuildReport(state); latestSpecialtyBalance=report;
    const registryEl=document.querySelector('[name="school_registry_id"]'), nameEl=document.querySelector('[name="school_name"]'), codeEl=document.querySelector('[name="school_code"]');
    const schoolRegistryId=registryEl?(registryEl.value||'').trim():'', schoolName=nameEl?(nameEl.value||'').trim():'', schoolCode=codeEl?(codeEl.value||'').trim():'';
    const rows=[['schema_version','school_registry_id','school_code','school_name','report_key','label','kind','deficit_hours','surplus_hours','balance_hours','note']];
    Object.keys(report.by_specialty||{}).sort(function(a,b){return String(a).localeCompare(String(b),'el',{numeric:true});}).forEach(function(code){
      const item=report.by_specialty[code]; if((item.gap_hours||0)<1&&(item.surplus_hours||0)<1) return;
      rows.push([specialtyReportSchemaVersion,schoolRegistryId,schoolCode,schoolName,code,item.label||'','specialty',item.gap_hours||0,item.surplus_hours||0,item.signed_balance_hours||0,item.has_both_gap_and_surplus?'Ταυτόχρονο έλλειμμα και πλεόνασμα — απαιτεί έλεγχο.':'']);
    });
    Object.keys(report.special_reporting_buckets||{}).sort().forEach(function(key){
      const item=report.special_reporting_buckets[key]; if((item.gap_hours||0)<1) return;
      rows.push([specialtyReportSchemaVersion,schoolRegistryId,schoolCode,schoolName,key,item.label||key,'subject_bucket',item.gap_hours||0,0,-(item.gap_hours||0),'Ξεχωριστή γραμμή υποδείγματος· δεν αποδίδεται αυτόματα σε ειδικότητα.']);
    });
    const text='\ufeff'+rows.map(function(row){return row.map(specialtyCsvCell).join(';');}).join('\r\n');
    const blob=new Blob([text],{type:'text/csv;charset=utf-8'}), url=URL.createObjectURL(blob), a=document.createElement('a');
    const stem=(schoolCode||schoolName||'school').replace(/[^0-9A-Za-zΑ-Ωα-ω._-]+/g,'-').replace(/^-+|-+$/g,'')||'school';
    a.href=url; a.download='staffing_balance_v1-'+stem+'.csv'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function(){URL.revokeObjectURL(url);},0);
  }
  const specialtyBalanceCsv=document.getElementById('specialtyBalanceCsv'); if(specialtyBalanceCsv) specialtyBalanceCsv.addEventListener('click',downloadSpecialtyBalanceCsv);
  const allocationSummaryEls={
    assigned:document.querySelector('[data-allocation-assigned]'),
    coverage:document.querySelector('[data-allocation-coverage]'),
    unassigned:document.querySelector('[data-allocation-unassigned]'),
    over:document.querySelector('[data-allocation-over]'),
    errors:document.querySelector('[data-allocation-errors]')
  };
  const vacancyTableWrap=document.getElementById('vacancyTableWrap');
  const vacancyEmpty=document.getElementById('vacancyEmpty');
  const printVacancyEmpty=document.getElementById('printVacancyEmpty');
  const vacancySummaryEls={
    total:document.querySelector('[data-vacancy-total]'),
    slots:document.querySelector('[data-vacancy-slots]'),
    noStaff:document.querySelector('[data-vacancy-no-staff]'),
    hasStaff:document.querySelector('[data-vacancy-has-staff]')
  };
  const allocationTotalCapacity=Object.keys(allocationSlotsData||{}).reduce(function(sum,sid){return sum+Math.max(0,(allocationSlotsData[sid]&&allocationSlotsData[sid].capacity_hours)||0);},0);
  const allocationPersonSummaryCache={};
  let allocationSummaryFrame=0;
  function scheduleAllocationSummary(){
    if(allocationSummaryFrame) return;
    const run=function(){ allocationSummaryFrame=0; updateAllocationSummary(); };
    if(typeof window.requestAnimationFrame==='function') allocationSummaryFrame=window.requestAnimationFrame(run);
    else run();
  }
  function updateAllocationSlotOptionAvailability(slotAssigned){
    allocationRows().forEach(function(row){
      const select=row.querySelector('.allocation-slot');
      if(!select) return;
      const current=select.value||'';
      Array.from(select.querySelectorAll('option[value]')).forEach(function(option){
        const sid=option.value||'';
        if(sid==='') return;
        if(option.dataset.baseAllocationLabel===undefined) option.dataset.baseAllocationLabel=option.textContent||'';
        if(option.dataset.staticDisabled===undefined) option.dataset.staticDisabled=option.disabled?'1':'0';
        const slot=allocationSlotsData[sid]||null;
        const full=!!(slot&&(slotAssigned[sid]||0)>=slot.capacity_hours);
        const dynamicallyDisabled=full&&current!==sid;
        option.disabled=option.dataset.staticDisabled==='1'||dynamicallyDisabled;
        option.textContent=option.dataset.baseAllocationLabel+(dynamicallyDisabled?' · καλύφθηκε':'');
      });
    });
  }
  function updateVacancyView(slotAssigned,personAssigned,personPriority){
    if(!vacancyRows.length){ stat51RenderComparison(slotAssigned||{}); return; }
    let total=0, slots=0, noStaff=0, hasStaff=0;
    const q=vacancyFilter?(vacancyFilter.value||'').toLocaleLowerCase('el-GR').normalize('NFD').replace(/[\u0300-\u036f]/g,''):'';
    vacancyRows.forEach(function(row){
      const sid=row.getAttribute('data-vacancy-row')||'', slot=allocationSlotsData[sid]||null;
      if(!slot){ row.hidden=true; return; }
      const remaining=Math.max(0,(slot.capacity_hours||0)-(slotAssigned[sid]||0));
      const availability=remaining>0?vacancyEligiblePeopleAvailability(sid,slot,personAssigned,personPriority):{normal:0,exceptionB:0,total:0};
      const availableCount=availability.total, cached=vacancyRowCache[sid]||{};
      const hoursEl=cached.hours||null; if(hoursEl) hoursEl.textContent=String(remaining);
      const status=cached.status||null;
      if(status){
        status.classList.remove('has-staff','no-staff');
        if(remaining<1) status.textContent='—';
        else if(availability.normal>0){ status.textContent='Υπάρχει επιλέξιμο προσωπικό με υπόλοιπο ('+availability.normal+(availability.exceptionB?' + '+availability.exceptionB+' μόνο κατ’ εξαίρεση Β΄':'')+')'; status.classList.add('has-staff'); }
        else if(availability.exceptionB>0){ status.textContent='Διαθέσιμο μόνο με κατ’ εξαίρεση υπέρβαση του ορίου Β΄ ανάθεσης ('+availability.exceptionB+')'; status.classList.add('has-staff'); }
        else { status.textContent='Δεν υπάρχει επιλέξιμο προσωπικό με διαθέσιμο υπόλοιπο'; status.classList.add('no-staff'); }
      }
      const printRow=cached.printRow||null;
      if(printRow){
        if(cached.printHours) cached.printHours.textContent=String(remaining);
        if(cached.printStatus && status) cached.printStatus.textContent=status.textContent;
        printRow.hidden=remaining<1;
      }
      row.hidden=remaining<1||(q!==''&&!(cached.search||'').includes(q));
      if(remaining>0){ total+=remaining; slots++; if(availableCount>0) hasStaff++; else noStaff++; }
    });
    if(vacancySummaryEls.total) vacancySummaryEls.total.textContent=String(total);
    if(vacancySummaryEls.slots) vacancySummaryEls.slots.textContent=String(slots);
    if(vacancySummaryEls.noStaff) vacancySummaryEls.noStaff.textContent=String(noStaff);
    if(vacancySummaryEls.hasStaff) vacancySummaryEls.hasStaff.textContent=String(hasStaff);
    if(vacancyTableWrap) vacancyTableWrap.hidden=slots===0;
    if(vacancyEmpty) vacancyEmpty.hidden=slots!==0;
    if(printVacancyEmpty) printVacancyEmpty.hidden=slots!==0;
    stat51RenderComparison(slotAssigned||{});
  }
  if(vacancyFilter) vacancyFilter.addEventListener('input',function(){ const state=allocationCollectState(); updateVacancyView(state.slotAssigned,state.personAssigned,state.personPriority); });
  function updateAllocationSummary(){
    const state=allocationCollectState();
    if(!allocationList){
      updateVacancyView(state.slotAssigned,state.personAssigned,state.personPriority);
      renderSpecialtyBalance(state);
      return;
    }
    const rowState=state.rowState, personAssigned=state.personAssigned, personPriority=state.personPriority, personSource=state.personSource, slotAssigned=state.slotAssigned;
    let errorRows=0;
    rowState.forEach(function(st){
      let error=st.finalError, warnings=st.warnings.slice();
      if(!error&&st.pid&&st.person&&(personAssigned[st.pid]||0)>st.person.available_here_hours){
        warnings.push('Υπέρβαση ατομικού διαθέσιμου ωραρίου κατά '+((personAssigned[st.pid]||0)-st.person.available_here_hours)+' ώρες.');
      }
      if(!error&&st.pid&&st.match&&st.match.priority==='B'&&(personPriority[st.pid].B||0)>10){
        warnings.push(allocationBAssignmentWarning);
      }
      if(error){ allocationSetStatus(st.row,error,'error'); errorRows++; }
      else if(st.person&&st.slot&&st.hours>0&&st.match){
        const base=allocationAssignmentLabel(st.match);
        if(warnings.length) allocationSetStatus(st.row,base+' · '+warnings.join(' '),'warning');
        else allocationSetStatus(st.row,base+' ✓','ok');
      } else allocationSetStatus(st.row,'Συμπλήρωσε μάθημα και εκπαιδευτικό.','');
    });
    if(allocationSummaryEls.assigned) allocationSummaryEls.assigned.textContent=String(state.basicAssigned);
    if(allocationSummaryEls.coverage){
      const pct=allocationTotalCapacity>0?(100*state.basicAssigned/allocationTotalCapacity):0;
      allocationSummaryEls.coverage.textContent=pct.toLocaleString('el-GR',{minimumFractionDigits:1,maximumFractionDigits:1})+'%';
    }
    if(allocationSummaryEls.unassigned) allocationSummaryEls.unassigned.textContent=String(state.unassigned);
    if(allocationSummaryEls.over) allocationSummaryEls.over.textContent=String(state.overSlots);
    if(allocationSummaryEls.errors) allocationSummaryEls.errors.textContent=String(errorRows);
    const allocationLiveStatus=document.getElementById('allocationLiveStatus');
    if(allocationLiveStatus) allocationLiveStatus.textContent='Κατανομή: '+state.basicAssigned+' ώρες κατανεμημένες, '+state.unassigned+' ακάλυπτες, '+errorRows+' γραμμές με σφάλμα.';
    const hasAllocationSlots=Object.keys(allocationSlotsData||{}).some(function(sid){return Math.max(0,(allocationSlotsData[sid]&&allocationSlotsData[sid].capacity_hours)||0)>0;});
    const hasAllocationContext=hasAllocationSlots && (staffingContextInitialAllocation || allocationRows().length>0);
    if(staffingContextAssigned){
      staffingContextAssigned.hidden=!hasAllocationContext;
      staffingContextAssigned.innerHTML='<strong>'+state.basicAssigned+'</strong> ώρες κατανεμημένες';
    }
    if(staffingContextUnassigned){
      staffingContextUnassigned.hidden=!hasAllocationContext;
      staffingContextUnassigned.innerHTML='<strong>'+state.unassigned+'</strong> ακάλυπτες';
      staffingContextUnassigned.classList.toggle('has-warning',state.unassigned>0);
    }
    updateAllocationSlotOptionAvailability(slotAssigned);
    updateVacancyView(slotAssigned,personAssigned,personPriority);
    renderSpecialtyBalance(state);
    Object.keys(allocationPeopleData||{}).forEach(function(pid){
      let summary=allocationPersonSummaryCache[pid];
      if(summary===undefined){ summary=document.querySelector('[data-allocation-person-summary="'+CSS.escape(pid)+'"]')||null; allocationPersonSummaryCache[pid]=summary; }
      if(!summary) return;
      const p=allocationPeopleData[pid], assigned=personAssigned[pid]||0, remain=Math.max(0,p.available_here_hours-assigned), aHours=personPriority[pid].A||0, bHours=personPriority[pid].B||0;
      const a=summary.querySelector('[data-person-assigned]'), r=summary.querySelector('[data-person-remaining]'), av=summary.querySelector('[data-person-a]'), bv=summary.querySelector('[data-person-b]');
      if(a) a.textContent=String(assigned); if(r) r.textContent=String(remain); if(av) av.textContent=String(aHours); if(bv){ bv.textContent=String(bHours)+'/10'; bv.classList.toggle('b-limit-over',bHours>10); }
      const source=summary.querySelector('[data-person-source-summary]');
      if(source){
        let text='Μέσω κύριας '+p.specialty_code+': '+(personSource[pid].primary||0)+' ώρ.';
        if(p.secondary_specialty_code) text+=' · μέσω 2ης '+p.secondary_specialty_code+': '+(personSource[pid].secondary||0)+' ώρ.';
        if((p.external_hours||0)>0) text+=' · '+p.external_hours+' ώρ. σε άλλη μονάδα';
        source.textContent=text;
      }
      const limitWarning=summary.querySelector('[data-person-b-warning]');
      if(limitWarning){ limitWarning.hidden=bHours<=10; limitWarning.textContent=allocationBAssignmentWarning; }
      const assignments=summary.querySelector('[data-person-assignments]');
      if(assignments){
        assignments.innerHTML='';
        const personRows=rowState.filter(function(st){return st.pid===pid&&!st.finalError&&st.match&&st.slot&&st.hours>0;});
        if(!personRows.length){ const empty=document.createElement('div'); empty.className='allocation-person-assignment-item'; empty.setAttribute('data-empty-assignment',''); empty.textContent='Δεν έχουν κατανεμηθεί μαθήματα.'; assignments.appendChild(empty); }
        else personRows.forEach(function(st){ const item=document.createElement('div'); item.className='allocation-person-assignment-item'; item.textContent=st.slot.slot_label+' · '+st.slot.subject+' · '+st.hours+' ώρ. — '+allocationAssignmentLabel(st.match); assignments.appendChild(item); });
      }
    });
  }
  function bindAllocationRow(row){
    if(!row||row.dataset.initialized==='1') return; row.dataset.initialized='1';
    const slot=row.querySelector('.allocation-slot'), person=row.querySelector('.allocation-person'), hours=row.querySelector('.allocation-hours');
    if(slot){ const initial=allocationSlotsData[slot.value]||null; if(initial&&hours) hours.max=String(initial.capacity_hours); }
    if(person) person.disabled=!(slot&&slot.value);
  }
  if(allocationList&&allocationList.dataset.eventsBound!=='1'){
    allocationList.dataset.eventsBound='1';
    allocationList.addEventListener('focusin',function(event){
      const row=event.target.closest('[data-allocation-row]'); if(!row) return;
      if(event.target.matches('.allocation-slot')) allocationEnsureSlotOptions(row);
      else if(event.target.matches('.allocation-person')) allocationEnsurePersonOptions(row);
    });
    allocationList.addEventListener('pointerdown',function(event){
      const row=event.target.closest('[data-allocation-row]'); if(!row) return;
      if(event.target.matches('.allocation-slot')) allocationEnsureSlotOptions(row);
      else if(event.target.matches('.allocation-person')) allocationEnsurePersonOptions(row);
    });
    allocationList.addEventListener('change',function(event){
      const row=event.target.closest('[data-allocation-row]'); if(!row) return;
      if(event.target.matches('.allocation-slot')){
        const slot=event.target, hours=row.querySelector('.allocation-hours'), data=allocationSlotsData[slot.value]||null;
        const person=row.querySelector('.allocation-person'); if(person) person.dataset.optionsLoaded='0';
        allocationPopulatePeopleForSlot(row,false);
        if(data&&hours){ hours.max=String(data.capacity_hours); hours.value=String(data.capacity_hours); }
        else if(hours) hours.value='0';
        updateAllocationSummary(); return;
      }
      if(event.target.matches('.allocation-person')){ updateAllocationSummary(); return; }
    });
    allocationList.addEventListener('input',function(event){ if(event.target.matches('.allocation-hours')) scheduleAllocationSummary(); });
    allocationList.addEventListener('click',function(event){
      const remove=event.target.closest('.allocation-remove'); if(!remove||!allocationList.contains(remove)) return;
      const row=remove.closest('[data-allocation-row]'); if(!row) return;
      row.remove();
      if(!allocationList.querySelector('[data-allocation-row]')){const empty=document.createElement('div');empty.id='emptyAllocationState';empty.className='allocation-empty';empty.textContent='Δεν έχει γίνει ακόμη κατανομή. Πάτησε «+ Προσθήκη μαθήματος» για να ξεκινήσεις.';allocationList.appendChild(empty);}
      updateAllocationSummary();
    });
  }
  if(allocationList) allocationRows().forEach(bindAllocationRow);
  if(addAllocation&&allocationTemplate&&allocationList){
    addAllocation.addEventListener('click',function(){
      const empty=document.getElementById('emptyAllocationState'); if(empty) empty.remove();
      const fragment=allocationTemplate.content.cloneNode(true), row=fragment.querySelector('[data-allocation-row]'); allocationList.appendChild(fragment); bindAllocationRow(row); const first=row.querySelector('.allocation-slot'); if(first) first.focus(); updateAllocationSummary();
    });
  }
  if(clearAllocation&&allocationList){
    clearAllocation.addEventListener('click',function(){
      const rows=allocationRows();
      if(rows.length&&!window.confirm('Να καθαριστεί όλη η τρέχουσα κατανομή μαθημάτων;')) return;
      rows.forEach(function(row){row.remove();});
      let empty=document.getElementById('emptyAllocationState');
      if(!empty){ empty=document.createElement('div'); empty.id='emptyAllocationState'; empty.className='allocation-empty'; empty.textContent='Δεν έχει γίνει ακόμη κατανομή. Πάτησε «Αυτόματη πρόταση κάλυψης» ή «+ Προσθήκη μαθήματος» για να ξεκινήσεις.'; allocationList.appendChild(empty); }
      updateAllocationSummary();
    });
  }
  const allocationViewButtons=Array.from(document.querySelectorAll('[data-allocation-view]'));
  const allocationViewPanels=Array.from(document.querySelectorAll('[data-allocation-view-panel]'));
  function activateAllocationView(button){
    const view=button.getAttribute('data-allocation-view');
    allocationViewButtons.forEach(function(b){
      const active=b===button;
      b.classList.toggle('is-active',active);
      b.setAttribute('aria-selected',active?'true':'false');
      b.tabIndex=active?0:-1;
    });
    allocationViewPanels.forEach(function(panel){ panel.hidden=panel.getAttribute('data-allocation-view-panel')!==view; });
  }
  allocationViewButtons.forEach(function(button){
    button.addEventListener('click',function(){ activateAllocationView(button); });
    button.addEventListener('keydown',function(event){
      if(['ArrowRight','ArrowDown','ArrowLeft','ArrowUp','Home','End'].indexOf(event.key)<0) return;
      event.preventDefault();
      const index=allocationViewButtons.indexOf(button);
      let next=index;
      if(event.key==='ArrowRight'||event.key==='ArrowDown') next=(index+1)%allocationViewButtons.length;
      else if(event.key==='ArrowLeft'||event.key==='ArrowUp') next=(index-1+allocationViewButtons.length)%allocationViewButtons.length;
      else if(event.key==='Home') next=0;
      else if(event.key==='End') next=allocationViewButtons.length-1;
      const target=allocationViewButtons[next];
      activateAllocationView(target);
      target.focus();
    });
  });
  updateAllocationSummary();
})();

