<?php
/* Temporary direct-request diagnostic wrapper.
 * If this page loads while ypologismos-didaktikon-anagkon.php returns HTTP 500,
 * the application itself is fine and the problem is specific to direct execution
 * of the original script path / server cache / per-file web-server handling.
 */
require __DIR__ . '/ypologismos-didaktikon-anagkon.php';
