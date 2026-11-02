/* Shared qualification vocabulary; procedure-specific decisions remain in their adapters.
   No degree catalogue is loaded: an unchecked/unknown title is not a rejection. */
(function(root, factory){
  var api=factory();
  if(typeof module==='object' && module.exports) module.exports=api;
  root.EaeQualifications=api;
}(typeof window!=='undefined'?window:globalThis,function(){
  'use strict';
  var aliases=Object.freeze({phd:'phd',doctorate:'phd',msc:'msc',masters:'msc',retraining:'retraining',two_year:'retraining',fiveYear:'fiveYear',aei5years:'fiveYear',experience:'fiveYear',pe11:'pe11'});
  var keys=Object.freeze(['phd','msc','retraining','fiveYear','pe11']);
  function canonical(key){return Object.prototype.hasOwnProperty.call(aliases,key)?aliases[key]:null;}
  function years(value){
    if(value==null || String(value).trim()==='') return null;
    var n=Number(String(value).replace(',','.'));
    return Number.isFinite(n)&&n>=0&&n<=50?n:null;
  }
  function assess(key, options){
    options=options||{};
    var id=canonical(key);
    if(!id) return {key:null,status:'unknown'};
    if(id==='pe11' && options.pe11Allowed!==true) return {key:id,status:'invalid'};
    if(id==='fiveYear' && options.requireYears===true){
      var n=years(options.years);
      return {key:id,status:n===null?'missing':n<5?'invalid':'valid'};
    }
    return {key:id,status:'valid'};
  }
  return Object.freeze({keys:keys,canonical:canonical,years:years,assess:assess});
}));
