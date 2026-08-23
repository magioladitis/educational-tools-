# Batch 3 CSS audit

| File | Local `<style>` before | Inline `style=` before | After |
|---|---:|---:|---|
| `ypologismos-morion-apospasis-sde(1).php` | 4,875 CSS chars | 3 | 0 `<style>`, 0 inline `style=` |
| `ergaleia(3).php` | 7,678 CSS chars | 0 | 0 `<style>`, 0 inline `style=` |
| `asep-tools(1).php` | 2,762 CSS chars | 0 | 0 `<style>`, 0 inline `style=` |

Total local CSS removed from PHP: **15,315 characters**.
Total inline style attributes removed: **3**.

### Architecture
- `ypologismos-morion-apospasis-sde.php` now uses the existing shared `edu-calc-sde` family plus the scoped `edu-page-sde-apospasis` variant.
- `ergaleia.php` uses scoped `edu-tools-directory` rules.
- `asep-tools.php` uses scoped `edu-asep-tools-hub` rules.
- The hub rules remain scoped in this candidate. Cross-hub deduplication can be done safely in the final 22/22 pass.