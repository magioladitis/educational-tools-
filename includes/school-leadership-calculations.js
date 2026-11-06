/* School directors: Law 4823/2021, arts 33,35,232. Pure calculation engine. */
(function(root, factory) {
  'use strict';
  if (typeof module === 'object' && module.exports) module.exports = factory();
  else root.SchoolLeadership = factory();
}(typeof globalThis !== 'undefined' ? globalThis : this, function() {
  'use strict';
  function number(v, max) { var n = Number(v); return Number.isFinite(n) ? Math.min(max === undefined ? 10000 : max, Math.max(0, n)) : 0; }
  function count(v, max) { return Math.floor(number(v, max)); }
  function months(v) { return Math.floor(number(v, 720) / 3) / 4; }
  function rounded(n) { return Math.round((n + Number.EPSILON) * 10000) / 10000; }
  function degree(related, other, first, second) {
    related = count(related, 2); other = count(other, 2);
    return related ? first + (related + other > 1 ? second : 0) : (other ? second : 0);
  }
  var works = [
    ['bookInternational', 2], ['bookGreek', 1], ['chapterInternational', .5],
    ['chapterGreek', .25], ['proceedingInternational', .5], ['proceedingGreek', .25],
    ['textbook', 1.5], ['curriculum', 1], ['software', .25], ['material', .1]
  ];
  function writing(data, rules) {
    return rules.reduce(function(sum, rule) {
      return sum + rule[1] * (count(data[rule[0]]) + count(data[rule[0] + 'Team']) / 2);
    }, 0);
  }
  function evaluation(data) {
    if (data.mode === 'first') return 0;
    if (data.mode !== 'general') return null;
    if (data.evalType === 'leader') {
      if (data.evalScore === '' || data.evalScore === undefined || Number(data.evalScore) < 50 || Number(data.evalScore) > 100) return null;
      return Math.min(25, Math.max(.5, (number(data.evalScore, 100) - 50) * .5));
    }
    var keys = data.evalType === 'three' ? ['evalA1','evalA2','evalB'] : (data.evalType === 'two' ? ['evalA1','evalB'] : []);
    if (!keys.length || keys.some(function(k) {return ['excellent','good','satisfactory'].indexOf(data[k]) < 0;})) return null;
    var e = keys.filter(function(k) {return data[k] === 'excellent';}).length;
    var g = keys.filter(function(k) {return data[k] === 'good';}).length;
    var three = {'3,0':25,'2,1':22.5,'1,2':20,'2,0':20,'0,3':17.5,'1,1':17.5,'1,0':15,'0,2':12.5,'0,1':10,'0,0':7.5};
    var two = {'2,0':25,'1,1':20,'1,0':15,'0,2':15,'0,1':10,'0,0':7.5};
    return (keys.length === 3 ? three : two)[e + ',' + g];
  }
  function calculate(d) {
    d = d || {};
    var titles = Math.min(13, degree(d.phdRelated, d.phdOther,6,5) + degree(d.masterRelated,d.masterOther,4,3) +
      (d.primary === 'yes' ? number(d.didaskaleio,1) * 2 : 0) +
      ({four:3,short:2}[d.secondDegree] || 0) + number(d.thirdDegree,1) + number(d.esdda,1));
    var training = Math.min(4, number(d.annualTraining,1) + count(d.universityTraining,2) +
      Math.min(1,Math.floor(number(d.publicHours)/10)*.1) + Math.min(1,Math.floor(number(d.ekddaHours)/10)*.1) +
      number(d.majorTraining,1) + Math.min(1,count(d.eapAnnual) + count(d.eapSemester)*.5) +
      ({B:1,B1:.5}[d.ictLevel] || 0));
    var languages = [{id:d.language1,level:d.languageLevel1},{id:d.language2,level:d.languageLevel2}];
    var unique = {};
    languages.forEach(function(l) {
      if (!l.id || l.id === d.appointmentLanguage) return;
      var value = ({C2:3,C1:2,B2:1}[l.level] || 0);
      unique[l.id] = Math.max(unique[l.id] || 0,value);
    });
    var levels = Object.keys(unique).map(function(k) {return unique[k];}).sort(function(a,b) {return b-a;});
    var languagePoints = Math.min(4,(levels[0] || 0) + ({3:2,2:1,1:.5}[levels[1]] || 0));
    var books = Math.min(3,writing(d,works));
    var articles = Math.min(2,writing(d,[['articleInternational',1],['articleGreek',.5]]));
    var higher = Math.min(2,count(d.higherSemesters)*.5);
    var academic = rounded(titles + training + languagePoints + books + articles + higher);
    // Input is total recognised teaching time. The eight qualifying years earn no points.
    var teachingYears = months(Math.max(0,number(d.teachingMonths,720)-96));
    var modelYears = months(Math.min(number(d.modelMonths,720),Math.max(0,number(d.teachingMonths,720)-96)));
    var teaching = Math.min(9,teachingYears + modelYears*.5);
    var trainer = Math.min(5,Math.floor(number(d.trainerHours)/10)*.1);
    var research = Math.min(4,count(d.researchProjects,2) + count(d.innovationProjects,3));
    var teachingGroup = Math.min(13,teaching + trainer + research);
    var guidance = Math.min(2,number(d.guidanceMonths,720)/12*.5);
    // Separate school roles from the other roles of ε γ/δ to enforce the shared school cap.
    var cOther = Math.min(6,months(d.adminCother)*1.5);
    var cSchool = Math.min(6-cOther,months(d.adminCschool)*1.5);
    var dOther = Math.min(4,months(d.adminDother)*.5);
    var dSchool = Math.min(4-dOther,months(d.adminDschool)*.5);
    var school = Math.min(6,cSchool+dSchool);
    var admin = Math.min(12,Math.min(6,months(d.adminA)*2) + Math.min(6,months(d.adminB)*1.75) +
      cOther+dOther+school+Math.min(3,months(d.adminE)*.5)+Math.min(2,months(d.adminF)*.25));
    var experience = rounded(teachingGroup+guidance+admin);
    var evalPoints = evaluation(d);
    var interview = d.interview === '' || d.interview === undefined ? null : number(d.interview,20);
    var knownMode = d.mode === 'general' || d.mode === 'first';
    var base = rounded(academic+experience);
    var pending = [];
    if (!knownMode) pending.push('Επίλεξε τη διαδικασία της προκήρυξης.');
    if (d.primary !== 'yes' && d.primary !== 'no') pending.push('Επίλεξε τη βαθμίδα της θέσης.');
    if (d.mode === 'general' && evalPoints === null) pending.push('Εκκρεμεί η αξιολόγηση.');
    if (interview === null) pending.push('Εκκρεμεί η συνέντευξη.');
    if (d.inputsConfirmed !== true) pending.push('Επιβεβαίωσε τον έλεγχο μη μοριοδοτούμενων προσόντων και επικαλύψεων.');
    return {academic:academic, experience:experience, base:base, evaluation:evalPoints, interview:interview,
      total:pending.length ? null : rounded(base+evalPoints+interview),
      known:rounded(base+(evalPoints || 0)+(interview || 0)),max:d.mode === 'first' ? 75 : (d.mode === 'general' ? 100 : null),pending:pending,
      details:{titles:titles,training:rounded(training),languages:languagePoints,books:rounded(books),articles:rounded(articles),higher:higher,
        teaching:rounded(teaching),trainer:rounded(trainer),research:research,teachingGroup:rounded(teachingGroup),guidance:rounded(guidance),admin:rounded(admin),school:rounded(school)}};
  }
  return {calculate:calculate,evaluation:evaluation,works:works};
}));
