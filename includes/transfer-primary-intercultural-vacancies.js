/* Annual organic vacancies for PRIMARY intercultural schools, 2026–2027.
 * Transcribed from the uploaded XLSX. Snapshot, not live availability.
 * The combined PE70+PE71 row is a SHARED group, not separate vacancies.
 * Keep separated from Secondary's intercultural-vacancies.js.
 */
(function(root,factory){
  'use strict';
  var api=factory();
  if(typeof module==='object'&&module.exports)module.exports=api;
  root.EducationTransferPrimaryInterculturalVacancies=api;
}(typeof globalThis!=='undefined'?globalThis:this,function(){
  'use strict';
  var rows=[{"school":"16ο ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΧΑΝΙΩΝ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ","language":"Αλβανική","branchGroup":"ΠΕ06","originalBranch":"ΠΕ06 ΑΓΓΛΙΚΗΣ","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΧΑΝΙΩΝ"},{"school":"16ο ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΧΑΝΙΩΝ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ","language":"Αλβανική","branchGroup":"ΠΕ11","originalBranch":"ΠΕ11 ΦΥΣΙΚΗΣ ΑΓΩΓΗΣ","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΧΑΝΙΩΝ"},{"school":"16ο ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΧΑΝΙΩΝ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ","language":"Αλβανική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΧΑΝΙΩΝ"},{"school":"5ο ΟΛΟΗΜΕΡΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΟ ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΜΕΝΕΜΕΝΗΣ ΘΕΣΣΑΛΟΝΙΚΗΣ","language":"Ελληνική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":2,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΘΕΣΣΑΛΟΝΙΚΗΣ Β΄"},{"school":"6ο ΟΛΟΗΜΕΡΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΟ ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΕΛΕΥΘΕΡΙΟΥ-ΚΟΡΔΕΛΙΟΥ ΘΕΣΣΑΛΟΝΙΚΗΣ","language":"Ελληνική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΘΕΣΣΑΛΟΝΙΚΗΣ Β΄"},{"school":"6ο ΟΛΟΗΜΕΡΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΟ ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΕΥΟΣΜΟΥ ΘΕΣΣΑΛΟΝΙΚΗΣ","language":"Ελληνική","branchGroup":"ΠΕ11","originalBranch":"ΠΕ11 ΦΥΣΙΚΗΣ ΑΓΩΓΗΣ","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΘΕΣΣΑΛΟΝΙΚΗΣ Β΄"},{"school":"6ο ΟΛΟΗΜΕΡΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΟ ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΕΥΟΣΜΟΥ ΘΕΣΣΑΛΟΝΙΚΗΣ","language":"Ελληνική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":1,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΘΕΣΣΑΛΟΝΙΚΗΣ Β΄"},{"school":"ΔΗΜΟΤΙΚΟ ΣΧΟΛΕΙΟ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΑΛΣΟΥΠΟΛΗΣ ΑΤΤΙΚΗΣ","language":"Αγγλική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":4,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΑΘΗΝΑΣ Β΄"},{"school":"ΔΣ ΔΙΑΠΟΛΙΤΙΣΜΙΚΗΣ ΕΚΠΑΙΔΕΥΣΗΣ ΝΕΩΝ ΕΠΙΒΑΤΩΝ","language":"Ελληνική","branchGroup":"ΠΕ70+ΠΕ71","originalBranch":"ΔΑΣΚΑΛΟΙ ΠΕ70+71","vacancies":4,"directorate":"ΔΙΕΥΘΥΝΣΗ Π.Ε. ΘΕΣΣΑΛΟΝΙΚΗΣ Α΄"}];
  var codes=['ΠΕ06','ΠΕ11','ΠΕ70','ΠΕ71'];
  function clean(code){return String(code||'').trim().toUpperCase();}
  function matches(row,code){
    var c=clean(code);
    return row.branchGroup===c || (row.branchGroup==='ΠΕ70+ΠΕ71' && (c==='ΠΕ70'||c==='ΠΕ71'));
  }
  function schoolsForBranch(code){
    if(codes.indexOf(clean(code))<0)return [];
    return rows.filter(function(row){return matches(row,code);}).map(function(row){return Object.assign({},row);});
  }
  function count(code,school){return schoolsForBranch(code).filter(function(row){return row.school===school;})
    .reduce(function(n,row){return n+row.vacancies;},0);}
  function branches(){return codes.map(function(code){
    return {code:code,positions:schoolsForBranch(code).reduce(function(n,row){return n+row.vacancies;},0),
      shared:code==='ΠΕ70'||code==='ΠΕ71'};
  });}
  return Object.freeze({year:'2026–2027',level:'ΠΕ',totalVacancies:16,totalSchools:6,
    // Reporting categories are THREE, including a single shared PE70+PE71 category.
    reportGroups:Object.freeze({'ΠΕ06':1,'ΠΕ11':2,'ΠΕ70+ΠΕ71':13}),
    rows:rows,branches:branches,schoolsForBranch:schoolsForBranch,count:count,
    hasBranch:function(code){return codes.indexOf(clean(code))!==-1;}});
}));
