#!/usr/bin/env python3
from pathlib import Path
import sys
ROOT=Path(__file__).resolve().parents[1]
PAGE=(ROOT/'ypologismos-didaktikon-anagkon.php').read_text(encoding='utf-8')
UI=(ROOT/'includes/staffing-simulator-ui.js').read_text(encoding='utf-8')
FAIL=[]
def check(name,cond):
    print(('  ✔ ' if cond else '  ✘ ')+name)
    if not cond: FAIL.append(name)
check('auto proposal keeps submit fallback', 'value="allocation_auto" data-staffing-request-action="allocation_auto" data-staffing-client-action="allocation_auto"' in PAGE)
check('allocation check keeps submit fallback', 'value="allocation" data-staffing-request-action="allocation" data-staffing-client-action="allocation"' in PAGE)
gate=UI[UI.index('function installExplicitRequestGate'):UI.index("['staffingProfileForm'")]
check('request gate invokes client handler before compacting fields', gate.find('handleStaffingClientAction')>=0 and gate.find('handleStaffingClientAction') < gate.find('compactRepeatedFormState'))
check('handled client action prevents default POST', "if(clientAction!=='' && typeof handleStaffingClientAction==='function' && handleStaffingClientAction(clientAction,button)){" in gate and 'event.preventDefault();' in gate[gate.find('handleStaffingClientAction'):])
handler=UI[UI.index('function handleStaffingClientAction'):UI.index('if(allocationList) allocationRows().forEach')]
check('client auto action delegates shared optimizer', 'W.optimizeRemaining(allocationSlotsData,allocationPeopleData,personState,slotState)' in handler)
check('client auto action validates existing rows first', 'const invalid=state.rowState.filter' in handler and "if(invalid.length)" in handler)
check('client auto action preserves atomic partial locks', 'atomic_blocked:assigned>0&&remaining>0' in handler)
check('client auto action appends proposal instead of replacing manual rows', 'proposed.forEach(allocationAppendOptimizerRow)' in handler and 'allocationRows().forEach(function(row){row.remove();})' not in handler)
append_frag=UI[UI.index('function allocationAppendOptimizerRow'):UI.index('function allocationClientMessage')]
check('optimizer proposal rows preserve lazy select loading', "dataset.optionsLoaded='0'" in append_frag and 'allocationPopulateAllSlots(row,false)' not in append_frag and 'allocationPopulatePeopleForSlot(row,false)' not in append_frag)
check('client check action uses live validator without POST', "if(action==='allocation')" in handler and 'updateAllocationSummary();' in handler)
check('optimizer exception deliberately allows server fallback', "return false; // progressive server fallback" in handler)
check('no fetch/XHR introduced for allocation actions', 'fetch(' not in handler and 'XMLHttpRequest' not in handler)
check('server optimizer remains as progressive fallback/reference', "$staffingAction === 'allocation_auto'" in PAGE and 'teachingAllocationEngineProposal' in PAGE)
if FAIL:
    print('\nStaffing client allocation actions: FAIL (%d)'%len(FAIL));sys.exit(1)
print('\nStaffing client allocation actions: PASS (%d checks)'%(13))
