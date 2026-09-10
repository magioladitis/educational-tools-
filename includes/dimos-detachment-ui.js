/*
 * Browser UI controller for ypologismos-morion-apospasis-dimos.php.
 * Coordinates DIMOS input normalization, language scoring, live totals, copy and reset.
 */
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
