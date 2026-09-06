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
 * Ο solver χρησιμοποιεί constrained-first atomic heuristic και μικρό repair
 * pass, με στόχο τη μέγιστη δυνατή κάλυψη χωρίς να παραβιάζει αυτή τη συνθήκη.
 *
 * PHP 5.6+ compatible.
 */

require_once __DIR__ . '/personnel-workload.php';

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
