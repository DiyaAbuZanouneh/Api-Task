<?php

// GET /api/users.php          -> list all users
// GET /api/users.php?id=3     -> one user
require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("GET");
requireAuth();

if (isset($_GET["id"])) {
    $user = userModel()->getUserById((int) $_GET["id"]);
    if (!$user) {
        fail("User not found.", 404);
    }
    respond(["success" => true, "user" => $user]);
}

respond(["success" => true, "users" => userModel()->getAllUsers()]);
