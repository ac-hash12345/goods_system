<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $openid = (string) request_data('openid', '');
    $nickname = (string) request_data('nickname', '微信用户');
    $avatarUrl = (string) request_data('avatarUrl', '');
    $code = (string) request_data('code', '');

    if ($openid === '') {
        $openid = 'openid_' . sha1($code ?: uniqid('', true));
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM user WHERE openid = :openid LIMIT 1');
    $stmt->execute(['openid' => $openid]);
    $user = $stmt->fetch();

    if (!$user) {
        $insert = $pdo->prepare('INSERT INTO user (nickname, avatar, openid, create_time) VALUES (:nickname, :avatar, :openid, :create_time)');
        $insert->execute([
            'nickname' => $nickname,
            'avatar' => $avatarUrl,
            'openid' => $openid,
            'create_time' => now_string(),
        ]);
        $user = [
            'id' => (int) $pdo->lastInsertId(),
            'nickname' => $nickname,
            'avatar' => $avatarUrl,
            'openid' => $openid,
        ];
    }

    json_ok(['user' => $user], '登录成功');
} catch (Throwable $e) {
    json_fail('登录失败：' . $e->getMessage());
}