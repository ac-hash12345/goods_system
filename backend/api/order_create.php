<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $goodsList = request_data('goodsList', []);
    $userId = (int) request_data('user_id', 0); // 接收前端传来的真实用户身份

    if ($userId <= 0) {
        json_fail('请先登录');
    }
    if (!is_array($goodsList) || count($goodsList) === 0) {
        json_fail('订单商品不能为空');
    }

    $pdo = db();
    $pdo->beginTransaction(); // 🌟 开启高并发防御：数据库事务

    $totalPrice = 0.0;
    $goodsInfo = [];

    // 1. 悲观锁防超卖：遍历扣减商品库存
    foreach ($goodsList as $item) {
        $goodsId = (int) ($item['id'] ?? 0);
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        
        $stmt = $pdo->prepare('SELECT id, name, price, stock FROM goods WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $goodsId]);
        $goods = $stmt->fetch();
        
        if (!$goods) {
            throw new Exception('部分商品不存在');
        }
        if ((int) $goods['stock'] < $quantity) {
            throw new Exception($goods['name'] . ' 库存不足');
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

    // 2. 检查并扣减用户钱包余额
    $userStmt = $pdo->prepare('SELECT id, balance FROM user WHERE id = :id FOR UPDATE');
    $userStmt->execute(['id' => $userId]);
    $user = $userStmt->fetch();
    
    if (!$user) {
        throw new Exception('用户状态异常，请重新登录');
    }
    if ((float) $user['balance'] < $totalPrice) {
        throw new Exception('钱包余额不足（需 ￥' . $totalPrice . '），请充值');
    }

    // 执行真实扣款
    $updateBalance = $pdo->prepare('UPDATE user SET balance = balance - :amount WHERE id = :id');
    $updateBalance->execute(['amount' => $totalPrice, 'id' => $userId]);
    $newBalance = (float) $user['balance'] - $totalPrice;

    // 3. 生成真实订单数据
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

    $pdo->commit(); // 🌟 资金、库存、订单全部就绪，提交持久化
    
    // 把扣款后的最新余额返回给前端实时刷新UI
    json_ok(['order_no' => $orderNo, 'new_balance' => $newBalance], '支付成功');

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack(); // 发生任何错误（如余额不足），库存瞬间回滚！
    }
    json_fail($e->getMessage());
}