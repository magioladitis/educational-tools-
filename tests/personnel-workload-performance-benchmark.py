#!/usr/bin/env python3
"""
Personnel workload performance benchmark / lightweight regression contract.

Default mode prints a reproducible Markdown-style report and, when Chromium is
available, measures the real browser JS engine plus representative DOM row
materialization.  --contract runs only the quick PHP/Node core checks intended
for the release gate; its thresholds are deliberately generous so it catches
catastrophic regressions rather than normal CI noise.
"""
from __future__ import annotations

import argparse
import html
import json
import os
from pathlib import Path
import re
import shutil
import statistics
import subprocess
import sys
import tempfile
import time

ROOT = Path(__file__).resolve().parents[1]
MODULE = ROOT / 'includes' / 'personnel-workload-calculations.js'
PAGE = ROOT / 'ypologismos-didaktikon-anagkon.php'

CODES = [
    'ΠΕ01','ΠΕ02','ΠΕ03','ΠΕ04.01','ΠΕ04.02','ΠΕ04.04','ΠΕ04.05','ΠΕ05','ΠΕ06','ΠΕ07',
    'ΠΕ08','ΠΕ11','ΠΕ33','ΠΕ34','ΠΕ78','ΠΕ79.01','ΠΕ79.02','ΠΕ80','ΠΕ81','ΠΕ82',
]
SIZES = [('small', 1), ('typical', 4), ('large', 10)]


def make_case(name: str, components: int) -> dict:
    """Create disconnected exact-search components with deterministic optima."""
    slots, people, person_state, slot_state, allocations = {}, [], {}, {}, []
    for i in range(components):
        a = CODES[(2 * i) % len(CODES)]
        b = CODES[(2 * i + 1) % len(CODES)]
        pa, pb = f'{name}_a{i}', f'{name}_b{i}'
        people.extend([
            {'person_id': pa, 'specialty_code': a, 'secondary_specialty_code': ''},
            {'person_id': pb, 'specialty_code': b, 'secondary_specialty_code': ''},
        ])
        person_state[pa] = {'remaining_hours': 8, 'b_assignment_hours': 0, 'b_remaining_hours': 10}
        person_state[pb] = {'remaining_hours': 6, 'b_assignment_hours': 0, 'b_remaining_hours': 10}
        for j in range(1, 5):
            sid = f'{name}_c{i}_s{j}'
            slots[sid] = {
                'slot_id': sid,
                'unit_id': sid,
                'grade': str(i + 1),
                'subject': f'Benchmark {i + 1}.{j}',
                'slot_label': f'Benchmark {i + 1}.{j}',
                'capacity_hours': 3,
                'eligible_by_priority': {'A': [a], 'B': [b], 'C': [], 'SPECIAL': []},
                'top_priority': 'A',
                'top_codes': [a],
            }
            slot_state[sid] = {'remaining_hours': 3, 'atomic_blocked': False}
            # Two A slots + two B slots per component: valid, atomic and within B<=10.
            pid = pa if j <= 2 else pb
            allocations.append({'person_id': pid, 'slot_id': sid, 'hours': 3})
    return {
        'name': name,
        'components': components,
        'slots': slots,
        'people': people,
        'person_state': person_state,
        'slot_state': slot_state,
        'allocations': allocations,
        'counts': {
            'slots': len(slots),
            'people': len(people),
            'allocation_rows': len(allocations),
        },
    }


def cases() -> list[dict]:
    return [make_case(name, components) for name, components in SIZES]


def median(values):
    return statistics.median(values) if values else None


def objective(rows: list[dict]) -> tuple[int, int, int, int]:
    covered = top = b = primary = 0
    for row in rows:
        h = int(row.get('hours', 0) or 0)
        p = row.get('priority', '')
        covered += h
        if p in ('A', 'SPECIAL'):
            top += h
        elif p == 'B':
            b += h
        if row.get('specialty_source') == 'primary':
            primary += h
    return covered, top, b, primary


