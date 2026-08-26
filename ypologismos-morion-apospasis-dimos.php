<?php require_once __DIR__ . '/includes/config.php'; ?>
<!doctype html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Υπολογισμός Μορίων ΔΗΜ.Ω.Σ. 2026-2027</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars(edu_asset_url('assets/common.css'), ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body class="edu-ui edu-page-dimos-detachment">
<?php require_once __DIR__ . '/includes/header.php'; ?>
<?php require_once __DIR__ . '/includes/components/calculator-layout.php'; ?>
  <main class="dimos-calc" id="dimosCalc">
    <?php calculatorHeroStart(); ?>
      <h1>Υπολογισμός Μορίων ΔΗΜ.Ω.Σ. 2026–2027</h1>
      <p>Ενημερωτικός υπολογιστής μοριοδότησης για απόσπαση μόνιμων εκπαιδευτικών σε Δημόσια Ωνάσεια Σχολεία.</p>
      <p><strong>Μέγιστο σύνολο: 53 μόρια</strong> · Α: 29 · Β: 13 · Γ: 11</p>
    <?php calculatorHeroEnd(); ?>

    <?php calculatorColumnsStart(array('class' => 'grid')); ?>
      <?php calculatorMainStart(); ?>
        <?php calculatorCardStart(); ?>
          <h2>Α. Επιστημονική, παιδαγωγική συγκρότηση και κατάρτιση</h2>
          <p class="cap">Μέγιστο κατηγορίας: 29 μόρια</p>

          <div class="note">
            Δεν μοριοδοτείται προσόν που αποτέλεσε προϋπόθεση διορισμού/μετάταξης/κατάταξης. Για αλλοδαπούς τίτλους απαιτείται η προβλεπόμενη αναγνώριση.
          </div>

          <h3>Α1. Τίτλοι σπουδών <span class="pill" id="titlesSubtotal">0,00 / 19</span></h3>
          <div class="checkrow">
            <input type="checkbox" id="phdRelated" data-title-points="8">
            <label for="phdRelated">Διδακτορικό συναφές <small>8 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondPhd" data-title-points="5">
            <label for="secondPhd">Δεύτερο διδακτορικό <small>5 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="masterRelated" data-title-points="4">
            <label for="masterRelated">Μεταπτυχιακό συναφές <small>4 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="masterOtherOrSecond" data-title-points="2">
            <label for="masterOtherOrSecond">Μεταπτυχιακό μη συναφές ή δεύτερο μεταπτυχιακό <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondDegree4y" data-title-points="3">
            <label for="secondDegree4y">Δεύτερο πτυχίο ΠΕ/ΤΕ τετραετούς φοίτησης <small>3 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="secondDegreeShort" data-title-points="2">
            <label for="secondDegreeShort">Δεύτερο πτυχίο ΤΕ διάρκειας μικρότερης των 4 ετών <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="esdda" data-title-points="2">
            <label for="esdda">Αποφοίτηση από Ε.Σ.Δ.Δ.Α. <small>2 μόρια</small></label>
          </div>
          <div class="checkrow">
            <input type="checkbox" id="thirdDegree" data-title-points="1">
            <label for="thirdDegree">Τρίτο πτυχίο <small>1 μόριο</small></label>
          </div>

          <h3>Α2. Επιμορφώσεις <span class="pill" id="trainingSubtotal">0,00 / 5</span></h3>
          <div class="checkrow">
            <input type="checkbox" id="selme" data-training-points="1">
            <label for="selme">Σ.Ε.Λ.Μ.Ε. / Α.Σ.ΠΑΙ.Τ.Ε. / Σ.Ε.Λ.Ε.Τ.Ε. <small>1 μόριο, εφόσον δεν αποτέλεσε προσόν διορισμού</small></label>
          </div>
          <div class="field">
            <label for="aeiPrograms">Προγράμματα Α.Ε.Ι. ≥300 ωρών ή ≥9 μηνών <small>1 μόριο ανά πρόγραμμα, έως 2</small></label>
            <input type="number" id="aeiPrograms" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="pekHours">Π.Ε.Κ. ώρες επιμόρφωσης <small>0,1 ανά πλήρες 10ωρο, έως 1. Η εισαγωγική επιμόρφωση νεοδιόριστων δεν μοριοδοτείται.</small></label>
            <input type="number" id="pekHours" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="iepHours">Ι.Ε.Π. / Π.Ι. / φορείς ΥΠΑΙΘΑ ώρες <small>0,1 ανά πλήρες 10ωρο, έως 1</small></label>
            <input type="number" id="iepHours" min="0" step="1" value="0">
          </div>
          <div class="checkrow">
            <input type="checkbox" id="meizon" data-training-points="1">
            <label for="meizon">Μείζον Πρόγραμμα Επιμόρφωσης Εκπαιδευτικών <small>1 μόριο</small></label>
          </div>
          <div class="two-col">
            <div class="field">
              <label for="eapAnnual">Ε.Α.Π. ετήσιες θεματικές ενότητες <small>0,80 ανά ενότητα, υποκριτήριο έως 0,80</small></label>
              <input type="number" id="eapAnnual" min="0" step="1" value="0">
            </div>
            <div class="field">
              <label for="eapSemester">Ε.Α.Π. εξαμηνιαίες θεματικές ενότητες <small>0,40 ανά ενότητα, υποκριτήριο έως 0,80</small></label>
              <input type="number" id="eapSemester" min="0" step="1" value="0">
            </div>
          </div>
          <div class="field">
            <label for="ekddaHours">Ε.Κ.Δ.Δ.Α. ώρες επιμόρφωσης <small>0,1 ανά πλήρες 10ωρο, έως 1</small></label>
            <input type="number" id="ekddaHours" min="0" step="1" value="0">
          </div>

          <h3>Α3. Ξένες γλώσσες <span class="pill" id="languageSubtotal">0,00 / 5</span></h3>
          <div class="note">
            Για την ίδια γλώσσα λαμβάνεται μόνο το ανώτερο επίπεδο. Για ΠΕ05, ΠΕ06, ΠΕ07, ΠΕ34 και ΠΕ40 η γλώσσα που αποτέλεσε προσόν διορισμού δεν μοριοδοτείται.
          </div>
          <div class="field">
            <label for="appointmentLanguage">Γλώσσα που αποτέλεσε προσόν διορισμού <small>Συμπλήρωσέ το μόνο αν είσαι ΠΕ05, ΠΕ06, ΠΕ07, ΠΕ34 ή ΠΕ40.</small></label>
            <select id="appointmentLanguage"><option value="">— Δεν αφορά —</option><option value="fr">ΠΕ05 — Γαλλική</option><option value="en">ΠΕ06 — Αγγλική</option><option value="de">ΠΕ07 — Γερμανική</option><option value="it">ΠΕ34 — Ιταλική</option><option value="es">ΠΕ40 — Ισπανική</option></select>
          </div>
          <div class="two-col">
            <div class="field"><label for="language1">1η ξένη γλώσσα</label><select id="language1"><option value="">— Επιλογή γλώσσας —</option><option value="en">Αγγλική</option><option value="fr">Γαλλική</option><option value="de">Γερμανική</option><option value="it">Ιταλική</option><option value="es">Ισπανική</option><option value="other">Άλλη ξένη γλώσσα</option></select></div>
            <div class="field"><label for="languageLevel1">Επίπεδο 1ης γλώσσας</label><select id="languageLevel1"><option value="0">Καμία / χωρίς μόρια</option><option value="1">Β2 — 1 μόριο</option><option value="2">Γ1 — 2 μόρια</option><option value="3">Γ2 — 3 μόρια</option></select></div>
            <div class="field hidden" id="languageOther1Wrap"><label for="languageOther1">Ονομασία άλλης 1ης γλώσσας</label><input id="languageOther1" type="text" placeholder="π.χ. Πορτογαλική"></div>
            <div class="field"><label for="language2">2η ξένη γλώσσα</label><select id="language2"><option value="">— Επιλογή γλώσσας —</option><option value="en">Αγγλική</option><option value="fr">Γαλλική</option><option value="de">Γερμανική</option><option value="it">Ιταλική</option><option value="es">Ισπανική</option><option value="other">Άλλη ξένη γλώσσα</option></select></div>
            <div class="field"><label for="languageLevel2">Επίπεδο 2ης γλώσσας</label><select id="languageLevel2"><option value="0">Καμία / χωρίς μόρια</option><option value="1">Β2 — 1 μόριο</option><option value="2">Γ1 — 2 μόρια</option><option value="3">Γ2 — 3 μόρια</option></select></div>
            <div class="field hidden" id="languageOther2Wrap"><label for="languageOther2">Ονομασία άλλης 2ης γλώσσας</label><input id="languageOther2" type="text" placeholder="π.χ. Πορτογαλική"></div>
          </div>
          <div id="languageWarning" class="note hidden"></div>

          <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Α', 'value_id' => 'categoryA', 'value_html' => '0,00 / 29')); ?>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <h2>Β. Επιστημονικό – συγγραφικό έργο</h2>
          <p class="cap">Μέγιστο κατηγορίας: 13 μόρια</p>

          <h3>Β1. Ερευνητικά προγράμματα και διακρίσεις <span class="pill" id="researchSubtotal">0,00 / 4</span></h3>
          <div class="two-col">
            <div class="field">
              <label for="researchPrograms">Συμμετοχές σε ερευνητικά προγράμματα / ομάδες έργου <small>1 μόριο ανά συμμετοχή</small></label>
              <input type="number" id="researchPrograms" min="0" step="1" value="0">
            </div>
            <div class="field">
              <label for="awards">Συναφείς διακρίσεις <small>1 μόριο ανά διάκριση</small></label>
              <input type="number" id="awards" min="0" step="1" value="0">
            </div>
          </div>

          <h3>Β2. Συγγραφικό και ερευνητικό έργο <span class="pill" id="writingSubtotal">0,00 / 5</span></h3>
          <div class="oknote">Στην ομαδική συγγραφή λαμβάνεται το ήμισυ της μοριοδότησης. Βάλε χωριστά ατομικά και ομαδικά έργα.</div>
          <table>
            <thead>
              <tr>
                <th>Είδος έργου</th>
                <th>Μόρια</th>
                <th>Ατομικά</th>
                <th>Ομαδικά</th>
              </tr>
            </thead>
            <tbody id="writingRows"></tbody>
          </table>

          <h3>Β3. Άρθρα σε επιστημονικά περιοδικά <span class="pill" id="articlesSubtotal">0,00 / 4</span></h3>
          <table>
            <thead>
              <tr>
                <th>Είδος άρθρου</th>
                <th>Μόρια</th>
                <th>Ατομικά</th>
                <th>Ομαδικά</th>
              </tr>
            </thead>
            <tbody id="articleRows"></tbody>
          </table>

          <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Β', 'value_id' => 'categoryB', 'value_html' => '0,00 / 13')); ?>
        <?php calculatorCardEnd(); ?>

        <?php calculatorCardStart(); ?>
          <h2>Γ. Καινοτόμο εκπαιδευτικό έργο και συμβολή στην ανάπτυξη του σχολείου</h2>
          <p class="cap">Μέγιστο κατηγορίας: 11 μόρια</p>

          <div class="field">
            <label for="selfAssessmentActions">Δράσεις κοινού ενδιαφέροντος στο πλαίσιο αυτοαξιολόγησης σχολικής μονάδας <small>0,50 ανά δράση, έως 2</small></label>
            <input type="number" id="selfAssessmentActions" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="innovativePublished">Καινοτόμα διδακτική πρακτική / έρευνα δράσης / ενδοσχολική επιμόρφωση με δημοσίευση σε περιοδικό ή πρακτικά με κριτές <small>1 ανά δράση, έως 2</small></label>
            <input type="number" id="innovativePublished" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="innovativePrograms">Καινοτόμα εκπαιδευτικά προγράμματα ή δράσεις <small>π.χ. Erasmus+, eTwinning, MUN, περιβαλλοντικά, πολιτιστικά, αγωγής υγείας · 0,50 ανά πρόγραμμα/δράση, έως 4</small></label>
            <input type="number" id="innovativePrograms" min="0" step="1" value="0">
          </div>
          <div class="field">
            <label for="clubs">Όμιλοι, Σύνολα ή εξωδιδακτικές δραστηριότητες <small>0,50 ανά δράση, έως 3</small></label>
            <input type="number" id="clubs" min="0" step="1" value="0">
          </div>

          <?php calculatorSubtotalRow(array('label_html' => 'Σύνολο Γ', 'value_id' => 'categoryC', 'value_html' => '0,00 / 11')); ?>
        <?php calculatorCardEnd(); ?>
      <?php calculatorMainEnd(); ?>

      <?php calculatorResultsStart(array('attrs' => array('aria-live' => 'polite'))); ?>
        <?php calculatorScoreHeader(array(
          'variant' => 'capped',
          'class' => 'total',
          'value_id' => 'grandTotal',
          'value_html' => '0,00',
          'value_class' => 'num',
          'cap_html' => 'από 53 μόρια',
          'cap_class' => 'outof'
        )); ?>
        <div class="bar"><div id="totalBar"></div></div>

        <?php calculatorResultRow(array('label_html' => 'Α. Συγκρότηση & κατάρτιση', 'value_html' => '0,00 / 29', 'value_id' => 'resA')); ?>
        <?php calculatorResultRow(array('label_html' => 'Β. Επιστημονικό έργο', 'value_html' => '0,00 / 13', 'value_id' => 'resB')); ?>
        <?php calculatorResultRow(array('label_html' => 'Γ. Καινοτόμο έργο', 'value_html' => '0,00 / 11', 'value_id' => 'resC')); ?>

        <?php calculatorActions(array(array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'copyBtn'), 'html' => 'Αντιγραφή'), array('attrs' => array('type' => 'button', 'class' => 'secondary', 'id' => 'resetBtn'), 'html' => 'Μηδενισμός'))); ?>

        <?php calculatorResultMessage(array('variant' => 'disclaimer', 'text' => 'Το εργαλείο είναι βοηθητικό. Η τελική αποτίμηση γίνεται από τα αρμόδια όργανα και προϋποθέτει τα σωστά δικαιολογητικά στα αντίστοιχα πεδία.')); ?>
      <?php calculatorResultsEnd(); ?>
    <?php calculatorColumnsEnd(); ?>
