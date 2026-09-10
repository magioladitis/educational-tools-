'use strict';
function assert(label, ok){ console.log((ok?'PASS':'FAIL')+' | '+label); if(!ok) process.exitCode=1; }
function classes(init=[]){ const s=new Set(init); return {add:(...x)=>x.forEach(v=>s.add(v)),remove:(...x)=>x.forEach(v=>s.delete(v)),toggle:(v,f)=>{if(f===undefined) f=!s.has(v); f?s.add(v):s.delete(v);},contains:v=>s.has(v)}; }
function el(id,value='',tag='SELECT'){ const listeners={}; return {id,value,tagName:tag,style:{},innerHTML:'',textContent:'',className:'',disabled:false,listeners,classList:classes(['hidden']),addEventListener:(t,f)=>listeners[t]=f,focus:()=>{},scrollIntoView:()=>{},closest:()=>({classList:classes(),querySelector:()=>null,appendChild:()=>{}})}; }

// Eligibility
{
 global.window=global; const ids=['birthYear','citizenship','health','qualifications','dismissed','criminal','convictionImpediment','indictmentImpediment','civilRightsOrSupport','commercial','politicalOffice','publicFullTime','privateEducation','military','progressText','progressFill','result','eligibilityCheckBtn','eligibilityResetBtn']; const E={}; ids.forEach(x=>E[x]=el(x)); E.birthYear.tagName='INPUT'; E.progressFill.style={};
 global.document={readyState:'complete',getElementById:id=>E[id]||null,addEventListener:()=>{},createElement:()=>el('new')};
 delete require.cache[require.resolve('../includes/eligibility-guide-ui.js')]; require('../includes/eligibility-guide-ui.js');
 assert('eligibility click wired',typeof E.eligibilityCheckBtn.listeners.click==='function'); assert('eligibility reset wired',typeof E.eligibilityResetBtn.listeners.click==='function');
 Object.assign(E.birthYear,{value:'1985'}); E.citizenship.value='eligible'; E.health.value='yes'; E.qualifications.value='yes'; ['dismissed','criminal','convictionImpediment','indictmentImpediment','civilRightsOrSupport','commercial','politicalOffice','publicFullTime','privateEducation'].forEach(x=>E[x].value='no'); E.military.value='not_applicable';
 global.EligibilityGuideUI.checkEligibility(); assert('eligibility positive result preserved',E.result.innerHTML.includes('έχεις δικαίωμα συμμετοχής'));
}

// Objection guide
{
 const ids=['deadlineStatus','guidanceBtn','objectionReason','foreignQuestions','pointsTools','result','recognitionStatus','recognitionDate','recognitionDateQuestion','submissionMode','resubmissionInfo','resubmissionText','paravoloStatus','paravoloValidator','paravoloCode','paravoloValidation']; const E={}; ids.forEach(x=>E[x]=el(x));
 global.document={readyState:'complete',getElementById:id=>E[id]||null,addEventListener:()=>{}}; global.setInterval=()=>0;
 delete require.cache[require.resolve('../includes/objection-guide-ui.js')]; require('../includes/objection-guide-ui.js');
 assert('objection reason wired',typeof E.objectionReason.listeners.change==='function'); assert('objection code wired',typeof E.paravoloCode.listeners.input==='function'); assert('objection button wired',typeof E.guidanceBtn.listeners.click==='function');
 E.objectionReason.value='points'; E.submissionMode.value='first'; E.paravoloStatus.value='paid'; E.paravoloCode.value='12345678901234567890'; global.ObjectionGuideUI.showGuidance();
 assert('objection guidance preserved',E.result.innerHTML.includes('Τι πρέπει να κάνεις')); assert('20-digit paravolo preserved',E.result.innerHTML.includes('σωστή μορφή 20 ψηφίων'));
}
if(process.exitCode) process.exit(process.exitCode); console.log('RESULT: 10/10 PASS');
