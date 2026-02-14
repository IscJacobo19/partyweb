<?php
declare(strict_types=1);

header("X-Content-Type-Options: nosniff");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit;
}

$dataUrl = isset($_POST["image"]) ? (string)$_POST["image"] : "";
$filename = isset($_POST["filename"]) ? (string)$_POST["filename"] : "pase.png";

if (strpos($dataUrl, "data:image/png;base64,") !== 0) {
  http_response_code(400);
  exit;
}

$raw = substr($dataUrl, strlen("data:image/png;base64,"));
$raw = str_replace(["\r", "\n", " "], "", $raw);
$binary = base64_decode($raw, true);
if ($binary === false) {
  http_response_code(400);
  exit;
}

// Max 5MB
if (strlen($binary) > 5 * 1024 * 1024) {
  http_response_code(413);
  exit;
}

$safe = preg_replace("/[^A-Za-z0-9._-]/", "_", $filename);
if ($safe === "" || strlen($safe) > 80) {
  $safe = "pase.png";
}
if (!preg_match("/\\.png$/i", $safe)) {
  $safe .= ".png";
}

header("Content-Type: image/png");
header("Content-Disposition: attachment; filename=\"" . $safe . "\"");
header("Content-Length: " . strlen($binary));
echo $binary;
