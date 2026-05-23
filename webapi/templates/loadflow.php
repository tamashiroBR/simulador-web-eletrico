<?php
// PHP 8 compatible: uses sequential LoadFlow instead of LoadFlowT (which requires pthreads)
// Suppress HTML error output — only JSON must be returned
ini_set('display_errors', 0);
error_reporting(E_ALL);

require __DIR__ . '/../bootstrap.php';

use NDSE\Tools\LoadFlow;

try {
    if (!is_null($data)) {

        // json_decode returns stdClass objects and arrays of stdClass.
        // The LoadFlow class expects plain indexed PHP arrays for bus and branch rows.
        // Convert recursively: stdClass → array, nested arrays preserved.
        $toArray = function ($value) use (&$toArray) {
            if (is_object($value)) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                return array_map($toArray, array_values($value));
            }
            return $value;
        };

        $dataArr = [
            'optLF'  => $toArray($data['optLF']),
            'bus'    => $toArray($data['bus']),
            'branch' => $toArray($data['branch']),
        ];

        $lf = new LoadFlow($dataArr);
        $lf->makeYbus();
        $result = $lf->run();

        if (empty($result)) {
            echo json_encode(['iteration' => null, 'bus' => null, 'branch' => null, 'loss' => null]);
        } else {
            echo $result;
        }
    }

} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file'  => basename($e->getFile()),
        'line'  => $e->getLine()
    ]);
}
