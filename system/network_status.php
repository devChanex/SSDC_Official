<?php
/* ONLINE SERVER STATUS CHECK */

function checkServerStatus() {

    $ctx = stream_context_create([
        'http' => [
            'timeout' => 3
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ]);

    // ✅ small, fast file
    $url = "https://smilesavedental.ph/favicon.ico";

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
