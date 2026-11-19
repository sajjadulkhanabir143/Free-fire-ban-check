<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    echo json_encode([
        "error" => "Missing 'uid' parameter."
    ]);
    exit;
}

$uid = htmlspecialchars($_GET['uid']);
$url = "https://ffapi-gamma.vercel.app/api/ban_check/" . urlencode($uid);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $data = json_decode($response, true);
    if (isset($data['is_banned'])) {
        $result = [
            "status" => $data['is_banned'] ? "banned" : "not_banned",
            "message" => $data['is_banned'] ? "Your account is banned." : "Your account is not banned."
        ];
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            "error" => "Unexpected response format."
        ]);
    }
} else {
    echo json_encode([
        "error" => "Failed to fetch data.",
        "status_code" => $http_code
    ]);
}
?>
