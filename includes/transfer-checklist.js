/* Shared preparation-checklist state for both transfer guides.
 * Grade-specific evidence, routing and markup deliberately remain in their controllers.
 * In-memory only: nothing is persisted or sent to a server.
 */
(function(root){
 'use strict';
 function create(){
  var completed=Object.create(null);
  function validId(id){return typeof id==='string' && id.length>0;}
  function set(id,value){if(!validId(id))return;if(value)completed[id]=true;else delete completed[id];}
  function isComplete(id){return validId(id) && completed[id]===true;}
  // Discard checks for removed/informational entries so they cannot leak across modes.
  // The supplied predicate is grade-specific; shared state never determines legal duties.
  function reconcile(rows,isAction){
   var live=Object.create(null),total=0,done=0;
   rows.forEach(function(row){
    if(!row || !validId(row.id) || !isAction(row))return;
    if(live[row.id])return; // no duplicate preparation tasks
    live[row.id]=true;total++;
    if(isComplete(row.id))done++;
   });
   Object.keys(completed).forEach(function(id){if(!live[id])delete completed[id];});
   return {completed:done,total:total};
  }
  function progressText(counts,grade){
   if(grade==='primary')return counts.completed+' από '+counts.total+' ενέργειες δικαιολογητικών έχουν ολοκληρωθεί';
   return counts.total?'Δικαιολογητικά/ενέργειες που ετοιμάστηκαν: '+counts.completed+' από '+counts.total+'.':
    'Δεν προκύπτουν δικαιολογητικά προς προετοιμασία από τις επιλογές σας.';
  }
  return Object.freeze({set:set,isComplete:isComplete,reconcile:reconcile,progressText:progressText});
 }
 root.EducationTransferChecklist=Object.freeze({create:create});
})(typeof window!=='undefined'?window:globalThis);
