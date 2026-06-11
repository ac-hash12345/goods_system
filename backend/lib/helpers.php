<?php
declare(strict_types=1);

function request_data(string $key, $default = null)
{
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json) && array_key_exists($key, $json)) {
            return $json[$key];
        }
    }

    return $_GET[$key] ?? $default;
}

function now_string(): string
{
    return date('Y-m-d H:i:s');
}