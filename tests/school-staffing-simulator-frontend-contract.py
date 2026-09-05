#!/usr/bin/env python3
from pathlib import Path
import subprocess, textwrap, re
ROOT=Path(__file__).resolve().parents[1]
PAGE=ROOT/'ypologismos-didaktikon-anagkon.php'
ERG=ROOT/'ergaleia.php'
checks=[]
def check(name,cond): checks.append((name,bool(cond)))

def render(post=None):
    if post is None:
        p=subprocess.run(['php',str(PAGE)],cwd=ROOT,text=True,capture_output=True)
    else:
        php=['<?php','$_SERVER["REQUEST_METHOD"]="POST";','$_POST=array(']
        for k,v in post.items():
            if isinstance(v,int): val=str(v)
            else: val='"'+str(v).replace('\\','\\\\').replace('"','\\"')+'"'
            php.append('"'+k+'"=>'+val+',')
        php += [');','include "'+str(PAGE).replace('\\','/')+'";']
        p=subprocess.run(['php'],cwd=ROOT,text=True,input='\n'.join(php),capture_output=True)
    if p.returncode:
        print(p.stderr); raise SystemExit(p.returncode)
    return p.stdout

get=render()
check('page renders', 'Υπολογισμός διδακτικών αναγκών σχολικής μονάδας' in get)
check('Gymnasium supported', 'Ημερήσιο Γυμνάσιο' in get)
check('GEL supported', 'Ημερήσιο Γενικό Λύκειο' in get)
check('personnel tab exists', 'data-staffing-tab="personnel"' in get and '>3. Εκπαιδευτικοί<' in get)
check('automatic placement not exposed', 'Αυτόματες τοποθετήσεις' in get and 'Όχι ακόμη' in get)
check('official vacancy disclaimer exists', 'δεν χαρακτηρίζει τις ώρες ως επίσημα «κενά»' in get)
check('ergaleia links simulator', 'href="ypologismos-didaktikon-anagkon.php"' in ERG.read_text(encoding='utf-8'))
check('tool card number 32 exists', '<span class="tool-number">32</span>' in ERG.read_text(encoding='utf-8'))
check('Ethics is collapsible', '<details class="option-panel" id="ethicsPanel"' in get)
check('Technology Informatics split is collapsible', '<details class="option-panel" id="technologyInformaticsPanel"' in get)
check('Technology split source and 21-student rule visible', 'πάνω από 21 μαθητές' in get and '74472/Δ2/2020' in get)

gym={
'school_type':'gymnasio','school_name':'Contract Gym',
'gym_general_a':2,'gym_general_b':2,'gym_general_c':1,
'gym_lang_a_fr':1,'gym_lang_a_de':1,'gym_lang_a_it':0,
'gym_lang_b_fr':2,'gym_lang_b_de':0,'gym_lang_b_it':0,
'gym_lang_c_fr':0,'gym_lang_c_de':0,'gym_lang_c_it':1,
'ethics_a_exempt':12,'ethics_a_timely':'1','ethics_a_equivalent':0,
'ethics_b_exempt':8,'ethics_b_timely':'1',
'ethics_c_exempt':10,'ethics_c_timely':'1'
}
g=render(gym)
check('complete Gym profile accepted', 'Τα στοιχεία της σχολικής μονάδας είναι δομικά πλήρη' in g)
check('Gym synthetic total 171 rendered', re.search(r'<strong>171</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>',g) is not None)
check('Gym staffing matrix rendered', 'id="staffingMatrixTable"' in g)
check('Gym PE02 rendered', '>ΠΕ02<' in g)
check('Gym language PE05 rendered', '>ΠΕ05<' in g)
check('Gym language PE34 rendered', '>ΠΕ34<' in g)
check('Skills Workshops collapsed to one generic row', 'Οποιαδήποτε ειδικότητα</span> · Εργαστήρια Δεξιοτήτων' in g)
gym_codes=re.findall(r'<tr class="staffing-code-row"[^>]*data-search="(ΠΕ[^ "<]+)',g)
def natural_code_key(value):
    return [int(part) if part.isdigit() else part for part in re.split(r'(\d+)',value)]
check('Gym branch rows use natural specialty-code order', gym_codes == sorted(gym_codes,key=natural_code_key))
check('Skills Workshops own summary is 5 hours', re.search(r'<strong>5</strong><span>ώρες Εργαστηρίων Δεξιοτήτων · συγκεντρωτικά</span>',g) is not None)
pe08_row=re.search(r'<tr class="staffing-code-row"[^>]*data-search="ΠΕ08[^"]*".*?</tr>',g,re.S)
check('Skills Workshops removed from branch claim rows', pe08_row is not None and 'Εργαστήρια Δεξιοτήτων' not in pe08_row.group(0))
check('Shared top summary excludes collapsed Skills Workshops', re.search(r'<strong>29</strong><span>ώρες κοινής κορυφαίας Α΄/Β΄/Γ΄</span>',g) is not None)
check('Ethics panel reopens when inputs are submitted', 'id="ethicsPanel" open' in g)

