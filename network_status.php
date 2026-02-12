<?php
/* ONLINE SERVER STATUS CHECK */

function checkServerStatus() {

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 3,
            'method'  => 'HEAD'
        ]
    ]);

    // 🔁 Change this to your own domain
    $url = "https://smilesavedental.ph";

    $start = microtime(true);
    $result = @file_get_contents($url, false, $ctx);

    if ($result === false) {
        return ["status" => "offline", "bars" => 0];
    }

    $time = (microtime(true) - $start) * 1000;

    if ($time < 150) return ["status" => "good", "bars" => 4];
    if ($time < 400) return ["status" => "fair", "bars" => 2];
    return ["status" => "slow", "bars" => 1];
}

header('Content-Type: application/json');
echo json_encode(checkServerStatus());
