// 创建订单接口，根据商品列表创建新的订单

<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $goodsList = request_data('goodsList', []);
    if (!is_array($goodsList) || count($goodsList) === 0) {
        json_fail('订单商品不能为空');
    }

    $pdo = db();
    $totalPrice = 0.0;
    $goodsInfo = [];

    foreach ($goodsList as $item) {
        $goodsId = (int) ($item['id'] ?? 0);
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $stmt = $pdo->prepare('SELECT id, name, price, stock FROM goods WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $goodsId]);
        $goods = $stmt->fetch();
        if (!$goods) {
            json_fail('商品不存在');
        }
        if ((int) $goods['stock'] < $quantity) {
            json_fail($goods['name'] . ' 库存不足');
        }
        $totalPrice += (float) $goods['price'] * $quantity;
        $goodsInfo[] = [
            'id' => (int) $goods['id'],
            'name' => $goods['name'],
            'price' => $goods['price'],
            'quantity' => $quantity,
        ];

        $update = $pdo->prepare('UPDATE goods SET stock = stock - :quantity WHERE id = :id');
        $update->execute(['quantity' => $quantity, 'id' => $goodsId]);
    }

    $userStmt = $pdo->query('SELECT id FROM user ORDER BY id DESC LIMIT 1');
    $user = $userStmt->fetch();
    $userId = $user ? (int) $user['id'] : 0;

    $orderNo = 'OD' . date('YmdHis') . random_int(1000, 9999);
    $insert = $pdo->prepare('INSERT INTO orders (order_no, user_id, goods_info, total_price, status, create_time) VALUES (:order_no, :user_id, :goods_info, :total_price, :status, :create_time)');
    $insert->execute([
        'order_no' => $orderNo,
        'user_id' => $userId,
        'goods_info' => json_encode($goodsInfo, JSON_UNESCAPED_UNICODE),
        'total_price' => number_format($totalPrice, 2, '.', ''),
        'status' => 0,
        'create_time' => now_string(),
    ]);

    json_ok(['order_no' => $orderNo], '下单成功');
} catch (Throwable $e) {
    json_fail('创建订单失败：' . $e->getMessage());
}