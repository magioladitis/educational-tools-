# Personnel workload performance baseline — v3.22.12

Date: 2026-09-26. Environment: project CLI/container (`php`, Node/V8). These numbers are **regression references**, not production latency guarantees.

## What is measured

The benchmark uses the same canonical `slots`, `people`, `person_state` and `slot_state` payload for PHP and JavaScript. Synthetic workloads are deliberately split into independent components so the benchmark measures predictable scaling of the exact component BnB/symmetry optimizer rather than random worst-case search noise.

| Scenario | People | Slots | Allocation rows | Search nodes |
| --- | ---: | ---: | ---: | ---: |
| small | 2 | 4 | 4 | 21 |
| typical | 8 | 16 | 16 | 84 |
| large | 20 | 40 | 40 | 210 |

## Baseline results

| Scenario | PHP optimizer median | Node/V8 optimizer median | Node/V8 validation median |
| --- | ---: | ---: | ---: |
| small | 0.088 ms | 0.211 ms | 0.014 ms |
| typical | 0.519 ms | 1.052 ms | 0.041 ms |
| large | 2.163 ms | 3.229 ms | 0.107 ms |

Default initial PHP GET render of `ypologismos-didaktikon-anagkon.php`: **24.42 ms** median and **65,190 bytes** generated HTML in this container.

### Interpretation

The JavaScript optimizer is **not intrinsically faster than the PHP core** on these fixtures; PHP is slightly faster in pure compute time. The performance gain from Phase 3A/3B is instead interaction latency: validation/optimization now completes in roughly 0.04–3.3 ms locally in V8 without an HTTP request, PHP bootstrap, server-side recomputation and full-page HTML render. Even the default PHP GET render alone is ~25 ms locally, before real network latency is added.

Therefore the benchmark should be used to protect two properties separately:

1. **Algorithmic parity/scaling:** PHP and JS must keep the same lexicographic objective, certification and search-node behavior for these deterministic fixtures.
2. **Interactive budget:** browser-side validation/optimizer must remain comfortably below the deliberately generous release ceilings, so a future regression cannot turn a millisecond interaction into a perceptible pause.

## Release contract

Run:

```bash
python3 tests/personnel-workload-performance-benchmark.py --contract
```

The release contract uses intentionally generous thresholds (small 25 ms, typical 75 ms, large 200 ms for optimizer; lower validation ceilings) plus a relative large/typical scaling guard. These are **catastrophic-regression guards**, not benchmark targets.

For a detailed report:

```bash
python3 tests/personnel-workload-performance-benchmark.py
```

If a working headless Chromium is available, the detailed mode also measures real browser payload hydration, validation, optimizer execution and representative allocation-row DOM materialization. The current container's Chromium binary cannot complete headless startup because of its DBus/runtime environment, so those optional columns were intentionally left unreported rather than fabricated.
