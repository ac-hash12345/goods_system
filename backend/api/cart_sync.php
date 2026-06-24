<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $goodsList = request_data('goodsList', []);
    $userId = (int) request_data('user_id', 0);
    $nickname = (string) request_data('nickname', '未知用户');

    if ($userId <= 0) {
        json_fail('未登录，跳过同步');
    }

    $pdo = db();
    $goodsInfo = json_encode($goodsList, JSON_UNESCAPED_UNICODE);
    $now = now_string();

    $stmt = $pdo->prepare('SELECT id FROM cart WHERE user_id = :user_id LIMIT 1');
    $stmt->execute(['user_id' => $userId]);
    
    if ($stmt->fetch()) {
        $update = $pdo->prepare('UPDATE cart SET goods_info = :goods_info, update_time = :update_time, nickname = :nickname WHERE user_id = :user_id');
        $update->execute(['goods_info' => $goodsInfo, 'update_time' => $now, 'nickname' => $nickname, 'user_id' => $userId]);
    } else {
        $insert = $pdo->prepare('INSERT INTO cart (user_id, nickname, goods_info, update_time) VALUES (:user_id, :nickname, :goods_info, :update_time)');
        $insert->execute(['user_id' => $userId, 'nickname' => $nickname, 'goods_info' => $goodsInfo, 'update_time' => $now]);
    }

    json_ok();
} catch (Throwable $e) {
    json_fail($e->getMessage());
}