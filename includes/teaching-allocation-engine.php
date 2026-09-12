<?php
/**
 * Αυτόματη ΠΡΟΤΑΣΗ κατανομής διδακτικών ωρών.
 *
 * Στόχος (λεξικογραφικά):
 * 1. μέγιστη δυνατή κάλυψη ωρών με το διαθέσιμο προσωπικό,
 * 2. Α΄/ειδική ανάθεση πριν από Β΄ και Β΄ πριν από Γ΄,
 * 3. κύρια ειδικότητα πριν από 2η ειδικότητα όταν όλα τα άλλα είναι ίσα.
 *
 * Οι ήδη δηλωμένες γραμμές θεωρούνται κλειδωμένες από τον χρήστη και ο
 * engine συμπληρώνει μόνο το υπόλοιπο. Για την αυτόματη πρόταση το συνήθες
 * όριο των 10 ωρών Β΄ ανάθεσης τηρείται ως hard limit· χειροκίνητη υπέρβαση
 * εξακολουθεί να ελέγχεται από το personnelWorkloadRosterSlotPlan ως ισχυρή
 * προειδοποίηση.
 *
 * Κρίσιμο invariant: κάθε πραγματικό μάθημα/τμήμα είναι αδιαίρετη μονάδα
 * ανάθεσης. Δεν επιτρέπεται να σπάσει π.χ. ένα 4ωρο μάθημα σε 3+1 ώρες.
 * Ο solver χρησιμοποιεί component-wise atomic optimization. Για components
 * ενός εκπαιδευτικού εφαρμόζει ακριβές μικρό dynamic programming, ενώ στα
 * σύνθετα components branch-and-bound με symmetry pruning. Η παλιά heuristic
 * λύση παραμένει μόνο ως αρχικό lower bound / ασφαλές fallback.
 *
 * PHP 5.6+ compatible.
 */

require_once __DIR__ . '/personnel-workload.php';

/**
 * Χαμηλού επιπέδου solver για το υπόλοιπο ενός ήδη έγκυρου πλάνου.
 * $personState: person_id => remaining_hours, b_assignment_hours, b_remaining_hours
 * $slotState: slot_id => remaining_hours
 */