</main>

  <script src="<?php echo htmlspecialchars(edu_asset_url('includes/language-calculations.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script>
    (function(){
      const MAX = {
        titles: 19,
        training: 5,
        language: 5,
        A: 29,
        research: 4,
        writing: 5,
        articles: 4,
        B: 13,
        C: 11,
        total: 53
      };

      const writingItems = [
        { id:"intlBooks", label:"Βιβλία διεθνών εκδοτικών οίκων με ISBN", pts:2 },
        { id:"greekBooks", label:"Βιβλία ελληνικών εκδοτικών οίκων με ISBN", pts:1.5 },
        { id:"intlChapters", label:"Κεφάλαια σε συλλογικούς τόμους διεθνών εκδοτικών οίκων με ISBN", pts:1 },
        { id:"greekChapters", label:"Κεφάλαια σε συλλογικούς τόμους ελληνικών εκδοτικών οίκων με ISBN", pts:0.5 },
        { id:"intlProceedings", label:"Εισηγήσεις σε πρακτικά διεθνών συνεδρίων με ISBN/ISSN", pts:0.5 },
        { id:"greekProceedings", label:"Εισηγήσεις σε πρακτικά ελληνικών συνεδρίων με ISBN/ISSN", pts:0.25 },
        { id:"schoolBook", label:"Σχολικό εγχειρίδιο / διδακτικό βιβλίο / ΑΠΣ-ΔΕΠΠΣ / προγράμματα σπουδών", pts:0.5 },
        { id:"software", label:"Εκπαιδευτικό λογισμικό πιστοποιημένο ή με σφραγίδα ποιότητας", pts:0.25 },
        { id:"trainingMaterial", label:"Επιμορφωτικό υλικό ΥΠΑΙΘΑ / ΙΕΠ / ΠΙ / εποπτευόμενων φορέων", pts:0.25 }
      ];

      const articleItems = [
        { id:"intlArticles", label:"Άρθρα σε διεθνή επιστημονικά περιοδικά με ISSN και κριτές", pts:1 },
        { id:"greekArticles", label:"Άρθρα σε ελληνικά επιστημονικά περιοδικά με ISSN και κριτές", pts:0.5 }
      ];

      const $ = (id) => document.getElementById(id);
      const qsa = (sel) => Array.from(document.querySelectorAll(sel));
      const n = (id) => Math.max(0, Number($(id)?.value || 0));
      const cap = (value, max) => Math.min(value, max);
      const f = (value) => (Math.round((value + Number.EPSILON) * 100) / 100).toLocaleString('el-GR', {minimumFractionDigits:2, maximumFractionDigits:2});
      const fullTenHours = (hours) => Math.floor(Math.max(0, hours) / 10) * 0.1;

      function makeRows(targetId, items){
        const tbody = $(targetId);
        tbody.innerHTML = items.map(item => `
          <tr>
            <td>${item.label}</td>
            <td>${String(item.pts).replace(".", ",")}</td>
            <td class="numcell"><input type="number" min="0" step="1" value="0" id="${item.id}Solo" aria-label="${item.label} ατομικά"></td>
            <td class="numcell"><input type="number" min="0" step="1" value="0" id="${item.id}Group" aria-label="${item.label} ομαδικά"></td>
          </tr>
        `).join("");
      }

      makeRows("writingRows", writingItems);
      makeRows("articleRows", articleItems);

      function scoreItems(items){
        return items.reduce((sum, item) => {
          const solo = n(item.id + "Solo");
          const group = n(item.id + "Group");
          return sum + item.pts * solo + item.pts * 0.5 * group;
        }, 0);
      }

      function normalizeIntegerFields(){
        document.querySelectorAll('input[type="number"][step="1"]').forEach(el => {
          if (el.value === '') return;
          let v = Number(el.value);
          if (!Number.isFinite(v)) v = 0;
          v = Math.floor(v);
          const min = el.getAttribute('min');
          const max = el.getAttribute('max');
          if (min !== null && min !== '') v = Math.max(Number(min), v);
          if (max !== null && max !== '') v = Math.min(Number(max), v);
          el.value = String(v);
          if (!el.hasAttribute('inputmode')) el.setAttribute('inputmode','numeric');
        });
      }

      function calc(){
        const titlesRaw = qsa("[data-title-points]").reduce((sum, el) => sum + (el.checked ? Number(el.dataset.titlePoints) : 0), 0);
        const titles = cap(titlesRaw, MAX.titles);

        const trainingRaw =
          ( $("selme").checked ? 1 : 0 ) +
          cap(n("aeiPrograms"), 2) +
          cap(fullTenHours(n("pekHours")), 1) +
          cap(fullTenHours(n("iepHours")), 1) +
          ( $("meizon").checked ? 1 : 0 ) +
          cap(n("eapAnnual") * 0.8 + n("eapSemester") * 0.4, 0.8) +
          cap(fullTenHours(n("ekddaHours")), 1);
        const training = cap(trainingRaw, MAX.training);

        syncLanguageUI();
        const languageDetails = EducationLanguages.calculatePair([
          {language:$("language1").value, otherText:$("languageOther1").value, points:Number($("languageLevel1").value||0)},
          {language:$("language2").value, otherText:$("languageOther2").value, points:Number($("languageLevel2").value||0)}
        ], {excluded:$("appointmentLanguage").value ? [$("appointmentLanguage").value] : [], cap:MAX.language});
        const language = languageDetails.points;
        const A = cap(titles + training + language, MAX.A);

        const research = cap(n("researchPrograms") + n("awards"), MAX.research);
        const writing = cap(scoreItems(writingItems), MAX.writing);
        const articles = cap(scoreItems(articleItems), MAX.articles);
        const B = cap(research + writing + articles, MAX.B);

        const C = cap(
          cap(n("selfAssessmentActions") * 0.5, 2) +
          cap(n("innovativePublished") * 1, 2) +
          cap(n("innovativePrograms") * 0.5, 4) +
          cap(n("clubs") * 0.5, 3),
          MAX.C
        );

        const total = cap(A + B + C, MAX.total);

        const values = {titles, training, language, languageDetails, A, research, writing, articles, B, C, total};
        render(values);
        return values;
      }

      function syncLanguageUI(){
        const s1=$("language1"),s2=$("language2"),excluded=$("appointmentLanguage").value;
        $("languageOther1Wrap").classList.toggle("hidden",s1.value!=="other"); $("languageOther2Wrap").classList.toggle("hidden",s2.value!=="other");
        [s1,s2].forEach(s=>Array.from(s.options).forEach(o=>o.disabled=false));
        [s1,s2].forEach(s=>{if(excluded){const o=Array.from(s.options).find(x=>x.value===excluded);if(o)o.disabled=true;}});
        if(s1.value&&s1.value!=="other"){const o=Array.from(s2.options).find(x=>x.value===s1.value);if(o)o.disabled=true;}
        if(s2.value&&s2.value!=="other"){const o=Array.from(s1.options).find(x=>x.value===s2.value);if(o)o.disabled=true;}
      }

      function render(v){
        $("titlesSubtotal").textContent = `${f(v.titles)} / 19`;
        $("trainingSubtotal").textContent = `${f(v.training)} / 5`;
        $("languageSubtotal").textContent = `${f(v.language)} / 5`;
        const lw=$("languageWarning"); lw.textContent=v.languageDetails.warnings.join(" "); lw.classList.toggle("hidden",v.languageDetails.warnings.length===0);
        $("categoryA").textContent = `${f(v.A)} / 29`;

        $("researchSubtotal").textContent = `${f(v.research)} / 4`;
        $("writingSubtotal").textContent = `${f(v.writing)} / 5`;
        $("articlesSubtotal").textContent = `${f(v.articles)} / 4`;
        $("categoryB").textContent = `${f(v.B)} / 13`;

        $("categoryC").textContent = `${f(v.C)} / 11`;

        $("grandTotal").textContent = f(v.total);
        $("resA").textContent = `${f(v.A)} / 29`;
        $("resB").textContent = `${f(v.B)} / 13`;
        $("resC").textContent = `${f(v.C)} / 11`;
        $("totalBar").style.width = `${Math.min(100, (v.total / MAX.total) * 100)}%`;
      }

      function summaryText(v){
        return [
          "Υπολογισμός μορίων ΔΗΜ.Ω.Σ. 2026–2027",
          `Σύνολο: ${f(v.total)} / 53`,
          `Α. Επιστημονική, παιδαγωγική συγκρότηση και κατάρτιση: ${f(v.A)} / 29`,
          `  - Τίτλοι σπουδών: ${f(v.titles)} / 19`,
          `  - Επιμορφώσεις: ${f(v.training)} / 5`,
          `  - Ξένες γλώσσες: ${f(v.language)} / 5`,
          `Β. Επιστημονικό – συγγραφικό έργο: ${f(v.B)} / 13`,
          `  - Ερευνητικά προγράμματα και διακρίσεις: ${f(v.research)} / 4`,
          `  - Συγγραφικό έργο: ${f(v.writing)} / 5`,
          `  - Άρθρα: ${f(v.articles)} / 4`,
          `Γ. Καινοτόμο εκπαιδευτικό έργο: ${f(v.C)} / 11`,
          "",
          "Σημείωση: Ενημερωτικός υπολογισμός. Η τελική αποτίμηση γίνεται από τα αρμόδια όργανα."
        ].join("\n");
      }

      document.addEventListener("input", calc);
      document.addEventListener("change", () => { normalizeIntegerFields(); calc(); });

      $("resetBtn").addEventListener("click", () => {
        qsa("input[type='number']").forEach(i => i.value = 0);
        qsa("input[type='text']").forEach(i => i.value = "");
        qsa("input[type='checkbox']").forEach(i => i.checked = false);
        qsa("select").forEach(s => s.selectedIndex = 0);
        calc();
      });

      $("copyBtn").addEventListener("click", async () => {
        const text = summaryText(calc());
        try{
          await navigator.clipboard.writeText(text);
          $("copyBtn").textContent = "Αντιγράφηκε ✓";
          setTimeout(() => $("copyBtn").textContent = "Αντιγραφή", 1400);
        }catch(e){
          alert(text);
        }
      });

      calc();
    })();
  </script>

