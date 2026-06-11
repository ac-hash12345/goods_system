<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $id = (int) request_data('id', 0);
    $status = (int) request_data('status', 0);
    if ($id <= 0) {
        json_fail('订单ID不能为空');
    }

    $stmt = db()->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $stmt->execute(['status' => $status, 'id' => $id]);
    json_ok();
} catch (Throwable $e) {
    json_fail('更新订单状态失败：' . $e->getMessage());
}