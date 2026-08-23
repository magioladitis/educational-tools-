/*
 * Shared ASEP PE academic UI/controller.
 * Scoring: EducationAcademic.
 * Languages: AsepLanguageSelector / EducationLanguages.
 * Profiles: general / eep / eae.
 */
(function (global) {
  'use strict';

  function byId(id){ return id ? document.getElementById(id) : null; }
  function getContainer(ref){ return typeof ref === 'string' ? byId(ref) : ref; }
  function idOf(container,key,fallback){ return container && container.dataset[key] ? container.dataset[key] : fallback; }
  function valueOf(id){ var el=byId(id); if(!el)return ''; if(el.type==='checkbox')return el.checked?'yes':'no'; return el.value; }
  function yes(id){ var el=byId(id); if(!el||el.disabled)return false; if(el.type==='checkbox')return !!el.checked; return el.value==='yes'; }
  function number(value){ var n=Number(String(value==null?'':value).trim().replace(',','.')); return Number.isFinite(n)?n:0; }
  function profileOf(container){ var p=String(idOf(container,'profile','general')||'general').toLowerCase(); return p==='eep'||p==='eae'?p:'general'; }
  function booleanData(container,key,fallback){ if(!container||container.dataset[key]===undefined)return fallback; var v=String(container.dataset[key]).toLowerCase(); return v==='1'||v==='true'||v==='yes'; }

  function specialtyId(c){return idOf(c,'specialtyId','specialty');}
  function degreeId(c){return idOf(c,'degreeId','degreeGrade');}
  function secondDegreeId(c){return idOf(c,'secondDegreeId','secondDegree');}
  function phdId(c){return idOf(c,'phdId','phd');}
  function mscId(c){return idOf(c,'mscId','mscCount');}
  function languageId(c){return idOf(c,'languageId','asepLanguages');}
  function computerId(c){return idOf(c,'computerId','computer');}
  function trainingId(c){return idOf(c,'trainingId','training');}
  function trainingProofId(c){return idOf(c,'trainingProofId','trainingProof');}
  function phdOverlayId(c){return idOf(c,'phdOverlayId','');}
  function mscOverlayId(c){return idOf(c,'mscOverlayId','');}
  function trainingOverlayId(c){return idOf(c,'trainingOverlayId','');}
  function eaePe11SpecialId(c){return idOf(c,'eaePe11SpecialId','');}
  function pe11WrapId(c){return idOf(c,'pe11WrapId','');}
  function pe6171NoteId(c){return idOf(c,'pe6171NoteId','');}

  function sync(containerRef){
    var container=getContainer(containerRef); if(!container)return null;
    var profile=profileOf(container), specialty=global.EducationCore.normalizeSpecialtyCode(valueOf(specialtyId(container)));
    var computer=byId(computerId(container));
    if(computer){
      var excluded=specialty==='ΠΕ86'; computer.disabled=excluded;
      if(excluded){ if(computer.type==='checkbox')computer.checked=false; else computer.value='no'; }
    }
    if(profile==='eae'){
      var pe11=byId(pe11WrapId(container)); if(pe11)pe11.classList.toggle('hidden',specialty!=='ΠΕ11');
      var pe6171=byId(pe6171NoteId(container)); if(pe6171)pe6171.classList.toggle('hidden',!(specialty==='ΠΕ61'||specialty==='ΠΕ71'));
    }
    if(global.AsepLanguageSelector)global.AsepLanguageSelector.sync(languageId(container));
    if(global.AsepComputerProof)global.AsepComputerProof.syncAll();
    if(global.TrainingProof)global.TrainingProof.syncAll();
    return getState(container);
  }

  function getState(containerRef){
    var container=getContainer(containerRef); if(!container)return null;
    var profile=profileOf(container);
    var languages=global.AsepLanguageSelector?global.AsepLanguageSelector.calculate(languageId(container)):{points:0,details:[],warnings:[]};
    var state={
      profile:profile, specialty:global.EducationCore.normalizeSpecialtyCode(valueOf(specialtyId(container))), degreeGrade:valueOf(degreeId(container)),
      secondDegree:yes(secondDegreeId(container)), phd:yes(phdId(container)),
      mscCount:parseInt(valueOf(mscId(container)),10)||0, languages:languages,
      computer:yes(computerId(container)), training:yes(trainingId(container)), eaePe11Specialization:false
    };
    if(profile==='eae'){
      state.phd=state.phd||yes(phdOverlayId(container));
      state.mscCount=Math.max(state.mscCount,yes(mscOverlayId(container))?1:0);
      state.training=state.training||yes(trainingOverlayId(container));
      state.eaePe11Specialization=yes(eaePe11SpecialId(container));
    }
    return state;
  }

  function validate(containerRef){
    var c=getContainer(containerRef), state=getState(c);
    if(!state)return {valid:false,specialty:'',degreeGrade:0,degreeValid:false};
    var grade=number(state.degreeGrade), degreeValid=grade>=5&&grade<=10;
    var degreeRequired=booleanData(c,'degreeRequired',state.profile==='general');
    return {valid:!!state.specialty&&(!degreeRequired||degreeValid),specialty:state.specialty,degreeGrade:grade,degreeValid:degreeValid,degreeRequired:degreeRequired};
  }

  function calculate(containerRef){
    var c=getContainer(containerRef); if(!c)throw new Error('Δεν βρέθηκε το κοινό block ακαδημαϊκών προσόντων ΠΕ.');
    sync(c); var state=getState(c), degreeRequired=booleanData(c,'degreeRequired',state.profile==='general');
    return global.EducationAcademic.calculate({
      profile:state.profile,specialty:state.specialty,degreeGrade:state.degreeGrade,degreeRequired:degreeRequired,
      secondDegree:state.secondDegree,phd:state.phd,mscCount:state.mscCount,eaePe11Specialization:state.eaePe11Specialization,
      languagePoints:state.languages.points,languageDetails:state.languages.details,languageWarnings:state.languages.warnings,
      computer:state.computer,training:state.training
    });
  }

  function trainingWarning(ref){var c=getContainer(ref); return c&&global.TrainingProof?global.TrainingProof.warning(trainingProofId(c)):'';}
  function trainingSummary(ref){var c=getContainer(ref); return c&&global.TrainingProof?global.TrainingProof.summary(trainingProofId(c)):'';}
  function resetElement(id){var el=byId(id); if(!el)return; if(el.type==='checkbox'||el.type==='radio')el.checked=false; else if(el.tagName==='SELECT')el.selectedIndex=0; else el.value='';}
  function reset(containerRef,options){
    var c=getContainer(containerRef); if(!c)return;
    resetElement(degreeId(c));
    [secondDegreeId(c),phdId(c),computerId(c),trainingId(c),phdOverlayId(c),mscOverlayId(c),trainingOverlayId(c),eaePe11SpecialId(c)].forEach(resetElement);
    var msc=byId(mscId(c)); if(msc)msc.value='0';
    if(global.AsepLanguageSelector)global.AsepLanguageSelector.reset(languageId(c),{silent:true});
    var proof=byId(trainingProofId(c)); if(proof)proof.querySelectorAll('input[type="radio"]').forEach(function(r){r.checked=false;});
    sync(c); if(!(options&&options.silent))emitChange(c);
  }
  function emitChange(c){if(c)c.dispatchEvent(new CustomEvent('asep-pe-academic-change',{bubbles:true,detail:getState(c)}));}
  function init(c){
    if(!c||c.dataset.peAcademicReady==='1')return;
    c.dataset.peAcademicReady='1';
    var s=byId(specialtyId(c));
    var degree=byId(degreeId(c));
    if(global.EducationCore&&global.EducationCore.bindBoundedNumberInput&&degree){
      global.EducationCore.bindBoundedNumberInput(degree,{min:5,max:10});
    }
    if(s)s.addEventListener('change',function(){sync(c);emitChange(c);});
    sync(c);
  }
  function initAll(){document.querySelectorAll('[data-component="asep-pe-academic"]').forEach(init);}

  global.AsepPeAcademic=Object.freeze({initAll:initAll,sync:sync,getState:getState,validate:validate,calculate:calculate,reset:reset,trainingWarning:trainingWarning,trainingSummary:trainingSummary});
  initAll(); if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',initAll);
})(typeof window!=='undefined'?window:globalThis);
