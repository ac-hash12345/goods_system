// 定义了一些辅助函数，包括request_data()用于获取请求数据，now_string()用于获取当前时间的字符串表示。

<?php
declare(strict_types=1);

// 获取请求数据的函数，支持从POST、JSON体和GET中获取参数
function request_data(string $key, $default = null)
{
    // 优先从POST数据中获取参数    
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    // 如果POST数据中没有，再尝试从JSON请求体中获取参数
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json) && array_key_exists($key, $json)) {
            return $json[$key];
        }
    }

    // 最后尝试从GET参数中获取参数
    return $_GET[$key] ?? $default;
}

// 获取当前时间的字符串表示，格式为 "Y-m-d H:i:s"
function now_string(): string
{
    return date('Y-m-d H:i:s');
}