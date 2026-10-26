<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/User.php";

const ALLOWED_ORIGINS = [
    "http://localhost:3000",
    "http://127.0.0.1:3000",
];

$origin = $_SERVER["HTTP_ORIGIN"] ?? "";
if (in_array($origin, ALLOWED_ORIGINS, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Vary: Origin");
}
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

session_set_cookie_params(["httponly" => true, "samesite" => "Lax"]);
session_start();

function respond($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $status = 400): void
{
    respond(["success" => false, "message" => $message], $status);
}

function requireMethod(string $method): void
{
    if ($_SERVER["REQUEST_METHOD"] !== $method) {
        fail("Method not allowed.", 405);
    }
}

function requireAuth(): int
{
    if (!isset($_SESSION["user_id"])) {
        fail("Unauthorized. Please log in.", 401);
    }
    return (int) $_SESSION["user_id"];
}

function jsonInput(): array
{
    $data = json_decode(file_get_contents("php://input"), true);
    return is_array($data) ? $data : [];
}

function userModel(): User
{
    static $model = null;
    if ($model === null) {
        try {
            $model = new User((new Database())->connect());
        } catch (PDOException $e) {
            fail("Database connection failed.", 500);
        }
    }
    return $model;
}

function isDuplicateEmail(PDOException $e): bool
{
    return $e->getCode() === "23000";
}