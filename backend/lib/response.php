<?php
declare(strict_types=1);

function json_response(int $code, string $msg, $data = null): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_ok($data = null, string $msg = 'ok'): void
{
    json_response(0, $msg, $data);
}

function json_fail(string $msg, int $code = 1, $data = null): void
{
    json_response($code, $msg, $data);
}