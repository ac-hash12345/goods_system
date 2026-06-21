// 获取用户列表接口，返回用户的基本信息，包括ID、昵称、头像、openid和创建时间等。

<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $stmt = db()->query('SELECT id, nickname, avatar, openid, create_time FROM user ORDER BY id DESC');
    json_ok(['list' => $stmt->fetchAll()]);
} catch (Throwable $e) {
    json_fail('获取用户列表失败：' . $e->getMessage());
}