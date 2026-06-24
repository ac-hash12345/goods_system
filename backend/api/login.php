<?php
// 账号密码登录与自动注册接口
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    // 1. 获取前端传来的账号和密码
    $username = trim((string) request_data('username', ''));
    $password = trim((string) request_data('password', ''));

    if ($username === '' || $password === '') {
        json_fail('账号和密码不能为空');
    }

    $pdo = db();
    
    // 2. 在数据库中寻找该账号
    $stmt = $pdo->prepare('SELECT * FROM user WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        // 3. 账号存在，校验密码
        if ($user['password'] !== $password) {
            json_fail('密码错误，请重新输入');
        }
    } else {
        // 4. 账号不存在，执行自动注册闭环
        $openid = 'local_' . uniqid(); 
        $nickname = '极客_' . $username; 
        $initialBalance = 8888.00; // 🌟 核心：新用户自动送 8888 测试金
        
        $insert = $pdo->prepare('INSERT INTO user (username, password, nickname, avatar, openid, balance, create_time) VALUES (:username, :password, :nickname, :avatar, :openid, :balance, :create_time)');
        $insert->execute([
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname,
            'avatar' => '/images/avatar.png', 
            'openid' => $openid,
            'balance' => $initialBalance,
            'create_time' => now_string(),
        ]);
        
        $user = [
            'id' => (int) $pdo->lastInsertId(),
            'username' => $username,
            'nickname' => $nickname,
            'avatar' => '/images/avatar.png',
            'openid' => $openid,
            'balance' => $initialBalance, // 🌟 记得把余额一并返回给前端
        ];
    }

    json_ok(['user' => $user], '登录成功');
} catch (Throwable $e) {
    json_fail('登录系统异常：' . $e->getMessage());
}