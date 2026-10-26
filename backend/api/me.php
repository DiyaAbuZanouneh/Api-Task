<?php

// Returns the logged-in user (used by the frontend to check the session).
require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("GET");

$id = requireAuth();
$user = userModel()->getUserById($id);
if (!$user) {
    session_destroy();
    fail("Unauthorized. Please log in.", 401);
}

respond(["success" => true, "user" => $user]);