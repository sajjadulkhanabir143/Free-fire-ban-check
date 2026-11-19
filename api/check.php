<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if (!isset($_GET['uid']) || empty(trim($_GET['uid']))) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'uid' parameter."]);
    exit;
}

$uid = trim($_GET['uid']);

if (!ctype_digit($uid)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid UID format. Only numbers are allowed."]);
    exit;
}

$url = "https://ffapi-gamma.vercel.app/api/ban_check/" . urlencode($uid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

if ($http_code == 200 && $response) {
    $data = json_decode($response, true);
    if (isset($data['is_banned'])) {
        echo json_encode([
            "status" => $data['is_banned'] ? "banned" : "not_banned",
            "message" => $data['is_banned'] ? "Your account is banned." : "Your account is not banned."
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            "error" => "Unexpected API response format.",
            "raw" => $data
        ]);
    }
} else {
    echo json_encode([
        "error" => "API Error (HTTP $http_code)" . ($curl_err ? " - $curl_err" : ""),
        "status_code" => $http_code
    ]);
}
?>