<?php sourceCardStart(); ?>
  <p><strong>28/ΔΕΔΗΜΩΣ/26-06-2026</strong>, «Πρόσκληση εκδήλωσης ενδιαφέροντος για πλήρωση λειτουργικών κενών στα Δημόσια Ωνάσεια Σχολεία με απόσπαση μόνιμων εκπαιδευτικών Δ.Ε. διάρκειας ενός διδακτικού έτους, 2026-2027» (ΑΔΑ: Ρ0ΦΛ46ΝΚΠΔ-Σ02). Η διαδικασία παραπέμπει επίσης στις Υ.Α. 81473/Δ6/03-07-2025 (Β΄ 3528) και 169485/Δ6/30-12-2025 (Β΄ 7259), όπως ισχύουν.</p>
  <?php sourceCardLinksStart(); ?><?php sourceCardLink('https://www.minedu.gov.gr/news/65393-29-06-26-prosklisi-apospaseon-sta-dimos', 'Επίσημη πρόσκληση — ΥΠΑΙΘΑ ↗'); ?><?php sourceCardLink('https://apps.espa.minedu.gov.gr/apospaseisdimos/', 'Επίσημη πλατφόρμα αιτήσεων ↗'); ?><?php sourceCardLinksEnd(); ?>
<?php sourceCardEnd(); ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
  <script src="<?php echo htmlspecialchars(edu_asset_url('assets/common.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