def run_php_core(payload: str, iterations: int) -> list[dict]:
    code = r'''
require "includes/teaching-allocation-engine.php";
$cases=json_decode(stream_get_contents(STDIN),true);$out=[];
foreach($cases as $c){
  $last=null; for($i=0;$i<4;$i++) $last=teachingAllocationEngineSolveRemaining($c['slots'],$c['people'],$c['person_state'],$c['slot_state']);
  $samples=[];
  for($i=0;$i<%d;$i++){
    $t=hrtime(true);$last=teachingAllocationEngineSolveRemaining($c['slots'],$c['people'],$c['person_state'],$c['slot_state']);$samples[]=(hrtime(true)-$t)/1000000.0;
  }
  $out[]=['name'=>$c['name'],'samples_ms'=>$samples,'allocations'=>$last['allocations'],'summary'=>$last['summary']];
}
echo json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
''' % iterations
    p = subprocess.run(
        ['php', '-d', 'memory_limit=1024M', '-r', code],
        input=payload, text=True, capture_output=True, cwd=ROOT,
    )
    if p.returncode:
        raise RuntimeError('PHP benchmark failed: ' + p.stderr)
    return json.loads(p.stdout)


def run_node_core(payload: str, iterations: int) -> list[dict]:
    code = r'''
const fs=require('fs'),vm=require('vm');global.window=global;
vm.runInThisContext(fs.readFileSync(process.argv[1],'utf8'));
vm.runInThisContext(fs.readFileSync(process.argv[2],'utf8'));
const cs=JSON.parse(fs.readFileSync(0,'utf8')),W=global.PersonnelWorkloadCalculations,out=[];
for(const c of cs){
  let last;for(let i=0;i<5;i++)last=W.optimizeRemaining(c.slots,c.people,c.person_state,c.slot_state);
  const opt=[];for(let i=0;i<%d;i++){const t=process.hrtime.bigint();last=W.optimizeRemaining(c.slots,c.people,c.person_state,c.slot_state);opt.push(Number(process.hrtime.bigint()-t)/1e6);}
  const val=[];let vr;for(let i=0;i<%d;i++){const t=process.hrtime.bigint();vr=W.validateRosterSlotAllocations(c.slots,c.people,c.allocations);val.push(Number(process.hrtime.bigint()-t)/1e6);}
  out.push({name:c.name,optimizer_samples_ms:opt,validation_samples_ms:val,allocations:last.allocations,summary:last.summary,validation_summary:vr.summary});
}
process.stdout.write(JSON.stringify(out));
''' % (iterations, iterations)
    p = subprocess.run(
        ['node', '-e', code, str(ROOT / 'includes' / 'specialty-code-normalization.js'), str(MODULE)], input=payload, text=True,
        capture_output=True, cwd=ROOT,
    )
    if p.returncode:
        raise RuntimeError('Node benchmark failed: ' + p.stderr)
    return json.loads(p.stdout)


def run_page_render(iterations: int = 7) -> dict:
    samples, sizes = [], []
    env = os.environ.copy()
    env['REQUEST_METHOD'] = 'GET'
    for i in range(iterations + 2):
        t = time.perf_counter()
        p = subprocess.run(['php', str(PAGE)], capture_output=True, cwd=ROOT, env=env)
        dt = (time.perf_counter() - t) * 1000
        if p.returncode:
            raise RuntimeError('Initial page render failed: ' + p.stderr.decode('utf-8', 'replace'))
        if i >= 2:
            samples.append(dt)
            sizes.append(len(p.stdout))
    return {'median_ms': median(samples), 'samples_ms': samples, 'html_bytes': int(median(sizes))}


