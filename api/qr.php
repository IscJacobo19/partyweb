<?php
declare(strict_types=1);

header("Content-Type: image/png");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

$data = isset($_GET["data"]) ? trim((string)$_GET["data"]) : "";
$size = isset($_GET["size"]) ? trim((string)$_GET["size"]) : "260";
$size = preg_replace("/[^0-9]/", "", $size);
$size = $size !== "" ? $size : "260";

if ($data === "") {
  http_response_code(400);
  exit;
}

$url = "https://api.qrserver.com/v1/create-qr-code/?size=" . $size . "x" . $size . "&data=" . rawurlencode($data);

$image = null;

if (function_exists("curl_init")) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 8);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $image = curl_exec($ch);
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  if ($status >= 400 || $image === false) {
    $image = null;
  }
} elseif (ini_get("allow_url_fopen")) {
  $context = stream_context_create([
    "http" => [
      "method" => "GET",
      "timeout" => 8,
      "follow_location" => 1,
    ],
  ]);
  $image = @file_get_contents($url, false, $context);
  if ($image === false) {
    $image = null;
  }
}

if (!$image) {
  header("Location: " . $url, true, 302);
  exit;
}

echo $image;
