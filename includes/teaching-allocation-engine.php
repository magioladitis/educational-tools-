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
 * Ο πυρήνας είναι min-cost max-flow. Άρα δεν είναι greedy ανά μάθημα: μπορεί
 * να αναδρομολογήσει ισότιμες επιλογές ώστε να καλύψει περισσότερες συνολικά
 * ώρες πριν εξετάσει ποια λύση χρησιμοποιεί καλύτερη βαθμίδα ανάθεσης.
 *
 * PHP 5.6+ compatible.
 */

require_once __DIR__ . '/personnel-workload.php';

function teachingAllocationEnginePriorityCost($priority)
{
    if ($priority === 'A' || $priority === 'SPECIAL') return 0;
    if ($priority === 'B') return 100000;
    if ($priority === 'C') return 200000;
    return 900000;
}

function teachingAllocationEngineRouteCost($match, $personOrder)
{
    $cost = teachingAllocationEnginePriorityCost(isset($match['priority']) ? $match['priority'] : null);
    // Η 2η ειδικότητα είναι απολύτως επιλέξιμη. Το μικρό penalty λειτουργεί
    // μόνο ως tie-break όταν η βαθμίδα ανάθεσης είναι ίδια.
    if (isset($match['specialty_source']) && $match['specialty_source'] === 'secondary') $cost += 1000;
    // Deterministic tie-break, χωρίς να μπορεί να υπερκεράσει την πηγή ή τη
    // βαθμίδα ανάθεσης.
    $cost += max(0, min(999, (int)$personOrder));
    return $cost;
}

function teachingAllocationEngineAddEdge(&$graph, $from, $to, $capacity, $cost)
{
    $capacity = max(0, (int)$capacity);
    $cost = (int)$cost;
    $forwardIndex = count($graph[$from]);
    $reverseIndex = count($graph[$to]);
    $graph[$from][] = array(
        'to'=>$to,
        'rev'=>$reverseIndex,
        'cap'=>$capacity,
        'initial_cap'=>$capacity,
        'cost'=>$cost,
    );
    $graph[$to][] = array(
        'to'=>$from,
        'rev'=>$forwardIndex,
        'cap'=>0,
        'initial_cap'=>0,
        'cost'=>-$cost,
    );
    return $forwardIndex;
}

/**
 * Successive shortest augmenting path με potentials.
 * Επιστρέφει μέγιστη ροή και, ανάμεσα στις μέγιστες ροές, ελάχιστο κόστος.
 */
function teachingAllocationEngineMinCostMaxFlow(&$graph, $source, $sink)
{
    $n = count($graph);
    $potential = array_fill(0, $n, 0);
    $flow = 0;
    $cost = 0;
    $augmentations = 0;
    $inf = PHP_INT_MAX;

    while (true) {
        $dist = array_fill(0, $n, $inf);
        $prevNode = array_fill(0, $n, -1);
        $prevEdge = array_fill(0, $n, -1);
        $dist[$source] = 0;

        $queue = new SplPriorityQueue();
        $queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        $queue->insert($source, 0);

        while (!$queue->isEmpty()) {
            $item = $queue->extract();
            $u = (int)$item['data'];
            $d = -(int)$item['priority'];
            if ($d !== $dist[$u]) continue;

            $edgeCount = count($graph[$u]);
            for ($ei = 0; $ei < $edgeCount; $ei++) {
                $edge = $graph[$u][$ei];
                if ((int)$edge['cap'] < 1) continue;
                $v = (int)$edge['to'];
                $reduced = (int)$edge['cost'] + $potential[$u] - $potential[$v];
                $nd = $d + $reduced;
                if ($nd < $dist[$v]) {
                    $dist[$v] = $nd;
                    $prevNode[$v] = $u;
                    $prevEdge[$v] = $ei;
                    $queue->insert($v, -$nd);
                }
            }
        }

        if ($prevNode[$sink] < 0) break;
        for ($v = 0; $v < $n; $v++) {
            if ($dist[$v] !== $inf) $potential[$v] += $dist[$v];
        }

        $add = $inf;
        for ($v = $sink; $v !== $source; $v = $prevNode[$v]) {
            $u = $prevNode[$v];
            $ei = $prevEdge[$v];
            if ($u < 0 || $ei < 0) { $add = 0; break; }
            $add = min($add, (int)$graph[$u][$ei]['cap']);
        }
        if ($add < 1 || $add === $inf) break;

        $pathCost = 0;
        for ($v = $sink; $v !== $source; $v = $prevNode[$v]) {
            $u = $prevNode[$v];
            $ei = $prevEdge[$v];
            $to = (int)$graph[$u][$ei]['to'];
            $rev = (int)$graph[$u][$ei]['rev'];
            $pathCost += (int)$graph[$u][$ei]['cost'];
            $graph[$u][$ei]['cap'] -= $add;
            $graph[$to][$rev]['cap'] += $add;
        }
        $flow += $add;
        $cost += $add * $pathCost;
        $augmentations++;
    }

    return array('flow'=>$flow, 'cost'=>$cost, 'augmentations'=>$augmentations);
}

