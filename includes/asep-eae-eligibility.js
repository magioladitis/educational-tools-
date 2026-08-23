(function(global){
  'use strict';

  function byId(id){ return id ? document.getElementById(id) : null; }
  function resolve(ref){ return typeof ref === 'string' ? byId(ref) : ref; }
  function checked(el){ return !!(el && el.checked); }
  function value(el){ return el ? el.value : ''; }
  function number(el){ const n = Number(value(el)); return Number.isFinite(n) ? Math.max(0, n) : 0; }

  function mainState(root, profile){
    const result = { phd:false, msc:false, retraining:false, fiveYear:false, pe11:false };
    if(profile === 'te'){
      const select = root.querySelector('[data-eae-main-select]');
      const selected = value(select);
      if(selected === 'phd') result.phd = true;
      else if(selected === 'msc') result.msc = true;
      else if(selected === 'retraining') result.retraining = true;
      else if(selected === 'aei5years' || selected === 'fiveYear') result.fiveYear = true;
      return result;
    }
    root.querySelectorAll('[data-eae-main]').forEach(function(el){
      const key = el.getAttribute('data-eae-main');
      if(Object.prototype.hasOwnProperty.call(result,key)) result[key] = checked(el);
    });
    return result;
  }

  function childDisability67(root, options){
    if(options && options.socialResult && typeof options.socialResult.childDisability67 !== 'undefined'){
      return !!options.socialResult.childDisability67;
    }
    const socialId = root.getAttribute('data-social-id') || '';
    if(socialId && global.AsepSocialCriteria && typeof global.AsepSocialCriteria.getState === 'function'){
      return !!global.AsepSocialCriteria.getState(socialId).childDisability67;
    }
    return false;
  }

  function getState(ref, options){
    const root = resolve(ref);
    if(!root) throw new Error('Δεν βρέθηκε το EAE eligibility component.');
    if(!global.EaeTableEligibility || typeof global.EaeTableEligibility.calculate !== 'function'){
      throw new Error('Δεν έχει φορτωθεί το EaeTableEligibility.');
    }
    const profile = root.getAttribute('data-eae-profile') === 'te' ? 'te' : 'pe';
    const specialtyId = root.getAttribute('data-specialty-id') || '';
    const seminar = root.querySelector('[data-eae-aux="seminar400"]');
    const months = root.querySelector('[data-eae-aux="months"]');
    return global.EaeTableEligibility.calculate({
      profile: profile,
      specialty: value(byId(specialtyId)),
      main: mainState(root, profile),
      aux: {
        seminar400: checked(seminar),
        eaeMonths: number(months),
        childDisability67: childDisability67(root, options || {})
      }
    });
  }

  function reset(ref, options){
    const root = resolve(ref);
    if(!root) return;
    root.querySelectorAll('[data-eae-main], [data-eae-aux="seminar400"]').forEach(function(el){
      if(el.type === 'checkbox' || el.type === 'radio') el.checked = false;
    });
    const select = root.querySelector('[data-eae-main-select]');
    if(select) select.value = 'none';
    const months = root.querySelector('[data-eae-aux="months"]');
    if(months) months.value = '0';
    if(!(options && options.silent)){
      document.dispatchEvent(new CustomEvent('asep-eae-eligibility-change', {detail:{id:root.id || ''}}));
    }
  }

  global.AsepEaeEligibility = Object.freeze({ getState:getState, calculate:getState, reset:reset });
})(window);
