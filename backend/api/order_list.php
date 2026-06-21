// 获取订单列表接口，返回订单的基本信息，包括订单编号、用户ID、商品信息、总价、订单状态和创建时间等。

<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';

try {
    $stmt = db()->query('SELECT id, order_no, user_id, goods_info, total_price, status, create_time FROM orders ORDER BY id DESC');
    $list = [];
    $statusMap = [0 => '待付款', 1 => '待发货', 2 => '已完成'];

    foreach ($stmt->fetchAll() as $row) {
        $list[] = [
            'id' => (int) $row['id'],
            'order_no' => $row['order_no'],
            'user_id' => (int) $row['user_id'],
            'goods_info' => $row['goods_info'],
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