/*
PHP后端连接数据库的配置文件，包含数据库连接信息和一个db()函数，用于获取PDO实例。

账号、密码、端口配置
*/

<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Shanghai');

// 数据库连接配置
const DB_HOST = '127.0.0.1;port=3307';
const DB_NAME = 'goods_system';
const DB_USER = 'goods_system';
const DB_PASS = '123456';
const DB_CHARSET = 'utf8mb4';

// 获取PDO实例的函数
function db(): PDO
{
    static $pdo = null;// 使用静态变量缓存PDO实例，避免重复创建连接
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 设置错误模式为异常
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 设置默认的获取模式为关联数组
    ]);

    return $pdo;
}