# Batch 2 CSS audit — v3.20.9-b2

| File | Local CSS chars before | Inline style before | After `<style>` | After inline |
|---|---:|---:|---:|---:|
| `ypologismos-morion-mitroo-sde.php` | 5286 | 0 | 0 | 0 |
| `ypologismos-morion-onaseia.php` | 6245 | 0 | 0 | 0 |
| `ypologismos-morion-diefthynton-ypodiefthynton-sde.php` | 4812 | 4 | 0 | 0 |
| `metatropi-klimakas.php` | 3937 | 0 | 0 | 0 |
| `ypologismos-morion-3ea-2025.php` | 4342 | 0 | 0 | 0 |
| `ypologismos-morion-4ea-2025.php` | 256 | 0 | 0 | 0 |
| `ypologismos-morion-1ea-2025.php` | 210 | 0 | 0 | 0 |
| `ypologismos-morion-1gt-2024.php` | 256 | 0 | 0 | 0 |
| `ypologismos-morion-2ea-2025.php` | 256 | 0 | 0 | 0 |

**Total local CSS removed from PHP:** 25,600 characters.
**Total inline style attributes removed:** 4.

The nine inline JavaScript blocks were compared against the uploaded originals and remained byte-for-byte identical. Only presentation hooks/body classes/cache-busting references changed.

The two SDE pages now share one scoped `edu-calc-sde` family in `common.css`; registry/leadership-only components remain separately scoped.

The four mature ASEP pages (1EA, 2EA, 4EA, 1GT) now keep their width/sidebar/warning differences in page body classes instead of local style blocks.
