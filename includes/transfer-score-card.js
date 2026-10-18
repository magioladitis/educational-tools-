/* Shared presentation of transfer scoring results. Calculations remain grade-specific. */
(function (root) {
 'use strict';
 function set(id, value) { var el=document.getElementById(id); if(el) el.textContent=String(value); }
 function render(prefix, data) {
   if(!data) return;
   var ids=prefix==='primary'?{total:'primaryTotal',label:'primaryTotalLabel',service:'primaryServiceResult',msd:'primaryMsdResult',coservice:'primaryCoserviceResult',family:'primaryFamilyResult',locality:'primaryLocalityResult',first:'primaryFirstResult',status:'primaryTotalStatus'}:
     {total:'totalResult',label:'transferTotalLabel',service:'servicePointsResult',msd:'msdPointsResult',coservice:'coServiceResult',family:'familyResult',locality:'localityResult',first:'firstPreferenceResult',status:'statusResult'};
   Object.keys(ids).forEach(function(key){if(Object.prototype.hasOwnProperty.call(data,key))set(ids[key],data[key]);});
 }
 root.EducationTransferScoreCard=Object.freeze({render:render});
})(typeof window !== 'undefined' ? window : globalThis);