function teachingAllocationEngineHeuristicSolveRemaining($slots, $people, $personState, $slotState)
{
    $peopleIndex = array();
    foreach ($people as $person) {
        $personId = isset($person['person_id']) ? trim((string)$person['person_id']) : '';
        if ($personId === '' || !isset($personState[$personId])) continue;
        if ((int)$personState[$personId]['remaining_hours'] < 1) continue;
        $peopleIndex[$personId] = $person;
    }

    $remainingAtStart = 0;
    foreach ($slotState as $state) {
        if (!empty($state['atomic_blocked'])) continue;
        $remainingAtStart += isset($state['remaining_hours']) ? max(0, (int)$state['remaining_hours']) : 0;
    }
    if (empty($peopleIndex) || empty($slots)) {
        return array(
            'allocations'=>array(),
            'summary'=>array('covered_hours'=>0,'remaining_slot_hours'=>$remainingAtStart,'route_count'=>0,'augmentations'=>0,'min_cost'=>0,'optimization_group_count'=>0,'atomic'=>true),
            'people'=>$personState,
            'slots'=>$slotState,
        );
    }

    /*
     * IMPORTANT: κάθε πραγματικό slot (π.χ. Μαθηματικά Α1, 4 ώρες) είναι
     * αδιαίρετη μονάδα ανάθεσης. Το παλιό min-cost flow δούλευε σε ώρες και
     * μπορούσε να δώσει 3+1 σε δύο εκπαιδευτικούς. Αυτό είναι χρήσιμη
     * μαθηματική χαλάρωση, όχι έγκυρη σχολική κατανομή.
     *
     * Ο atomic solver δουλεύει με ολόκληρα slots. Πρώτα τα slots με τους
     * λιγότερους πραγματικά διαθέσιμους εκπαιδευτικούς και στη συνέχεια
     * εφαρμόζει βαθμίδα ανάθεσης, ευελιξία εκπαιδευτικού και tight-fit
     * tie-breaks. Ένα μικρό repair pass μετακινεί μία ήδη ολόκληρη ανάθεση
     * όταν αυτό αρκεί για να καλυφθεί ένα στενότερο ακάλυπτο slot.
     */
    $allocations = array();
    $routeCount = 0;

    // Static legal routes: eligibility does not change while capacities do.
    $routesBySlot = array();
    $flexibility = array();
    foreach ($peopleIndex as $personId=>$person) $flexibility[$personId] = 0;
    foreach ($slots as $slotId=>$slot) {
        $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
        if ($need < 1 || !empty($slotState[$slotId]['atomic_blocked'])) continue;
        $routesBySlot[$slotId] = array();
        foreach ($peopleIndex as $personId=>$person) {
            $match = personnelWorkloadBestAssignmentForSlot($slot, $person);
            if ($match === null) continue;
            $routesBySlot[$slotId][$personId] = $match;
            $flexibility[$personId]++;
            $routeCount++;
        }
    }

    $candidateFor = function($slotId, $personId, $requireCapacity) use (&$routesBySlot, &$personState, &$slotState) {
        if (!isset($routesBySlot[$slotId][$personId], $personState[$personId])) return null;
        $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
        if ($need < 1 || !empty($slotState[$slotId]['atomic_blocked'])) return null;
        $match = $routesBySlot[$slotId][$personId];
        $available = max(0, (int)$personState[$personId]['remaining_hours']);
        if ($match['priority'] === 'B') {
            $available = min($available, max(0, (int)$personState[$personId]['b_remaining_hours']));
        }
        if ($requireCapacity && $available < $need) return null;
        $match['available_hours'] = $available;
        return $match;
    };

    $makeSlotOrder = function() use (&$slots, &$slotState, &$routesBySlot, &$candidateFor) {
        $rows = array();
        foreach ($slots as $slotId=>$slot) {
            $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
            if ($need < 1 || !empty($slotState[$slotId]['atomic_blocked'])) continue;
            $count = 0; $bestPriorityRank = 999;
            if (!empty($routesBySlot[$slotId])) {
                foreach ($routesBySlot[$slotId] as $personId=>$route) {
                    $candidate = $candidateFor($slotId, $personId, true);
                    if ($candidate === null) continue;
                    $count++;
                    $bestPriorityRank = min($bestPriorityRank, personnelWorkloadPriorityRank($candidate['priority']));
                }
            }
            $rows[] = array('slot_id'=>$slotId,'need'=>$need,'candidate_count'=>$count,'best_priority_rank'=>$bestPriorityRank);
        }
        usort($rows, function($a,$b) use ($slots) {
            // 0 candidates first so they remain visible as true gaps, then scarce slots.
            if ($a['candidate_count'] !== $b['candidate_count']) return $a['candidate_count'] - $b['candidate_count'];
            if ($a['best_priority_rank'] !== $b['best_priority_rank']) return $a['best_priority_rank'] - $b['best_priority_rank'];
            if ($a['need'] !== $b['need']) return $b['need'] - $a['need'];
            $sa = isset($slots[$a['slot_id']]['subject']) ? $slots[$a['slot_id']]['subject'] : '';
            $sb = isset($slots[$b['slot_id']]['subject']) ? $slots[$b['slot_id']]['subject'] : '';
            $c = strnatcmp($sa,$sb); if ($c !== 0) return $c;
            return strnatcmp($a['slot_id'],$b['slot_id']);
        });
        return $rows;
    };

    $remainingFlexibility = function($personId) use (&$flexibility) {
        return isset($flexibility[$personId]) ? (int)$flexibility[$personId] : 999999;
    };

    $assignWhole = function($slotId, $personId, $match, $source = 'automatic_proposal') use (&$allocations, &$slots, &$slotState, &$personState) {
        $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
        if ($need < 1) return false;
        $available = max(0, (int)$personState[$personId]['remaining_hours']);
        if ($match['priority'] === 'B') $available = min($available, max(0, (int)$personState[$personId]['b_remaining_hours']));
        if ($available < $need) return false;
        $slot = $slots[$slotId];
        $allocations[] = array(
            'person_id'=>$personId,
            'slot_id'=>$slotId,
            'slot_label'=>isset($slot['slot_label']) ? $slot['slot_label'] : '',
            'subject'=>isset($slot['subject']) ? $slot['subject'] : '',
            'hours'=>$need,
            'priority'=>$match['priority'],
            'used_specialty_code'=>$match['used_specialty_code'],
            'specialty_source'=>$match['specialty_source'],
            'source'=>$source,
        );
        $personState[$personId]['remaining_hours'] = max(0, (int)$personState[$personId]['remaining_hours'] - $need);
        if ($match['priority'] === 'B') {
            $personState[$personId]['b_assignment_hours'] = (int)$personState[$personId]['b_assignment_hours'] + $need;
            $personState[$personId]['b_remaining_hours'] = max(0, 10 - (int)$personState[$personId]['b_assignment_hours']);
        }
        $slotState[$slotId]['remaining_hours'] = 0;
        return true;
    };

    // Main atomic pass.
    $slotRows = $makeSlotOrder();
    foreach ($slotRows as $slotRow) {
        $slotId = $slotRow['slot_id'];
        $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
        if ($need < 1) continue;
        $candidates = array();
        $slotRoutes = isset($routesBySlot[$slotId]) ? $routesBySlot[$slotId] : array();
        foreach ($slotRoutes as $personId=>$route) {
            $match = $candidateFor($slotId, $personId, true);
            if ($match === null) continue;
            $candidates[] = array(
                'person_id'=>$personId,
                'match'=>$match,
                'flexibility'=>$remainingFlexibility($personId),
                'leftover'=>max(0, (int)$match['available_hours'] - $need),
            );
        }
        usort($candidates, function($a,$b) {
            $r = personnelWorkloadPriorityRank($a['match']['priority']) - personnelWorkloadPriorityRank($b['match']['priority']);
            if ($r !== 0) return $r;
            if ($a['flexibility'] !== $b['flexibility']) return $a['flexibility'] - $b['flexibility'];
            if ($a['leftover'] !== $b['leftover']) return $a['leftover'] - $b['leftover'];
            if ($a['match']['specialty_source'] !== $b['match']['specialty_source']) return $a['match']['specialty_source'] === 'primary' ? -1 : 1;
            return strnatcmp($a['person_id'],$b['person_id']);
        });
        if (!empty($candidates)) $assignWhole($slotId, $candidates[0]['person_id'], $candidates[0]['match']);
    }

    /* One-move repair. Αν ένα ακάλυπτο slot χωρά σε εκπαιδευτικό μόνο αν
     * μετακινηθεί μία ήδη ανατεθειμένη ολόκληρη μονάδα του σε άλλον, κάνε
     * τη μετακίνηση όταν δεν χειροτερεύει τη βαθμίδα της μετακινούμενης
     * ανάθεσης και αυξάνει την κάλυψη. */
    $progress = true; $repairPasses = 0;
    while ($progress && $repairPasses < 2) {
        $progress = false; $repairPasses++;
        $openRows = $makeSlotOrder();
        foreach ($openRows as $openRow) {
            $slotId = $openRow['slot_id'];
            $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
            if ($need < 1) continue;
            $targetRoutes = isset($routesBySlot[$slotId]) ? $routesBySlot[$slotId] : array();
            foreach ($targetRoutes as $targetId=>$targetRoute) {
                $targetMatch = $candidateFor($slotId, $targetId, false);
                if ($targetMatch === null) continue;
                $available = max(0, (int)$personState[$targetId]['remaining_hours']);
                if ($targetMatch['priority'] === 'B') $available = min($available, max(0, (int)$personState[$targetId]['b_remaining_hours']));
                if ($available >= $need) {
                    if ($assignWhole($slotId,$targetId,$targetMatch,'automatic_repair')) { $progress = true; break 2; }
                    continue;
                }
                $deficit = $need - $available;
                foreach ($allocations as $idx=>$existing) {
                    if ($existing['person_id'] !== $targetId) continue;
                    $moveHours = (int)$existing['hours'];
                    if ($moveHours < $deficit) continue;
                    $moveSlotId = $existing['slot_id'];
                    foreach ($peopleIndex as $altId=>$altPerson) {
                        if ($altId === $targetId) continue;
                        $altMatch = isset($routesBySlot[$moveSlotId][$altId]) ? $routesBySlot[$moveSlotId][$altId] : null;
                        if ($altMatch === null) continue;
                        // Δεν μετακινούμε σε χειρότερη βαθμίδα μόνο για repair.
                        if (personnelWorkloadPriorityRank($altMatch['priority']) > personnelWorkloadPriorityRank($existing['priority'])) continue;
                        $altAvailable = max(0, (int)$personState[$altId]['remaining_hours']);
                        if ($altMatch['priority'] === 'B') $altAvailable = min($altAvailable, max(0, (int)$personState[$altId]['b_remaining_hours']));
                        if ($altAvailable < $moveHours) continue;
                        $targetAfterMove = max(0, (int)$personState[$targetId]['remaining_hours']) + $moveHours;
                        if ($targetMatch['priority'] === 'B') {
                            $targetBAfterMove = max(0, (int)$personState[$targetId]['b_remaining_hours'])
                                + ($existing['priority'] === 'B' ? $moveHours : 0);
                            $targetAfterMove = min($targetAfterMove, $targetBAfterMove);
                        }
                        if ($targetAfterMove < $need) continue;

                        // Undo existing from target.
                        $personState[$targetId]['remaining_hours'] += $moveHours;
                        if ($existing['priority'] === 'B') {
                            $personState[$targetId]['b_assignment_hours'] = max(0, (int)$personState[$targetId]['b_assignment_hours'] - $moveHours);
                            $personState[$targetId]['b_remaining_hours'] = max(0, 10 - (int)$personState[$targetId]['b_assignment_hours']);
                        }
                        // Move whole existing slot to alternate.
                        $allocations[$idx]['person_id'] = $altId;
                        $allocations[$idx]['priority'] = $altMatch['priority'];
                        $allocations[$idx]['used_specialty_code'] = $altMatch['used_specialty_code'];
                        $allocations[$idx]['specialty_source'] = $altMatch['specialty_source'];
                        $allocations[$idx]['source'] = 'automatic_repair_move';
                        $personState[$altId]['remaining_hours'] -= $moveHours;
                        if ($altMatch['priority'] === 'B') {
                            $personState[$altId]['b_assignment_hours'] = (int)$personState[$altId]['b_assignment_hours'] + $moveHours;
                            $personState[$altId]['b_remaining_hours'] = max(0, 10 - (int)$personState[$altId]['b_assignment_hours']);
                        }
                        if ($assignWhole($slotId,$targetId,$targetMatch,'automatic_repair')) { $progress = true; break 4; }
                    }
                }
            }
        }
    }

    usort($allocations, function($a, $b) use ($slots) {
        $sa = isset($slots[$a['slot_id']]) ? $slots[$a['slot_id']] : array();
        $sb = isset($slots[$b['slot_id']]) ? $slots[$b['slot_id']] : array();
        $g = strnatcmp(isset($sa['grade']) ? $sa['grade'] : '', isset($sb['grade']) ? $sb['grade'] : '');
        if ($g !== 0) return $g;
        $s = strnatcmp(isset($sa['subject']) ? $sa['subject'] : '', isset($sb['subject']) ? $sb['subject'] : '');
        if ($s !== 0) return $s;
        return strnatcmp(isset($sa['slot_label']) ? $sa['slot_label'] : '', isset($sb['slot_label']) ? $sb['slot_label'] : '');
    });

    $covered = 0;
    foreach ($allocations as $row) $covered += (int)$row['hours'];
    $remainingSlots = 0;
    foreach ($slotState as $state) {
        if (!empty($state['atomic_blocked'])) continue;
        $remainingSlots += isset($state['remaining_hours']) ? max(0, (int)$state['remaining_hours']) : 0;
    }
    return array(
        'allocations'=>$allocations,
        'summary'=>array(
            'covered_hours'=>$covered,
            'remaining_slot_hours'=>$remainingSlots,
            'route_count'=>$routeCount,
            'augmentations'=>0,
            'min_cost'=>0,
            'optimization_group_count'=>count($slotRows),
            'atomic'=>true,
            'repair_passes'=>$repairPasses,
        ),
        'people'=>$personState,
        'slots'=>$slotState,
    );
}