gym_split=dict(gym)
gym_split.update({'gym_tech_split_a':1,'gym_tech_split_b':1,'gym_tech_split_c':1})
gs=render(gym_split)
check('Gym split total 179 rendered', re.search(r'<strong>179</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>',gs) is not None)
check('Technology panel reopens when split inputs are positive', 'id="technologyInformaticsPanel" open' in gs)
check('split inputs preserved', 'name="gym_tech_split_a"' in gs and 'value="1"' in gs)

check('split fields expose declared-section maximum', 'name="gym_tech_split_a"' in gs and 'max="2"' in gs and 'data-max-source="gym_general_a"' in gs)
invalid_split=dict(gym)
invalid_split.update({'gym_general_a':2,'gym_tech_split_a':5})
gi=render(invalid_split)
check('crafted over-limit split is reported', 'Τα τμήματα με πάνω από 21 μαθητές δεν μπορούν να είναι περισσότερα από τα δηλωμένα τμήματα της ίδιας τάξης.' in gi)
check('over-limit split makes profile partial', 'Μερικός υπολογισμός' in gi)

gel={
'school_type':'gel','school_name':'Contract GEL',
'gel_general_a':3,'gel_general_b':2,'gel_general_c':3,
'gel_lang_a_fr':2,'gel_lang_a_de':1,'gel_lang_b_fr':1,'gel_lang_b_de':1,
'gel_b_hum':1,'gel_b_sci':1,
'gel_c_hum':1,'gel_c_scihealth':2,'gel_c_econit':1,
'gel_c_field_math':1,'gel_c_field_bio':1,
'gel_c_cond_math':1,'gel_c_cond_history':3,
'ethics_a_exempt':12,'ethics_a_timely':'1','ethics_a_equivalent':1,
'ethics_b_exempt':10,'ethics_b_timely':'1','ethics_b_equivalent':0,
'ethics_c_exempt':5,'ethics_c_timely':'1'
}
l=render(gel)
check('complete GEL profile accepted', 'Τα στοιχεία της σχολικής μονάδας είναι δομικά πλήρη' in l)
check('GEL synthetic total 293 rendered', re.search(r'<strong>293</strong><span>ώρες με αντιστοιχισμένη ανάθεση</span>',l) is not None)
gel_codes=re.findall(r'<tr class="staffing-code-row"[^>]*data-search="(ΠΕ[^ "<]+)',l)
check('GEL branch rows use natural specialty-code order', gel_codes == sorted(gel_codes,key=natural_code_key))
check('GEL orientation fields preserved', 'name="gel_c_scihealth" value="2"' in l)
check('GEL 2nd/3rd field inputs preserved', 'name="gel_c_field_math" value="1"' in l and 'name="gel_c_field_bio" value="1"' in l)
check('GEL conditional inputs preserved', 'name="gel_c_cond_math" value="1"' in l and 'name="gel_c_cond_history" value="3"' in l)

partial=render({'school_type':'gel','gel_general_a':2,'gel_general_b':2,'gel_general_c':2})
check('incomplete profile flagged partial', 'Μερικός υπολογισμός' in partial)
check('missing orientations explained', 'προσανατολισμού' in partial.lower())
check('matrix semantics warning exists', 'οι στήλες επιλεξιμότητας Α΄/Β΄/Γ΄ μπορούν να επικαλύπτονται' in l)

check('Greek terminology in summary', 'ενεργές εκκρεμείς εξαρτήσεις' in l and 'επιβεβαιωμένα κανονιστικά κενά' in l)
check('Greek terminology in matrix', 'Α΄ επιλεξιμότητα' in l and 'Χαμηλότερη ανάθεση' in l)
check('Greek readiness label', 'Έτοιμο για πίνακα επιλεξιμότητας' in l)
check('English UI terminology removed', all(term not in l for term in ['School profile','assignment units','Assignment units','eligibility','Fallback','unresolved dependencies','regulatory gaps','simulator / test harness','backend','roster']))

text=PAGE.read_text(encoding='utf-8')
check('frontend uses common profile builder', 'schoolProfileBuildDayGymnasium2026' in text and 'schoolProfileBuildDayGel2026' in text)
check('frontend uses workload matrix', 'schoolProfileWorkloadMatrix' in text)
check('frontend does not use personnel auto allocation', 'personnelWorkloadRosterPlan' not in text and 'personnelWorkloadEvaluatePerson' not in text)

failed=[n for n,ok in checks if not ok]
for n,ok in checks: print(('PASS' if ok else 'FAIL')+': '+n)
print('RESULT %d PASS / %d FAIL' % (len(checks)-len(failed),len(failed)))
raise SystemExit(1 if failed else 0)
