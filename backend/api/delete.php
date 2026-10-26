<?php

// DELETE /api/delete.php?id=3
require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("DELETE");
$currentId = requireAuth();

$id = (int) ($_GET["id"] ?? 0);
if ($id <= 0) {
    fail("User ID is required.", 422);
}
if ($id === $currentId) {
    fail("You cannot delete your own account.", 403);
}
if (!userModel()->getUserById($id)) {
    fail("User not found.", 404);
}

userModel()->deleteUser($id);
respond(["success" => true, "message" => "User deleted."]);