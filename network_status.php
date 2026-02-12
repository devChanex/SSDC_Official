<?php
/* NETWORK STATUS CHECK ONLY */

function checkInternetConnection() {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 3
        ]
    ]);

    $start = microtime(true);
    $data = @file_get_contents("https://www.google.com", false, $ctx);

    if ($data === false) {
        return ["status" => "offline", "bars" => 0];
    }

    $time = (microtime(true) - $start) * 1000;

    if ($time < 100) return ["status" => "good", "bars" => 4];
    if ($time < 300) return ["status" => "fair", "bars" => 2];
    return ["status" => "slow", "bars" => 1];
    }


header('Content-Type: application/json');
echo json_encode(checkInternetSpeed());