def browser_html(payload: str, iterations: int) -> str:
    # Real Chromium/V8 measurements. Rendering is deliberately representative of
    # allocationAppendOptimizerRow(): one row, slot select, person select, hours input.
    escaped_payload = payload.replace('</', '<\\/')
    return f'''<!doctype html><meta charset="utf-8"><title>Personnel workload browser benchmark</title>
<script src="../includes/specialty-code-normalization.js"></script>
<script src="../includes/personnel-workload-calculations.js"></script>
<div id="rows" style="position:absolute;left:-10000px;top:0;width:800px"></div><pre id="result">pending</pre>
<script>
(function(){{
'use strict';
const cases={escaped_payload},W=window.PersonnelWorkloadCalculations,ITER={iterations};
function med(a){{const b=a.slice().sort((x,y)=>x-y),m=Math.floor(b.length/2);return b.length%%2?b[m]:(b[m-1]+b[m])/2;}}
function sample(fn,n){{for(let i=0;i<5;i++)fn();const a=[];for(let i=0;i<n;i++){{const t=performance.now();fn();a.push(performance.now()-t);}}return med(a);}}
function renderRows(rows,c){{
 const host=document.getElementById('rows');host.textContent='';const frag=document.createDocumentFragment();
 rows.forEach(function(item){{
  const row=document.createElement('div');row.className='allocation-row';
  const s=document.createElement('select'),so=document.createElement('option');so.value=item.slot_id;so.textContent=(c.slots[item.slot_id]||{{}}).slot_label||item.slot_id;s.appendChild(so);
  const p=document.createElement('select'),po=document.createElement('option');po.value=item.person_id;po.textContent=item.person_id+' · '+(item.priority||'');p.appendChild(po);
  const h=document.createElement('input');h.type='number';h.value=String(item.hours||0);row.append(s,p,h);frag.appendChild(row);
 }});
 host.appendChild(frag);void host.offsetHeight;
}}
const out=[];
cases.forEach(function(c){{
 let optimized=W.optimizeRemaining(c.slots,c.people,c.person_state,c.slot_state);
 const hydration=sample(function(){{JSON.parse(JSON.stringify(c));}},ITER);
 const validation=sample(function(){{W.validateRosterSlotAllocations(c.slots,c.people,c.allocations);}},ITER);
 const optimizer=sample(function(){{optimized=W.optimizeRemaining(c.slots,c.people,c.person_state,c.slot_state);}},ITER);
 const rendering=sample(function(){{renderRows(optimized.allocations,c);}},ITER);
 out.push({{name:c.name,hydrate_ms:hydration,validation_ms:validation,optimizer_ms:optimizer,render_ms:rendering,rows:optimized.allocations.length}});
}});
document.getElementById('result').textContent=JSON.stringify(out);
}})();
</script>'''


def run_chromium(payload: str, iterations: int = 25) -> list[dict] | None:
    chromium = shutil.which('chromium') or shutil.which('chromium-browser') or shutil.which('google-chrome')
    if not chromium:
        return None
    bench_path = ROOT / 'tests' / '.personnel-workload-browser-benchmark.tmp.html'
    try:
        bench_path.write_text(browser_html(payload, iterations), encoding='utf-8')
        cmd = [
            chromium, '--headless', '--no-sandbox', '--disable-gpu',
            '--allow-file-access-from-files', '--virtual-time-budget=30000',
            '--dump-dom', bench_path.as_uri(),
        ]
        # Some minimal CI/container images ship Chromium without a working
        # headless/DBus environment. Use the system timeout command when
        # available and treat that situation as an optional-browser skip, not
        # as a benchmark failure. Core PHP/Node measurements still run.
        timeout_bin = shutil.which('timeout')
        run_cmd = ([timeout_bin, '12s'] + cmd) if timeout_bin else cmd
        try:
            p = subprocess.run(run_cmd, text=True, capture_output=True, cwd=ROOT, timeout=15)
        except subprocess.TimeoutExpired:
            return None
        if p.returncode:
            return None
        m = re.search(r'<pre id="result">(.*?)</pre>', p.stdout, re.S)
        if not m:
            raise RuntimeError('Chromium benchmark did not expose result payload')
        return json.loads(html.unescape(m.group(1)))
    finally:
        try:
            bench_path.unlink()
        except FileNotFoundError:
            pass


def check_contract(cs: list[dict], php: list[dict], node: list[dict]) -> tuple[bool, list[str]]:
    messages, ok = [], True
    pmap = {r['name']: r for r in php}
    nmap = {r['name']: r for r in node}
    # Generous wall-clock budgets: intended only to catch orders-of-magnitude regressions.
    budgets = {
        'small': {'opt': 25.0, 'val': 15.0},
        'typical': {'opt': 75.0, 'val': 30.0},
        'large': {'opt': 200.0, 'val': 60.0},
    }
    for c in cs:
        name = c['name']; pr = pmap[name]; nr = nmap[name]
        po, no = objective(pr['allocations']), objective(nr['allocations'])
        certified = bool(pr['summary'].get('maximum_coverage_certified')) and bool(nr['summary'].get('maximum_coverage_certified'))
        nopt = median(nr['optimizer_samples_ms']); nval = median(nr['validation_samples_ms'])
        nodes_p = int(pr['summary'].get('optimizer_search_nodes', 0)); nodes_n = int(nr['summary'].get('optimizer_search_nodes', 0))
        checks = [
            (po == no, f'{name}: PHP/JS objective parity'),
            (certified, f'{name}: both optimizers certified'),
            (nodes_p == nodes_n, f'{name}: identical search-node count'),
            (nopt < budgets[name]['opt'], f'{name}: JS optimizer median {nopt:.3f} ms < {budgets[name]["opt"]:.0f} ms'),
            (nval < budgets[name]['val'], f'{name}: JS validation median {nval:.3f} ms < {budgets[name]["val"]:.0f} ms'),
            (nr['validation_summary'].get('invalid_allocation_row_count') == 0, f'{name}: benchmark validation fixture remains valid'),
        ]
        for passed, label in checks:
            messages.append(('✔ ' if passed else '✘ ') + label)
            ok &= passed
    # Scaling guard is relative and therefore much less machine-sensitive.
    typ = median(nmap['typical']['optimizer_samples_ms']); large = median(nmap['large']['optimizer_samples_ms'])
    scaling_ok = large <= max(typ * 8.0, 10.0)
    messages.append(('✔ ' if scaling_ok else '✘ ') + f'large/typical optimizer scaling bounded ({large:.3f}/{typ:.3f} ms)')
    ok &= scaling_ok
    return ok, messages


