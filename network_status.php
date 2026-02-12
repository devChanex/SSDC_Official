<?php
/* NETWORK STATUS CHECK ONLY */

function checkInternetSpeed() {
    $start = microtime(true);
    $connected = @fsockopen("www.google.com", 80, $errno, $errstr, 3);

    if (!$connected) {
        return ["status" => "offline", "bars" => 0];
    }

    $end = microtime(true);
    fclose($connected);

    $responseTime = ($end - $start) * 1000;

    if ($responseTime < 100) {
        return ["status" => "good", "bars" => 4];
    } elseif ($responseTime < 300) {
        return ["status" => "fair", "bars" => 2];
    } else {
        return ["status" => "slow", "bars" => 1];
    }
}

header('Content-Type: application/json');
echo json_encode(checkInternetSpeed());
