<?php

require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("POST");

$in = jsonInput();
$email = trim($in["email"] ?? "");
$password = $in["password"] ?? "";

if ($email === "" || $password === "") {
    fail("Email and password are required.", 422);
}

$user = userModel()->login($email, $password);
if (!$user) {
    fail("Invalid email or password.", 401);
}

session_regenerate_id(true);
$_SESSION["user_id"] = (int) $user["id"];

respond(["success" => true, "user" => $user]);
