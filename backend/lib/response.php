// 定义了几个函数，用于生成标准的JSON响应格式，方便前端处理API返回的数据。

<?php
declare(strict_types=1);

// 生成标准的JSON响应格式
function json_response(int $code, string $msg, $data = null): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE); // 保持中文字符不被转义
    exit;
}

// 生成成功的JSON响应
function json_ok($data = null, string $msg = 'ok'): void
{
    json_response(0, $msg, $data);
}

// 生成失败的JSON响应
function json_fail(string $msg, int $code = 1, $data = null): void
{
    json_response($code, $msg, $data);
}