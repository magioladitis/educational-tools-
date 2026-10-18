/* Common, safe, A4-oriented printing for the P.E./D.E. transfer checklists.
 * Grading, obligations and recipient selection are owned by the grade controllers.
 * The printer receives text-only normalized rows; no data is stored/transmitted.
 */
(function(global){
 'use strict';
 function esc(v){return String(v==null?'':v).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});}
 function p(text){return '<p>'+esc(text).replace(/\n/g,'<br>')+'</p>';}
 function report(data){
  if(!data || !Array.isArray(data.rows))throw new TypeError('Checklist print rows are required');
  var sections=(data.sections||[]).map(function(section){
   return '<section><h2>'+esc(section.title)+'</h2>'+(section.lines||[]).filter(Boolean).map(p).join('')+'</section>';
  }).join('');
  var items=data.rows.map(function(item){
   var mark=item.disabled?'—':item.action?(item.done?'☑':'☐'):'ⓘ';
   return '<li><span class="mark">'+mark+'</span> <strong>'+esc(item.title)+'</strong>'+
     (item.detail?p(item.detail):'')+(item.extra?p(item.extra):'')+
     (item.channel?'<small>'+esc(item.channel)+'</small>':'')+'</li>';
  }).join('');
  return '<!doctype html><html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'+
   '<title>Λίστα δικαιολογητικών μετάθεσης</title><style>'+ 
   '@page{size:A4;margin:16mm}body{font:13px/1.5 Arial,sans-serif;color:#172336;max-width:800px;margin:24px auto;padding:0 16px}'+
   'header{border-bottom:2px solid #314d7a;margin-bottom:14px}h1{font-size:21px;margin:6px 0}h2{font-size:16px;margin:20px 0 5px}'+
   'p{margin:5px 0 9px}ul{padding-left:24px}li{margin:0 0 15px;break-inside:avoid}li p{margin:4px 0}'+
   '.mark{font-size:15px}small{display:block;color:#4c5869;font-size:11px;margin-top:4px}'+
   '.notice{border-top:1px solid #d8e0e9;padding-top:9px;margin-top:18px;font-size:11px;color:#4c5869}'+
   '@media print{body{margin:0;padding:0}section{break-inside:auto}}'+
   '</style></head><body><header><strong>Εργαλειοθήκη Εκπαιδευτικού</strong><h1>Έλεγχος δικαιολογητικών μετάθεσης</h1>'+p(data.grade||'')+p(data.mode||'')+'</header>'+sections+
   '<section><h2>Προσωπική λίστα δικαιολογητικών</h2>'+(data.progress?p(data.progress):'')+
   '<ul>'+items+'</ul></section><p class="notice">'+esc(data.notice||'Ενδεικτική προσωπική λίστα — δεν αποτελεί βεβαίωση υποχρεωτικών εγγράφων. Ελέγξτε την εγκύκλιο και την αρμόδια Διεύθυνση Εκπαίδευσης.')+'</p></body></html>';
 }
 function print(data){
  var html=report(data);
  var win=global.open('','_blank');
  if(!win)return false;
  win.document.write(html);win.document.close();win.focus();win.print();
  return true;
 }
 global.EducationTransferChecklistPrint=Object.freeze({render:report,print:print});
})(typeof window!=='undefined'?window:globalThis);