/**
 * Χαμηλού επιπέδου solver για το υπόλοιπο ενός ήδη έγκυρου πλάνου.
 * $personState: person_id => remaining_hours, b_assignment_hours, b_remaining_hours
 * $slotState: slot_id => remaining_hours
 */
function teachingAllocationEngineSolveRemaining($slots, $people, $personState, $slotState)
{
    $peopleIndex = array();
    foreach ($people as $person) {
        $personId = isset($person['person_id']) ? trim((string)$person['person_id']) : '';
        if ($personId === '' || !isset($personState[$personId])) continue;
        if ((int)$personState[$personId]['remaining_hours'] < 1) continue;
        $peopleIndex[$personId] = $person;
    }

    /*
     * Για απόδοση σε μεγάλα σχολεία δεν λύνουμε 1 node ανά Α1/Α2/... .
     * Ομαδοποιούμε slots του ίδιου curriculum unit με ακριβώς την ίδια
     * eligibility signature. Αυτό είναι μαθηματικά ισοδύναμο ως προς την
     * κάλυψη, επειδή τα slots της ομάδας έχουν τους ίδιους επιλέξιμους
     * κλάδους. Μετά τη ροή, οι ώρες απλώνονται ξανά στα πραγματικά τμήματα.
     */
    $groups = array();
    foreach ($slots as $slotId=>$slot) {
        $remaining = isset($slotState[$slotId]['remaining_hours']) ? (int)$slotState[$slotId]['remaining_hours'] : 0;
        if ($remaining < 1) continue;
        $unitId = isset($slot['unit_id']) && $slot['unit_id'] !== '' ? (string)$slot['unit_id'] : (string)$slotId;
        $signaturePayload = array(
            'eligible_by_priority'=>isset($slot['eligible_by_priority']) ? $slot['eligible_by_priority'] : array(),
            'top_priority'=>isset($slot['top_priority']) ? $slot['top_priority'] : null,
        );
        $signature = md5(json_encode($signaturePayload));
        $groupKey = $unitId . '|' . $signature;
        if (!isset($groups[$groupKey])) {
            $groups[$groupKey] = array(
                'group_key'=>$groupKey,
                'unit_id'=>$unitId,
                'prototype'=>$slot,
                'capacity_hours'=>0,
                'slot_ids'=>array(),
            );
        }
        $groups[$groupKey]['capacity_hours'] += $remaining;
        $groups[$groupKey]['slot_ids'][] = $slotId;
    }

    $remainingAtStart = 0;
    foreach ($slotState as $state) $remainingAtStart += isset($state['remaining_hours']) ? max(0, (int)$state['remaining_hours']) : 0;
    if (empty($peopleIndex) || empty($groups)) {
        return array(
            'allocations'=>array(),
            'summary'=>array('covered_hours'=>0,'remaining_slot_hours'=>$remainingAtStart,'route_count'=>0,'augmentations'=>0,'min_cost'=>0,'optimization_group_count'=>count($groups)),
            'people'=>$personState,
            'slots'=>$slotState,
        );
    }

    $node = 0;
    $source = $node++;
    $groupNodes = array();
    foreach ($groups as $groupKey=>$group) $groupNodes[$groupKey] = $node++;
    $personBNodes = array();
    $personTotalNodes = array();
    foreach ($peopleIndex as $personId=>$person) {
        $personBNodes[$personId] = $node++;
        $personTotalNodes[$personId] = $node++;
    }
    $sink = $node++;
    $graph = array_fill(0, $node, array());

    foreach ($groups as $groupKey=>$group) {
        teachingAllocationEngineAddEdge($graph, $source, $groupNodes[$groupKey], (int)$group['capacity_hours'], 0);
    }
    foreach ($peopleIndex as $personId=>$person) {
        $remaining = max(0, (int)$personState[$personId]['remaining_hours']);
        $bRemaining = isset($personState[$personId]['b_remaining_hours'])
            ? max(0, (int)$personState[$personId]['b_remaining_hours'])
            : max(0, 10 - (isset($personState[$personId]['b_assignment_hours']) ? (int)$personState[$personId]['b_assignment_hours'] : 0));
        teachingAllocationEngineAddEdge($graph, $personBNodes[$personId], $personTotalNodes[$personId], min($remaining, $bRemaining), 0);
        teachingAllocationEngineAddEdge($graph, $personTotalNodes[$personId], $sink, $remaining, 0);
    }

    $personOrderMap = array(); $personOrder = 0;
    foreach (array_keys($peopleIndex) as $personId) $personOrderMap[$personId] = $personOrder++;
    $routes = array(); $routeCount = 0;
    foreach ($groups as $groupKey=>$group) {
        $slot = $group['prototype'];
        foreach ($peopleIndex as $personId=>$person) {
            $match = personnelWorkloadBestAssignmentForSlot($slot, $person);
            if ($match === null) continue;
            $priority = isset($match['priority']) ? $match['priority'] : null;
            if ($priority === 'B' && (int)$personState[$personId]['b_remaining_hours'] < 1) continue;
            $target = $priority === 'B' ? $personBNodes[$personId] : $personTotalNodes[$personId];
            $edgeIndex = teachingAllocationEngineAddEdge(
                $graph,
                $groupNodes[$groupKey],
                $target,
                (int)$group['capacity_hours'],
                teachingAllocationEngineRouteCost($match, isset($personOrderMap[$personId]) ? $personOrderMap[$personId] : 0)
            );
            $routes[] = array(
                'group_key'=>$groupKey,
                'person_id'=>$personId,
                'from_node'=>$groupNodes[$groupKey],
                'edge_index'=>$edgeIndex,
                'priority'=>$priority,
                'used_specialty_code'=>isset($match['used_specialty_code']) ? $match['used_specialty_code'] : '',
                'specialty_source'=>isset($match['specialty_source']) ? $match['specialty_source'] : '',
            );
            $routeCount++;
        }
    }

    $mcmf = teachingAllocationEngineMinCostMaxFlow($graph, $source, $sink);

    // Συγκεντρωτική ροή ανά curriculum group / εκπαιδευτικό.
    $groupFlows = array();
    foreach ($routes as $route) {
        $edge = $graph[$route['from_node']][$route['edge_index']];
        $used = (int)$edge['initial_cap'] - (int)$edge['cap'];
        if ($used < 1) continue;
        if (!isset($groupFlows[$route['group_key']])) $groupFlows[$route['group_key']] = array();
        $flowRow = $route;
        $flowRow['hours'] = $used;
        $groupFlows[$route['group_key']][] = $flowRow;
        $personState[$route['person_id']]['remaining_hours'] = max(0, (int)$personState[$route['person_id']]['remaining_hours'] - $used);
        if ($route['priority'] === 'B') {
            $personState[$route['person_id']]['b_assignment_hours'] = (int)$personState[$route['person_id']]['b_assignment_hours'] + $used;
            $personState[$route['person_id']]['b_remaining_hours'] = max(0, 10 - (int)$personState[$route['person_id']]['b_assignment_hours']);
        }
    }

    // Expand aggregate flow back to actual A1/A2/... slots. Prefer a route that
    // can take the whole remaining slot; split only when no such route exists.
    $allocations = array();
    foreach ($groups as $groupKey=>$group) {
        $flows = isset($groupFlows[$groupKey]) ? $groupFlows[$groupKey] : array();
        if (empty($flows)) continue;
        usort($flows, function($a,$b){
            if ((int)$a['hours'] !== (int)$b['hours']) return (int)$b['hours'] - (int)$a['hours'];
            $p = personnelWorkloadPriorityRank($a['priority']) - personnelWorkloadPriorityRank($b['priority']);
            if ($p !== 0) return $p;
            if ($a['specialty_source'] !== $b['specialty_source']) return $a['specialty_source'] === 'primary' ? -1 : 1;
            return strnatcmp($a['person_id'],$b['person_id']);
        });
        $slotIds = $group['slot_ids'];
        usort($slotIds, function($a,$b) use ($slots){
            $la = isset($slots[$a]['slot_label']) ? $slots[$a]['slot_label'] : $a;
            $lb = isset($slots[$b]['slot_label']) ? $slots[$b]['slot_label'] : $b;
            return strnatcmp($la,$lb);
        });
        foreach ($slotIds as $slotId) {
            $need = isset($slotState[$slotId]['remaining_hours']) ? max(0, (int)$slotState[$slotId]['remaining_hours']) : 0;
            while ($need > 0) {
                $chosen = -1;
                // First choice: one teacher can take the whole remainder.
                for ($i=0; $i<count($flows); $i++) {
                    if ((int)$flows[$i]['hours'] >= $need) { $chosen = $i; break; }
                }
                // Otherwise use the route with the largest residual amount.
                if ($chosen < 0) {
                    $bestHours = 0;
                    for ($i=0; $i<count($flows); $i++) {
                        if ((int)$flows[$i]['hours'] > $bestHours) { $bestHours = (int)$flows[$i]['hours']; $chosen = $i; }
                    }
                }
                if ($chosen < 0 || (int)$flows[$chosen]['hours'] < 1) break;
                $take = min($need, (int)$flows[$chosen]['hours']);
                $slot = $slots[$slotId];
                $allocations[] = array(
                    'person_id'=>$flows[$chosen]['person_id'],
                    'slot_id'=>$slotId,
                    'slot_label'=>isset($slot['slot_label']) ? $slot['slot_label'] : '',
                    'subject'=>isset($slot['subject']) ? $slot['subject'] : '',
                    'hours'=>$take,
                    'priority'=>$flows[$chosen]['priority'],
                    'used_specialty_code'=>$flows[$chosen]['used_specialty_code'],
                    'specialty_source'=>$flows[$chosen]['specialty_source'],
                    'source'=>'automatic_proposal',
                );
                $flows[$chosen]['hours'] -= $take;
                $need -= $take;
            }
            $slotState[$slotId]['remaining_hours'] = max(0, $need);
        }
    }

    usort($allocations, function($a, $b) use ($slots) {
        $sa = isset($slots[$a['slot_id']]) ? $slots[$a['slot_id']] : array();
        $sb = isset($slots[$b['slot_id']]) ? $slots[$b['slot_id']] : array();
        $g = strnatcmp(isset($sa['grade']) ? $sa['grade'] : '', isset($sb['grade']) ? $sb['grade'] : '');
        if ($g !== 0) return $g;
        $s = strnatcmp(isset($sa['subject']) ? $sa['subject'] : '', isset($sb['subject']) ? $sb['subject'] : '');
        if ($s !== 0) return $s;
        $l = strnatcmp(isset($sa['slot_label']) ? $sa['slot_label'] : '', isset($sb['slot_label']) ? $sb['slot_label'] : '');
        if ($l !== 0) return $l;
        $p = personnelWorkloadPriorityRank($a['priority']) - personnelWorkloadPriorityRank($b['priority']);
        if ($p !== 0) return $p;
        return strnatcmp($a['person_id'], $b['person_id']);
    });

    $remainingSlots = 0;
    foreach ($slotState as $state) $remainingSlots += isset($state['remaining_hours']) ? max(0, (int)$state['remaining_hours']) : 0;
    return array(
        'allocations'=>$allocations,
        'summary'=>array(
            'covered_hours'=>(int)$mcmf['flow'],
            'remaining_slot_hours'=>$remainingSlots,
            'route_count'=>$routeCount,
            'augmentations'=>(int)$mcmf['augmentations'],
            'min_cost'=>(int)$mcmf['cost'],
            'optimization_group_count'=>count($groups),
        ),
        'people'=>$personState,
        'slots'=>$slotState,
    );
}

