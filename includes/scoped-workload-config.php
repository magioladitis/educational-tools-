<?php
/**
 * Shared configuration for the staffing simulator's school-scoped regulatory data.
 *
 * `active` schools are used by production workload requests.
 * `prepared` schools have generated/checked snapshots ready for a future activation,
 * but are not loaded by the staffing simulator until they move to `active`.
 */
function scopedWorkloadConfig()
{
    static $config = null;
    if ($config === null) {
        $config = array(
            'active' => array(
                'gymnasio',
                'gel',
                'esperino_gymnasio',
                'esperino_gel',
            ),
            'prepared' => array(
                'protypo_ekklisiastiko_gymnasio',
                'protypo_ekklisiastiko_lykeio',
            ),
        );
    }
    return $config;
}

function scopedWorkloadActiveSchools()
{
    $config = scopedWorkloadConfig();
    return isset($config['active']) && is_array($config['active']) ? $config['active'] : array();
}

function scopedWorkloadPreparedSchools()
{
    $config = scopedWorkloadConfig();
    return isset($config['prepared']) && is_array($config['prepared']) ? $config['prepared'] : array();
}

function scopedWorkloadAllSnapshotSchools()
{
    return array_values(array_unique(array_merge(scopedWorkloadActiveSchools(), scopedWorkloadPreparedSchools())));
}

/**
 * Very cheap production safety net.
 *
 * If a canonical regulatory source was edited after its generated snapshot, the
 * simulator ignores the stale snapshot and falls back to the canonical dataset.
 * Exact content equality is enforced by tools/scoped-workload-sync.php --check.
 */
function scopedWorkloadSnapshotIsFresh($dataset, $snapshotPath)
{
    if (!is_file($snapshotPath)) {
        return false;
    }

    if ($dataset === 'assignments') {
        $sourcePath = __DIR__ . '/teaching-assignments-data.php';
    } elseif ($dataset === 'weekly') {
        $sourcePath = __DIR__ . '/weekly-timetable-data.php';
    } else {
        return false;
    }

    $sourceMtime = @filemtime($sourcePath);
    $snapshotMtime = @filemtime($snapshotPath);
    if ($sourceMtime === false || $snapshotMtime === false) {
        return false;
    }

    return $snapshotMtime >= $sourceMtime;
}
