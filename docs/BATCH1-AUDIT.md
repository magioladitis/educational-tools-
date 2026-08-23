# Batch 1 audit — 5 / 22 files

| File | Original local `<style>` | Original inline `style=` | Candidate local CSS | Mobile overflow 390px |
|---|---:|---:|---:|---:|
| `ypologismos-morion-apospasis-evropaika-scholeia.php` | 5,077 CSS chars | 13 | 0 | 0 px |
| `ypologismos-morion-apospasis-exoteriko.php` | 6,058 CSS chars | 8 | 0 | 0 px |
| `ypologismos-morion-apospasis-psifiako-frontistirio.php` | 4,851 CSS chars | 3 | 0 | 0 px |
| `ypologismos-morion-apospasis.php` | 7,195 CSS chars | 2 | 0 | 0 px |
| `ypologismos-morion-apospasis-dimos.php` | 5,750 CSS chars | 1 | 0 | 0 px |

**Batch total:** 28,931 characters of local CSS migrated and 27 inline style attributes removed.

## DΗΜ.Ω.Σ. mobile finding

The original DΗΜ.Ω.Σ. detachment page already produced horizontal overflow on a 390 px viewport because the single-column grid retained the table's min-content width. The candidate adds a page-scoped containment fix (`min-width:0` and `minmax(0,1fr)` on mobile), reducing document-level horizontal overflow to 0 px while allowing the card/table content to remain usable.

## Migration policy

For this batch the priority is **safe centralization first**. Each page's former local CSS has been moved into `common.css` under a unique `body` scope. This prevents global leakage and gives every production page 0 local CSS. After all 22 files are available, a second pass will merge truly identical rules into shared calculator families and remove duplicate declarations across the 22 pages.