def fmt(v):
    return '—' if v is None else f'{v:.3f}'


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--contract', action='store_true', help='quick release-gate mode')
    ap.add_argument('--json', action='store_true', help='emit machine-readable JSON in full mode')
    args = ap.parse_args()

    cs = cases()
    payload = json.dumps(cs, ensure_ascii=False, separators=(',', ':'))
    iterations = 8 if args.contract else 35
    php = run_php_core(payload, iterations)
    node = run_node_core(payload, iterations)

    ok, messages = check_contract(cs, php, node)
    if args.contract:
        print('Personnel workload performance contract')
        for m in messages:
            print('  ' + m)
        print('\nPerformance contract: ' + ('PASS' if ok else 'FAIL'))
        return 0 if ok else 1

    page = run_page_render()
    chrome = run_chromium(payload, 20)
    pmap = {r['name']: r for r in php}; nmap = {r['name']: r for r in node}
    cmap = {r['name']: r for r in chrome} if chrome else {}
    result = {
        'page_initial_get': page,
        'scenarios': [],
        'contract_pass': ok,
        'contract_messages': messages,
        'chromium_available': chrome is not None,
    }
    for c in cs:
        name = c['name']; pr, nr, cr = pmap[name], nmap[name], cmap.get(name, {})
        result['scenarios'].append({
            'name': name,
            **c['counts'],
            'php_optimizer_median_ms': median(pr['samples_ms']),
            'node_optimizer_median_ms': median(nr['optimizer_samples_ms']),
            'node_validation_median_ms': median(nr['validation_samples_ms']),
            'search_nodes': int(nr['summary'].get('optimizer_search_nodes', 0)),
            'browser_hydrate_median_ms': cr.get('hydrate_ms'),
            'browser_validation_median_ms': cr.get('validation_ms'),
            'browser_optimizer_median_ms': cr.get('optimizer_ms'),
            'browser_render_rows_median_ms': cr.get('render_ms'),
            'browser_rendered_rows': cr.get('rows'),
        })
    if args.json:
        print(json.dumps(result, ensure_ascii=False, indent=2))
        return 0 if ok else 1

    print('# Personnel workload performance benchmark')
    print('\nEnvironment-local numbers; they are regression references, not production latency guarantees.')
    print(f"\nInitial PHP GET render: **{page['median_ms']:.2f} ms** median, **{page['html_bytes']:,} B** HTML.")
    print('\n| Scenario | People | Slots | PHP optimizer | Node/V8 optimizer | Node validation | Chromium optimizer | Chromium validation | Chromium DOM rows |')
    print('|---|---:|---:|---:|---:|---:|---:|---:|---:|')
    for r in result['scenarios']:
        print('| {name} | {people} | {slots} | {php} ms | {node} ms | {val} ms | {copt} ms | {cval} ms | {rend} ms |'.format(
            name=r['name'], people=r['people'], slots=r['slots'],
            php=fmt(r['php_optimizer_median_ms']), node=fmt(r['node_optimizer_median_ms']), val=fmt(r['node_validation_median_ms']),
            copt=fmt(r['browser_optimizer_median_ms']), cval=fmt(r['browser_validation_median_ms']), rend=fmt(r['browser_render_rows_median_ms']),
        ))
    print('\nSearch nodes:', ', '.join(f"{r['name']}={r['search_nodes']}" for r in result['scenarios']))
    if chrome is None:
        print('\nChromium not available: browser/DOM columns were skipped.')
    print('\nContract:', 'PASS' if ok else 'FAIL')
    return 0 if ok else 1


if __name__ == '__main__':
    raise SystemExit(main())