/**
 * Σύγκριση λεξικογραφικού στόχου του optimizer.
 * 1) συνολική κάλυψη,
 * 2) ώρες Α΄ ή ειδικής ανάθεσης,
 * 3) ώρες Β΄ ανάθεσης,
 * 4) ώρες μέσω κύριας ειδικότητας.
 */
function teachingAllocationEngineCompareObjective($a, $b)
{
    foreach (array('covered','top','b','primary') as $key) {
        $av = isset($a[$key]) ? (int)$a[$key] : 0;
        $bv = isset($b[$key]) ? (int)$b[$key] : 0;
        if ($av === $bv) continue;
        return $av > $bv ? 1 : -1;
    }
    return 0;
}

function teachingAllocationEngineObjectiveForRows($rows)
{
    $o = array('covered'=>0,'top'=>0,'b'=>0,'primary'=>0);
    foreach ($rows as $row) {
        $h = isset($row['hours']) ? max(0, (int)$row['hours']) : 0;
        $p = isset($row['priority']) ? (string)$row['priority'] : '';
        $o['covered'] += $h;
        if ($p === 'A' || $p === 'SPECIAL') $o['top'] += $h;
        elseif ($p === 'B') $o['b'] += $h;
        if (isset($row['specialty_source']) && $row['specialty_source'] === 'primary') $o['primary'] += $h;
    }
    return $o;
}

