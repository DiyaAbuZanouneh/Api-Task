<?php

// PUT /api/update.php  body: {id, name, email, password?}
require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("PUT");
requireAuth();

$in = jsonInput();
$id = (int) ($in["id"] ?? 0);
$name = trim($in["name"] ?? "");
$email = trim($in["email"] ?? "");
$password = $in["password"] ?? "";

if ($id <= 0) {
    fail("User ID is required.", 422);
}
if ($name === "" || $email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail("Name and a valid email are required.", 422);
}
// Password is optional on update; if sent it must be strong enough.
if ($password !== "" && strlen($password) < 8) {
    fail("Password must be at least 8 characters.", 422);
}

if (!userModel()->getUserById($id)) {
    fail("User not found.", 404);
}

try {
    userModel()->updateUser($id, $name, $email, $password);
} catch (PDOException $e) {
    if (isDuplicateEmail($e)) {
        fail("This email is already registered.", 409);
    }
    fail("Update failed.", 500);
}

respond(["success" => true, "message" => "User updated."]);