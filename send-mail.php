<?php
// send-mail.php

header("Access-Control-Allow-Origin: *");  
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Read JSON body ---
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// --- Validate input ---
if (!$data || !isset($data['to']) || !isset($data['subject']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Missing required fields /-"]);
    exit;
}

$to      = filter_var($data['to'], FILTER_SANITIZE_EMAIL);
$subject = strip_tags($data['subject']);
$message = $data['message'];

// --- Headers for HTML email ---
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";

// Optional: set sender if provided
if (isset($data['from'])) {
    $from = filter_var($data['from'], FILTER_SANITIZE_EMAIL);
    $headers .= "From: $from\r\n";
}

// --- Try sending email ---
if (mail($to, $subject, $message, $headers)) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Email sent successfully"]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Email failed to send"]);
}
?>
