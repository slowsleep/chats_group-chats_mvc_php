<?php

namespace App\Tools;

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return $token === $_SESSION['csrf_token'];
}

function refreshCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
