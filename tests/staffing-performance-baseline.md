# Staffing simulator performance baseline

Comparison: `3.20.92` (inline controller) → `3.20.93` (external controller).
Environment: CLI PHP/Node in the project test container. Numbers are intended as regression reference, not production latency guarantees.

| Scenario | 3.20.92 median PHP wall time | 3.20.93 median PHP wall time | 3.20.92 RSS | 3.20.93 RSS | HTML bytes before → after |
| --- | ---: | ---: | ---: | ---: | ---: |
| Default GET | 0.02398 s | 0.02405 s | 15,596 KB | 15,472 KB | 203,844 → 43,079 |
| Evening GEL profile POST | 0.03262 s | 0.03151 s | 16,108 KB | 15,984 KB | 375,070 → 214,305 |
| 150-person roster POST | 0.05332 s | 0.05500 s | 16,748 KB | 16,624 KB | 2,109,369 → 1,948,604 |

PHP internal peak memory on the profile POST remained exactly 6 MB in both versions.

Default compressed transfer estimate (gzip -9):
- 3.20.92 HTML: 44,027 B.
- 3.20.93 HTML: 8,878 B; external controller: 35,879 B.
- First uncached total: 44,757 B (+730 B / ~1.7%). Once the controller is cached, the HTML transfer is ~80% smaller.

JS parser-only benchmark (`vm.Script`, 100 runs): median 0.016 ms and ~1 KB heap delta both before and after.

Structural performance guard in `staffing-js-refactor-performance-contract.py` locks: 62 listeners, 165 `querySelector*` calls, zero `setInterval`, seven `setTimeout`, one runtime-config parse, controller <165 KB raw / <37 KB gzip, and default HTML <60 KB.

## 3.20.95 — stat4_8 EAE (.50) / allocation alignment fix

Benchmark profile: Gymnasium, 20 general-education personnel rows, same POST payload in both versions, 24 CLI runs (first 4 discarded as warm-up noise).

| Metric | 3.20.93 | 3.20.95 |
|---|---:|---:|
| Median PHP wall time | 0.05174 s | 0.05149 s |
| Mean PHP wall time | 0.05312 s | 0.05224 s |
| Rendered HTML | 940,615 B | 941,245 B |
| Median process RSS | 17,004 KB | 17,008 KB |

The +630 B HTML delta is the new EAE summary/status markup. `.50` specialty options are injected only for affected imported/selected personnel rows, not into every specialty `<select>`, so large rosters do not pay an O(rows × EAE-options) DOM/payload penalty.