/**
 * Δημόσιο API του engine. Οι $lockedAllocations είναι οι υπάρχουσες
 * χειροκίνητες γραμμές της Καρτέλας 4 και δεν αλλάζουν.
 */
function teachingAllocationEngineProposal($profile, $people, $lockedAllocations = array(), $model = null)
{
    if ($model === null) $model = teachingWorkloadModel();
    $matrix = schoolProfileWorkloadMatrix($profile, $model);
    $slots = personnelWorkloadAllocationSlots($profile, $matrix);
    $basePlan = personnelWorkloadRosterSlotPlan($profile, $people, $lockedAllocations, $model);

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
        );
    }

    $slotState = array();
    foreach ($slots as $slotId=>$slot) {
        $remaining = isset($basePlan['slots'][$slotId]['remaining_hours'])
            ? max(0, (int)$basePlan['slots'][$slotId]['remaining_hours'])
            : max(0, (int)$slot['capacity_hours']);
        $slotState[$slotId] = array('remaining_hours'=>$remaining);
    }

    $solved = teachingAllocationEngineSolveRemaining($slots, array_values($peopleIndex), $personState, $slotState);
    $proposal = $solved['allocations'];
    $combined = array_values($lockedAllocations);
    foreach ($proposal as $row) {
        $combined[] = array('person_id'=>$row['person_id'],'slot_id'=>$row['slot_id'],'hours'=>$row['hours']);
    }
    $combinedPlan = personnelWorkloadRosterSlotPlan($profile, array_values($peopleIndex), $combined, $model);

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
        'summary'=>array(
            'locked_hours'=>isset($basePlan['summary']['assigned_slot_hours_total']) ? (int)$basePlan['summary']['assigned_slot_hours_total'] : 0,
            'auto_covered_hours'=>isset($solved['summary']['covered_hours']) ? (int)$solved['summary']['covered_hours'] : 0,
            'final_uncovered_hours'=>isset($combinedPlan['summary']['unassigned_slot_hours']) ? (int)$combinedPlan['summary']['unassigned_slot_hours'] : 0,
            'final_assigned_hours'=>isset($combinedPlan['summary']['assigned_slot_hours_total']) ? (int)$combinedPlan['summary']['assigned_slot_hours_total'] : 0,
            'priority_hours'=>$priorityHours,
            'split_slot_count'=>count($splitSlots),
            'split_slot_ids'=>$splitSlots,
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
            'slot_splitting_possible_only_when_flow_requires_multiple_routes'=>true,
        ),
    );
}