/**
 * Ακριβής branch-and-bound επί των αδιαίρετων slots ενός συνδεδεμένου
 * component του γράφου slot<->εκπαιδευτικός. Τα ισοδύναμα slots ομαδοποιούνται
 * ώστε ένα σχολείο με πολλά όμοια τμήματα να μην παράγει περιττές μεταθέσεις
 * της ίδιας ακριβώς κατάστασης.
 */
function teachingAllocationEngineOptimizeComponent($groups, $peopleIndex, $initialPeopleState, $seedRows = array(), $nodeLimit = 400000, $deadline = null)
{
    $personIds = array_keys($peopleIndex);
    usort($personIds, 'strnatcmp');

    // Exact shortcut for the very common one-teacher component. This is a
    // tiny 2D knapsack (general remaining hours x remaining B hours), so it
    // certifies the optimum in milliseconds even with hundreds of slots.
    if (count($personIds) === 1) {
        $pid = $personIds[0];
        $capacity = isset($initialPeopleState[$pid]['remaining_hours']) ? max(0,(int)$initialPeopleState[$pid]['remaining_hours']) : 0;
        $bCapacity = isset($initialPeopleState[$pid]['b_remaining_hours']) ? max(0,(int)$initialPeopleState[$pid]['b_remaining_hours']) : 0;
        $items = array();
        foreach ($groups as $group) {
            if (!isset($group['routes'][$pid])) continue;
            foreach ($group['slot_ids'] as $sid) $items[] = array('slot_id'=>$sid,'need'=>(int)$group['need'],'match'=>$group['routes'][$pid],'meta'=>$group['slot_meta'][$sid]);
        }
        $dp = array('0:0'=>array('objective'=>array('covered'=>0,'top'=>0,'b'=>0,'primary'=>0),'rows'=>array(),'used'=>0,'bused'=>0));
        $nodes = 0;
        $aborted = false;
        foreach ($items as $item) {
            if ($deadline !== null && microtime(true) > $deadline) { $aborted = true; break; }
            $next = $dp;
            foreach ($dp as $state) {
                $nodes++;
                if ($nodes > $nodeLimit) { $aborted = true; break 2; }
                $need=$item['need']; $match=$item['match'];
                $newUsed=$state['used']+$need;
                $newB=$state['bused']+($match['priority']==='B'?$need:0);
                if ($newUsed>$capacity || $newB>$bCapacity) continue;
                $obj=$state['objective']; $obj['covered']+=$need;
                if ($match['priority']==='A'||$match['priority']==='SPECIAL') $obj['top']+=$need;
                elseif ($match['priority']==='B') $obj['b']+=$need;
                if (isset($match['specialty_source'])&&$match['specialty_source']==='primary') $obj['primary']+=$need;
                $rows=$state['rows']; $meta=$item['meta'];
                $rows[]=array('person_id'=>$pid,'slot_id'=>$item['slot_id'],'slot_label'=>isset($meta['slot_label'])?$meta['slot_label']:'','subject'=>isset($meta['subject'])?$meta['subject']:'','hours'=>$need,'priority'=>$match['priority'],'used_specialty_code'=>isset($match['used_specialty_code'])?$match['used_specialty_code']:'','specialty_source'=>isset($match['specialty_source'])?$match['specialty_source']:'','source'=>'automatic_optimizer_dp');
                $key=$newUsed.':'.$newB;
                if (!isset($next[$key]) || teachingAllocationEngineCompareObjective($obj,$next[$key]['objective'])>0) $next[$key]=array('objective'=>$obj,'rows'=>$rows,'used'=>$newUsed,'bused'=>$newB);
            }
            $dp=$next;
        }
        $best=array('objective'=>array('covered'=>0,'top'=>0,'b'=>0,'primary'=>0),'rows'=>array());
        foreach ($dp as $state) if (teachingAllocationEngineCompareObjective($state['objective'],$best['objective'])>0) $best=$state;
        return array('allocations'=>$best['rows'],'objective'=>$best['objective'],'nodes'=>$nodes,'certified'=>!$aborted);
    }

    $remainingCounts = array();
    $groupOriginalCounts = array();
    foreach ($groups as $gi=>$group) {
        $count = count($group['slot_ids']);
        $remainingCounts[$gi] = $count;
        $groupOriginalCounts[$gi] = $count;
    }

    $rem = array(); $brem = array();
    foreach ($personIds as $pid) {
        $rem[$pid] = isset($initialPeopleState[$pid]['remaining_hours']) ? max(0,(int)$initialPeopleState[$pid]['remaining_hours']) : 0;
        $brem[$pid] = isset($initialPeopleState[$pid]['b_remaining_hours']) ? max(0,(int)$initialPeopleState[$pid]['b_remaining_hours']) : 0;
    }

    // Static equivalence signature: permits safe symmetry pruning between
    // teachers who have exactly the same routes throughout this component.
    $equiv = array();
    foreach ($personIds as $pid) {
        $parts = array();
        foreach ($groups as $group) {
            if (!isset($group['routes'][$pid])) { $parts[] = '-'; continue; }
            $m = $group['routes'][$pid];
            $parts[] = $m['priority'].'/'.(isset($m['specialty_source'])?$m['specialty_source']:'').'/'.(isset($m['used_specialty_code'])?$m['used_specialty_code']:'');
        }
        $equiv[$pid] = implode(';',$parts);
    }

    $bestRows = array_values($seedRows);
    $bestObjective = teachingAllocationEngineObjectiveForRows($bestRows);
    $currentRows = array();
    $currentObjective = array('covered'=>0,'top'=>0,'b'=>0,'primary'=>0);
    $memo = array();
    $nodes = 0; $aborted = false;

    $search = null;
    $search = function() use (&$search, &$groups, &$remainingCounts, &$groupOriginalCounts, &$personIds, &$rem, &$brem, &$equiv, &$bestRows, &$bestObjective, &$currentRows, &$currentObjective, &$memo, &$nodes, &$aborted, $nodeLimit, $deadline) {
        if ($aborted) return;
        $nodes++;
        if ($nodes > $nodeLimit) { $aborted = true; return; }
        if ($deadline !== null && (($nodes & 255) === 0) && microtime(true) > $deadline) { $aborted = true; return; }

        $remainingSlotHours = 0; $allDone = true;
        foreach ($groups as $gi=>$group) {
            $count = isset($remainingCounts[$gi]) ? (int)$remainingCounts[$gi] : 0;
            if ($count > 0) {
                $allDone = false;
                $remainingSlotHours += $count * (int)$group['need'];
            }
        }
        if ($allDone) {
            if (teachingAllocationEngineCompareObjective($currentObjective, $bestObjective) > 0) {
                $bestObjective = $currentObjective;
                $bestRows = $currentRows;
            }
            return;
        }

        $personHours = 0;
        foreach ($personIds as $pid) $personHours += max(0,(int)$rem[$pid]);
        $coverageUpper = $currentObjective['covered'] + min($remainingSlotHours, $personHours);
        if ($coverageUpper < $bestObjective['covered']) return;
        if ($coverageUpper === $bestObjective['covered']) {
            $topUpper = $currentObjective['top'] + min($remainingSlotHours, $personHours);
            if ($topUpper < $bestObjective['top']) return;
        }

        // Memoization of capacity + remaining grouped slots. For identical
        // state, only the best top/primary history can improve the result.
        $keyParts = array(implode(',', $remainingCounts));
        $equivStates = array();
        foreach ($personIds as $pid) {
            $sig = isset($equiv[$pid]) ? $equiv[$pid] : $pid;
            if (!isset($equivStates[$sig])) $equivStates[$sig]=array();
            $equivStates[$sig][]=$rem[$pid].':'.$brem[$pid];
        }
        ksort($equivStates);
        foreach ($equivStates as $sig=>$states) { sort($states,SORT_STRING); $keyParts[]=$sig.'='.implode(',',$states); }
        $key = implode('|',$keyParts);
        $history = array('top'=>$currentObjective['top'],'primary'=>$currentObjective['primary']);
        if (isset($memo[$key])) {
            $seen = $memo[$key];
            if ($seen['top'] > $history['top'] || ($seen['top'] === $history['top'] && $seen['primary'] >= $history['primary'])) return;
        }
        $memo[$key] = $history;

        // Dynamic most-constrained group first.
        $chosen = null; $chosenCandidates = array(); $chosenFeasibleCount = PHP_INT_MAX;
        foreach ($groups as $gi=>$group) {
            if (empty($remainingCounts[$gi])) continue;
            $need = (int)$group['need'];
            $candidates = array();
            foreach ($group['routes'] as $pid=>$match) {
                if (!isset($rem[$pid]) || $rem[$pid] < $need) continue;
                if ($match['priority'] === 'B' && (!isset($brem[$pid]) || $brem[$pid] < $need)) continue;
                $candidates[] = array('person_id'=>$pid,'match'=>$match,'leftover'=>$rem[$pid]-$need);
            }
            $fc = count($candidates);
            if ($chosen === null || $fc < $chosenFeasibleCount || ($fc === $chosenFeasibleCount && $need > (int)$groups[$chosen]['need'])) {
                $chosen = $gi; $chosenCandidates = $candidates; $chosenFeasibleCount = $fc;
            }
        }
        if ($chosen === null) return;

        // If nobody can currently fit this class/group, all identical copies
        // are necessarily uncovered in this state; skip them in one step.
        if (empty($chosenCandidates)) {
            $old = $remainingCounts[$chosen];
            $remainingCounts[$chosen] = 0;
            $search();
            $remainingCounts[$chosen] = $old;
            return;
        }

        usort($chosenCandidates, function($a,$b) {
            $ra = personnelWorkloadPriorityRank($a['match']['priority']);
            $rb = personnelWorkloadPriorityRank($b['match']['priority']);
            if ($ra !== $rb) return $ra - $rb;
            $pa = isset($a['match']['specialty_source']) && $a['match']['specialty_source']==='primary' ? 0 : 1;
            $pb = isset($b['match']['specialty_source']) && $b['match']['specialty_source']==='primary' ? 0 : 1;
            if ($pa !== $pb) return $pa - $pb;
            if ($a['leftover'] !== $b['leftover']) return $a['leftover'] - $b['leftover'];
            return strnatcmp($a['person_id'],$b['person_id']);
        });

        $group = $groups[$chosen];
        $need = (int)$group['need'];
        $processedIndex = $groupOriginalCounts[$chosen] - $remainingCounts[$chosen];
        $slotId = $group['slot_ids'][$processedIndex];
        $remainingCounts[$chosen]--;

        $symmetrySeen = array();
        foreach ($chosenCandidates as $candidate) {
            $pid = $candidate['person_id']; $match = $candidate['match'];
            $sym = $equiv[$pid].'|'.$rem[$pid].'|'.$brem[$pid];
            if (isset($symmetrySeen[$sym])) continue;
            $symmetrySeen[$sym] = true;

            $rem[$pid] -= $need;
            if ($match['priority'] === 'B') $brem[$pid] -= $need;
            $row = array(
                'person_id'=>$pid,
                'slot_id'=>$slotId,
                'slot_label'=>isset($group['slot_meta'][$slotId]['slot_label']) ? $group['slot_meta'][$slotId]['slot_label'] : '',
                'subject'=>isset($group['slot_meta'][$slotId]['subject']) ? $group['slot_meta'][$slotId]['subject'] : '',
                'hours'=>$need,
                'priority'=>$match['priority'],
                'used_specialty_code'=>isset($match['used_specialty_code']) ? $match['used_specialty_code'] : '',
                'specialty_source'=>isset($match['specialty_source']) ? $match['specialty_source'] : '',
                'source'=>'automatic_optimizer',
            );
            $currentRows[] = $row;
            $currentObjective['covered'] += $need;
            if ($match['priority'] === 'A' || $match['priority'] === 'SPECIAL') $currentObjective['top'] += $need;
            elseif ($match['priority'] === 'B') $currentObjective['b'] += $need;
            if (isset($match['specialty_source']) && $match['specialty_source'] === 'primary') $currentObjective['primary'] += $need;

            $search();

            if (isset($match['specialty_source']) && $match['specialty_source'] === 'primary') $currentObjective['primary'] -= $need;
            if ($match['priority'] === 'A' || $match['priority'] === 'SPECIAL') $currentObjective['top'] -= $need;
            elseif ($match['priority'] === 'B') $currentObjective['b'] -= $need;
            $currentObjective['covered'] -= $need;
            array_pop($currentRows);
            if ($match['priority'] === 'B') $brem[$pid] += $need;
            $rem[$pid] += $need;
        }

        // Explicit uncovered branch. Necessary because skipping a large slot
        // can free capacity for a better combination of smaller atomic slots.
        $search();
        $remainingCounts[$chosen]++;
    };

    $search();
    return array(
        'allocations'=>$bestRows,
        'objective'=>$bestObjective,
        'nodes'=>$nodes,
        'certified'=>!$aborted,
    );
}

