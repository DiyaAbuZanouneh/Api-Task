<?php

require_once __DIR__ . "/../includes/bootstrap.php";
requireMethod("POST");

$_SESSION = [];
session_destroy();

respond(["success" => true]);