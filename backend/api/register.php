<?php

require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("POST");

$in = jsonInput();
$name = trim($in["name"] ?? "");
$email = trim($in["email"] ?? "");
$password = $in["password"] ?? "";

if ($name === "" || $email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail("Name and a valid email are required.", 422);
}
if (strlen($password) < 8) {
    fail("Password must be at least 8 characters.", 422);
}

try {
    userModel()->register($name, $email, $password);
} catch (PDOException $e) {
    if (isDuplicateEmail($e)) {
        fail("This email is already registered.", 409);
    }
    fail("Registration failed.", 500);
}

respond(["success" => true, "message" => "Account created."], 201);
