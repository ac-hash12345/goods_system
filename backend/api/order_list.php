<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $userId = (int) request_data('user_id', 0);
    if ($userId <= 0) {
        json_fail('未登录，无法获取订单');
    }

    // 核心修复：WHERE user_id = :user_id 仅查属于自己的！
    $stmt = db()->prepare('SELECT id, order_no, user_id, goods_info, total_price, status, create_time FROM orders WHERE user_id = :user_id ORDER BY id DESC');
    $stmt->execute(['user_id' => $userId]);
    
    $list = [];
    $statusMap = [0 => '已付款/待发货', 1 => '已发货', 2 => '已完成'];

    foreach ($stmt->fetchAll() as $row) {
        $goodsArr = json_decode($row['goods_info'], true);
        $goodsDesc = [];
        if (is_array($goodsArr)) {
            foreach($goodsArr as $g) {
                $goodsDesc[] = $g['name'] . ' x' . $g['quantity'];
            }
        }
        
        $list[] = [
            'id' => (int) $row['id'],
            'order_no' => $row['order_no'],
            'user_id' => (int) $row['user_id'],
            'goods_info' => implode('，', $goodsDesc), 
            'total_price' => $row['total_price'],
            'status' => (int) $row['status'],
            'status_text' => $statusMap[(int) $row['status']] ?? '未知',
            'create_time' => $row['create_time'],
        ];
    }

    json_ok(['list' => $list]);
} catch (Throwable $e) {
    json_fail('获取订单列表失败：' . $e->getMessage());
}