/**
 * Κοινός optimizer Καρτελών 4 και 6.
 *
 * Η προηγούμενη constrained-first λύση παραμένει μόνο ως γρήγορο seed και
 * ασφαλές fallback. Η τελική αναζήτηση γίνεται component-wise με ακριβές
 * branch-and-bound και αδιαίρετα course-section slots. Έτσι αποφεύγεται η
 * κλασική παγίδα 6 αντί 3+2+2 όταν ο εκπαιδευτικός διαθέτει 7 ώρες.
 */
function teachingAllocationEngineSolveRemaining($slots, $people, $personState, $slotState)
{
    $heuristic = teachingAllocationEngineHeuristicSolveRemaining($slots, $people, $personState, $slotState);

    $peopleIndex = array();
    foreach ($people as $person) {
        $pid = isset($person['person_id']) ? trim((string)$person['person_id']) : '';
        if ($pid === '' || !isset($personState[$pid]) || (int)$personState[$pid]['remaining_hours'] < 1) continue;
        $peopleIndex[$pid] = $person;
    }

    $routesBySlot = array(); $routeCount = 0; $openSlotCount = 0;
    foreach ($slots as $slotId=>$slot) {
        $need = isset($slotState[$slotId]['remaining_hours']) ? max(0,(int)$slotState[$slotId]['remaining_hours']) : 0;
        if ($need < 1 || !empty($slotState[$slotId]['atomic_blocked'])) continue;
        $openSlotCount++;
        $routesBySlot[$slotId] = array();
        foreach ($peopleIndex as $pid=>$person) {
            $match = personnelWorkloadBestAssignmentForSlot($slot,$person);
            if ($match === null) continue;
            if ($personState[$pid]['remaining_hours'] < $need) continue;
            if ($match['priority'] === 'B' && $personState[$pid]['b_remaining_hours'] < $need) continue;
            $routesBySlot[$slotId][$pid] = $match;
            $routeCount++;
        }
    }

    // Identical need + identical legal route map => interchangeable slots.
    $groupMap = array();
    foreach ($routesBySlot as $slotId=>$routes) {
        if (empty($routes)) continue;
        $need = (int)$slotState[$slotId]['remaining_hours'];
        $parts = array();
        foreach ($routes as $pid=>$m) $parts[] = $pid.'='.$m['priority'].'/'.(isset($m['specialty_source'])?$m['specialty_source']:'').'/'.(isset($m['used_specialty_code'])?$m['used_specialty_code']:'');
        usort($parts,'strnatcmp');
        $key = $need.'|'.implode(';',$parts);
        if (!isset($groupMap[$key])) $groupMap[$key] = array('need'=>$need,'routes'=>$routes,'slot_ids'=>array(),'slot_meta'=>array());
        $groupMap[$key]['slot_ids'][] = $slotId;
        $groupMap[$key]['slot_meta'][$slotId] = $slots[$slotId];
    }
    $groups = array_values($groupMap);
    usort($groups, function($a,$b) {
        $ca=count($a['routes']); $cb=count($b['routes']);
        if ($ca!==$cb) return $ca-$cb;
        if ($a['need']!==$b['need']) return $b['need']-$a['need'];
        return strnatcmp($a['slot_ids'][0],$b['slot_ids'][0]);
    });

    // Connected components through shared teachers.
    $personToGroups = array();
    foreach ($groups as $gi=>$g) foreach ($g['routes'] as $pid=>$m) { if (!isset($personToGroups[$pid])) $personToGroups[$pid]=array(); $personToGroups[$pid][]=$gi; }
    $visited = array(); $components = array();
    foreach ($groups as $start=>$g0) {
        if (isset($visited[$start])) continue;
        $queue=array($start); $visited[$start]=true; $gis=array(); $pids=array();
        while (!empty($queue)) {
            $gi=array_shift($queue); $gis[]=$gi;
            foreach ($groups[$gi]['routes'] as $pid=>$m) {
                $pids[$pid]=true;
                if (empty($personToGroups[$pid])) continue;
                foreach ($personToGroups[$pid] as $ngi) if (!isset($visited[$ngi])) { $visited[$ngi]=true; $queue[]=$ngi; }
            }
        }
        $components[] = array('group_indexes'=>$gis,'person_ids'=>array_keys($pids));
    }

    $seedBySlot = array();
    foreach ($heuristic['allocations'] as $row) $seedBySlot[$row['slot_id']] = $row;
    $finalAllocations = array(); $allCertified = true; $totalNodes = 0;
    // Safety budget for shared hosting (users.sch.gr: typically 128M / 30s).
    // Exact search is valuable for small/medium connected components, but a
    // large memoized branch-and-bound can consume far more memory than the
    // final answer warrants. When the budget is exhausted we keep the valid
    // heuristic seed and explicitly mark the optimum as uncertified.
    $optimizerDeadline = microtime(true) + 1.25;
    $globalNodeBudget = 30000;
    $safetyFallbackComponents = 0;
    foreach ($components as $component) {
        $componentGroups=array(); $slotSet=array(); $componentPeople=array(); $componentRouteCount=0;
        foreach ($component['group_indexes'] as $gi) {
            $componentGroups[]=$groups[$gi];
            $componentRouteCount += isset($groups[$gi]['routes']) ? count($groups[$gi]['routes']) : 0;
            foreach ($groups[$gi]['slot_ids'] as $sid) $slotSet[$sid]=true;
        }
        foreach ($component['person_ids'] as $pid) if (isset($peopleIndex[$pid])) $componentPeople[$pid]=$peopleIndex[$pid];
        $seedRows=array();
        foreach ($slotSet as $sid=>$dummy) if (isset($seedBySlot[$sid])) $seedRows[]=$seedBySlot[$sid];

        $personCount=count($componentPeople);
        $groupCount=count($componentGroups);
        $tooComplex = $personCount > 28 || $componentRouteCount > 1400 || ($personCount > 1 && $groupCount > 100);
        $outOfBudget = $globalNodeBudget < 1 || microtime(true) > $optimizerDeadline;
        if ($tooComplex || $outOfBudget) {
            $optimized=array('allocations'=>$seedRows,'objective'=>teachingAllocationEngineObjectiveForRows($seedRows),'nodes'=>0,'certified'=>false);
            $safetyFallbackComponents++;
        } else {
            $componentNodeLimit=min(12000,$globalNodeBudget);
            // One-teacher components use bounded DP and are safe even with many
            // interchangeable slots; multi-teacher components use B&B.
            $optimized=teachingAllocationEngineOptimizeComponent($componentGroups,$componentPeople,$personState,$seedRows,$componentNodeLimit,$optimizerDeadline);
            $globalNodeBudget=max(0,$globalNodeBudget-(int)$optimized['nodes']);
            if (empty($optimized['certified'])) $safetyFallbackComponents++;
        }
        foreach ($optimized['allocations'] as $row) $finalAllocations[]=$row;
        $totalNodes += (int)$optimized['nodes'];
        if (empty($optimized['certified'])) $allCertified=false;
    }

    // Apply the selected exact/best-known rows to fresh state.
    $finalPeople=$personState; $finalSlots=$slotState;
    foreach ($finalAllocations as $row) {
        $pid=$row['person_id']; $sid=$row['slot_id']; $h=(int)$row['hours'];
        if (!isset($finalPeople[$pid],$finalSlots[$sid])) continue;
        $finalPeople[$pid]['remaining_hours']=max(0,(int)$finalPeople[$pid]['remaining_hours']-$h);
        if ($row['priority']==='B') {
            $finalPeople[$pid]['b_assignment_hours']=(int)$finalPeople[$pid]['b_assignment_hours']+$h;
            $finalPeople[$pid]['b_remaining_hours']=max(0,10-(int)$finalPeople[$pid]['b_assignment_hours']);
        }
        $finalSlots[$sid]['remaining_hours']=0;
    }

    usort($finalAllocations,function($a,$b) use ($slots) {
        $sa=isset($slots[$a['slot_id']])?$slots[$a['slot_id']]:array();
        $sb=isset($slots[$b['slot_id']])?$slots[$b['slot_id']]:array();
        $g=strnatcmp(isset($sa['grade'])?$sa['grade']:'',isset($sb['grade'])?$sb['grade']:''); if($g!==0)return $g;
        $s=strnatcmp(isset($sa['subject'])?$sa['subject']:'',isset($sb['subject'])?$sb['subject']:''); if($s!==0)return $s;
        return strnatcmp($a['slot_id'],$b['slot_id']);
    });

    $covered=0; foreach($finalAllocations as $row)$covered+=(int)$row['hours'];
    $remainingSlots=0; foreach($finalSlots as $state) { if(!empty($state['atomic_blocked']))continue; $remainingSlots+=isset($state['remaining_hours'])?max(0,(int)$state['remaining_hours']):0; }
    return array(
        'allocations'=>$finalAllocations,
        'summary'=>array(
            'covered_hours'=>$covered,
            'remaining_slot_hours'=>$remainingSlots,
            'route_count'=>$routeCount,
            'augmentations'=>0,
            'min_cost'=>0,
            'optimization_group_count'=>$openSlotCount,
            'atomic'=>true,
            'repair_passes'=>isset($heuristic['summary']['repair_passes'])?(int)$heuristic['summary']['repair_passes']:0,
            'optimizer_component_count'=>count($components),
            'optimizer_search_nodes'=>$totalNodes,
            'maximum_coverage_certified'=>$allCertified,
            'heuristic_seed_hours'=>isset($heuristic['summary']['covered_hours'])?(int)$heuristic['summary']['covered_hours']:0,
            'optimizer_safety_fallback_components'=>$safetyFallbackComponents,
        ),
        'people'=>$finalPeople,
        'slots'=>$finalSlots,
    );
}

