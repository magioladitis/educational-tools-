/* Shared transfer-email interactions for both education levels.
 * This module deliberately has no knowledge of official circulars, routes,
 * document lists, or message formats. Each level owns those legal rules.
 * All state is in memory; nothing is stored or transmitted.
 */
(function(global){
'use strict';
function mailto(parts){
 return 'mailto:?subject='+encodeURIComponent(parts.subject)+'&body='+encodeURIComponent(parts.body);
}
function open(disclosure, trigger, personalizeSelector){
 if(!disclosure)return;
 disclosure.open=true;
 var personalize=disclosure.querySelector(personalizeSelector||'[data-transfer-personalize]');
 if(personalize)personalize.open=true;
 if(trigger)trigger.setAttribute('aria-expanded','true');
 if(disclosure.scrollIntoView)disclosure.scrollIntoView({block:'start',behavior:'smooth'});
}
function bindState(disclosure,trigger){
 if(!disclosure)return;
 disclosure.addEventListener('toggle',function(){
  if(trigger)trigger.setAttribute('aria-expanded',disclosure.open?'true':'false');
 });
}
function fallbackCopy(value){
 var field=document.createElement('textarea');
 field.value=value;
 field.setAttribute('readonly','');
 field.style.position='fixed';field.style.left='-9999px';
 document.body.appendChild(field);field.select();
 var success=false;
 try{success=!!document.execCommand('copy');}catch(err){success=false;}
 document.body.removeChild(field);
 return success;
}
function copy(value,messages,feedback){
 if(!feedback)return;
 var brackets=/\[[^\]]+\]/.test(value);
 function report(ok,kind){
  if(kind==='native')feedback.textContent=ok?(brackets?messages.nativeBrackets:messages.nativeSuccess):messages.nativeFailure;
  else feedback.textContent=ok?(brackets&&messages.fallbackBrackets?messages.fallbackBrackets:messages.fallbackSuccess):messages.fallbackFailure;
 }
 if(typeof navigator!=='undefined'&&navigator.clipboard&&navigator.clipboard.writeText){
  try{
   navigator.clipboard.writeText(value).then(function(){report(true,'native');},function(){report(fallbackCopy(value),'fallback');});
  }catch(err){report(fallbackCopy(value),'fallback');}
 }else report(fallbackCopy(value),'fallback');
}
/* One shared presentation for specialty dropdowns. The level owns its allowed catalogue.
 * The selected value is the canonical code; the description is display-only.
 */
function escapeHtml(value){
 return String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;')
  .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}
function specialtyOptions(catalog,selected){
 catalog=catalog||{};
 return Object.keys(catalog).sort(function(a,b){return a.localeCompare(b,'el');}).map(function(code){
  return '<option value="'+escapeHtml(code)+'"'+(selected===code?' selected':'')+'>'+escapeHtml(code+' — '+catalog[code])+'</option>';
 }).join('');
}
function fillSpecialtyDropdown(select,catalog,selected){
 if(!select)return;
 select.innerHTML='<option value="">— Επιλέξτε ειδικότητα —</option>'+specialtyOptions(catalog,selected||'');
}
/* One authoritative source for common UI wording. Legal text stays in each level. */
var labels=Object.freeze({
 emailTitle:'Προετοιμασία μηνύματος υποβολής δικαιολογητικών',
 mydocsTitle:'Έτοιμο υπόδειγμα υποβολής μέσω mydocs',
 optionalTitle:'Υπόδειγμα μηνύματος — αν χρειαστεί',
 mixedTitle:'Προετοιμασία email και υποβολής μέσω mydocs',
 emailButton:'Δημιουργία email υποβολής',
 mydocsButton:'Προετοιμασία κειμένου για mydocs',
 optionalButton:'Προβολή προαιρετικού υποδείγματος',
 mixedButton:'Προετοιμασία μηνύματος υποβολής',
 subject:'Θέμα:',body:'Σώμα κειμένου:',
 copy:'Αντιγραφή',
 clear:'Καθαρισμός πεδίων',
 personalize:'Δημιουργία έτοιμου μηνύματος (προαιρετικά)',
 templateHeading:'Θέμα και σώμα μηνύματος — υπόδειγμα',
 openMail:'Άνοιγμα στο email μου ↗',
 recipientHint:'Ο παραλήπτης δεν συμπληρώνεται αυτόματα· προσθέστε εσείς τα συνημμένα. Δεν πραγματοποιείται αποστολή.',
 copySuccess:'Το κείμενο αντιγράφηκε.',
 copyBrackets:'Αντιγράφηκε. Συμπληρώστε τα πεδία που απομένουν στις αγκύλες.',
 copyFallbackFailure:'Επιλέξτε το κείμενο και αντιγράψτε το χειροκίνητα.',
 copyNativeFailure:'Δεν ήταν δυνατή η αντιγραφή. Επιλέξτε το κείμενο χειροκίνητα.'
});
function label(key){return labels[key];}
function stateCopy(channel){
 var prefix=channel==='mydocs'?'mydocs':channel==='mixed'?'mixed':channel==='optional'?'optional':'email';
 return {title:labels[prefix+'Title'],button:labels[prefix+'Button']};
}
/* Apply only labels shared across education levels. Never touch official templates. */
function syncPrimaryStatic(root){
 if(!root)return;
 var selectors={
  '.transfer-submission-template > h4':'templateHeading',
  '.transfer-submission-template__personalize > summary':'personalize',
  '.transfer-submission-template__clear':'clear',
  '#primaryCopySubject':'copy',
  '#primaryCopyBody':'copy',
  '#primaryOpenMail':'openMail'
 };
 Object.keys(selectors).forEach(function(selector){
  var node=root.querySelector(selector);
  if(node)node.textContent=labels[selectors[selector]];
 });
 var bars=root.querySelectorAll('.transfer-submission-template__bar strong');
 if(bars[0])bars[0].textContent=labels.subject;
 if(bars[1])bars[1].textContent=labels.body;
 var hint=root.querySelector('.transfer-submission-template__email-action span');
 if(hint)hint.textContent=labels.recipientHint;
}
global.EducationTransferEmailComponent=Object.freeze({mailto:mailto,open:open,bindState:bindState,copy:copy,labels:labels,label:label,stateCopy:stateCopy,syncPrimaryStatic:syncPrimaryStatic,specialtyOptions:specialtyOptions,fillSpecialtyDropdown:fillSpecialtyDropdown});
})(typeof window!=='undefined'?window:this);