/**
 * Δημόσιο API του engine. Οι $lockedAllocations είναι οι υπάρχουσες
 * χειροκίνητες γραμμές της Καρτέλας 4 και δεν αλλάζουν.
 */
function teachingAllocationEngineProposal($profile, $people, $lockedAllocations = array(), $model = null, $matrix = null)
{
    if ($model === null) $model = teachingWorkloadModel();
    if ($matrix === null) $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $slots = personnelWorkloadAllocationSlots($profile, $matrix);
    $basePlan = personnelWorkloadRosterSlotPlan($profile, $people, $lockedAllocations, $model, $matrix);

    $invalidRows = isset($basePlan['summary']['invalid_allocation_row_count']) ? (int)$basePlan['summary']['invalid_allocation_row_count'] : 0;
    if ($invalidRows > 0 || !empty($basePlan['summary']['overallocated_slot_hours'])) {
        return array(
            'status'=>'invalid_locked_allocations',
            'message'=>'Διόρθωσε πρώτα τις υπάρχουσες γραμμές κατανομής πριν ζητήσεις αυτόματη πρόταση.',
            'locked_allocations'=>$lockedAllocations,
            'proposed_allocations'=>array(),
            'combined_allocations'=>$lockedAllocations,
            'base_plan'=>$basePlan,
            'combined_plan'=>$basePlan,
            'summary'=>array(
                'locked_hours'=>isset($basePlan['summary']['assigned_slot_hours_total']) ? (int)$basePlan['summary']['assigned_slot_hours_total'] : 0,
                'auto_covered_hours'=>0,
                'final_uncovered_hours'=>isset($basePlan['summary']['unassigned_slot_hours']) ? (int)$basePlan['summary']['unassigned_slot_hours'] : 0,
            ),
            'semantics'=>array('existing_rows_locked'=>true,'maximum_coverage_first'=>true,'automatic_b_assignment_limit_10'=>true),
        );
    }

    $peopleIndex = array();
    foreach ($people as $person) {
        $personId = isset($person['person_id']) ? trim((string)$person['person_id']) : '';
        if ($personId === '') continue;
        $normalized = personnelWorkloadNormalizePerson($person);
        if ($normalized['status'] !== 'resolved') continue;
        $peopleIndex[$personId] = $person;
    }

    $personState = array();
    foreach ($peopleIndex as $personId=>$person) {
        $evaluation = isset($basePlan['people'][$personId]) ? $basePlan['people'][$personId] : null;
        $remaining = $evaluation && isset($evaluation['remaining_hours']) ? max(0, (int)$evaluation['remaining_hours']) : 0;
        $bHours = $evaluation && isset($evaluation['b_assignment_hours']) ? max(0, (int)$evaluation['b_assignment_hours']) : 0;
        $personState[$personId] = array(
            'remaining_hours'=>$remaining,
            'b_assignment_hours'=>$bHours,
            'b_remaining_hours'=>max(0, 10 - $bHours),
            'primary_code'=>isset($person['specialty_code']) ? teacherSpecialtyCanonicalCode($person['specialty_code']) : '',
            'secondary_code'=>isset($person['secondary_specialty_code']) ? teacherSpecialtyCanonicalCode($person['secondary_specialty_code']) : '',
        );
    }

    $slotState = array();
    $partialLockedSlotIds = array();
    foreach ($slots as $slotId=>$slot) {
        $remaining = isset($basePlan['slots'][$slotId]['remaining_hours'])
            ? max(0, (int)$basePlan['slots'][$slotId]['remaining_hours'])
            : max(0, (int)$slot['capacity_hours']);
        $assigned = isset($basePlan['slots'][$slotId]['assigned_hours'])
            ? max(0, (int)$basePlan['slots'][$slotId]['assigned_hours']) : 0;
        $atomicBlocked = $assigned > 0 && $remaining > 0;
        if ($atomicBlocked) $partialLockedSlotIds[] = $slotId;
        $slotState[$slotId] = array('remaining_hours'=>$remaining,'atomic_blocked'=>$atomicBlocked);
    }

    $solved = teachingAllocationEngineSolveRemaining($slots, array_values($peopleIndex), $personState, $slotState);
    $proposal = $solved['allocations'];
    $combined = array_values($lockedAllocations);
    foreach ($proposal as $row) {
        $combined[] = array('person_id'=>$row['person_id'],'slot_id'=>$row['slot_id'],'hours'=>$row['hours']);
    }
    $combinedPlan = personnelWorkloadRosterSlotPlan($profile, array_values($peopleIndex), $combined, $model, $matrix);

    $priorityHours = array('A'=>0,'B'=>0,'C'=>0,'SPECIAL'=>0);
    $splitSlots = array();
    $slotParts = array();
    foreach ($proposal as $row) {
        $p = isset($row['priority']) ? $row['priority'] : '';
        if (!isset($priorityHours[$p])) $priorityHours[$p] = 0;
        $priorityHours[$p] += (int)$row['hours'];
        if (!isset($slotParts[$row['slot_id']])) $slotParts[$row['slot_id']] = 0;
        $slotParts[$row['slot_id']]++;
    }
    foreach ($slotParts as $slotId=>$parts) if ($parts > 1) $splitSlots[] = $slotId;

    return array(
        'status'=>'ok',
        'message'=>'Η αυτόματη πρόταση δημιουργήθηκε. Οι υπάρχουσες γραμμές διατηρήθηκαν ως κλειδωμένες.',
        'locked_allocations'=>$lockedAllocations,
        'proposed_allocations'=>$proposal,
        'combined_allocations'=>$combined,
        'base_plan'=>$basePlan,
        'combined_plan'=>$combinedPlan,
        'optimizer_state'=>array(
            'people'=>isset($solved['people']) ? $solved['people'] : array(),
            'slots'=>isset($solved['slots']) ? $solved['slots'] : array(),
            'summary'=>isset($solved['summary']) ? $solved['summary'] : array(),
        ),
        'summary'=>array(
            'locked_hours'=>isset($basePlan['summary']['assigned_slot_hours_total']) ? (int)$basePlan['summary']['assigned_slot_hours_total'] : 0,
            'auto_covered_hours'=>isset($solved['summary']['covered_hours']) ? (int)$solved['summary']['covered_hours'] : 0,
            'final_uncovered_hours'=>isset($combinedPlan['summary']['unassigned_slot_hours']) ? (int)$combinedPlan['summary']['unassigned_slot_hours'] : 0,
            'final_assigned_hours'=>isset($combinedPlan['summary']['assigned_slot_hours_total']) ? (int)$combinedPlan['summary']['assigned_slot_hours_total'] : 0,
            'priority_hours'=>$priorityHours,
            'split_slot_count'=>count($splitSlots),
            'split_slot_ids'=>$splitSlots,
            'partial_locked_slot_count'=>count($partialLockedSlotIds),
            'partial_locked_slot_ids'=>$partialLockedSlotIds,
        ),
        'semantics'=>array(
            'proposal_only'=>true,
            'existing_rows_locked'=>true,
            'maximum_coverage_first'=>true,
            'min_cost_assignment_priority_after_maximum_coverage'=>true,
            'a_and_special_before_b_before_c'=>true,
            'primary_specialty_tie_break_before_secondary'=>true,
            'automatic_b_assignment_limit_10'=>true,
            'manual_changes_allowed_after_proposal'=>true,
            'course_section_assignment_is_atomic'=>true,
            'automatic_slot_splitting_forbidden'=>true,
            'partial_locked_slots_are_not_auto_completed_by_another_teacher'=>true,
        ),
    );
}
