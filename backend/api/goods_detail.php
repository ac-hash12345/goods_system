// 获取商品详情接口，根据商品ID返回商品的详细信息，包括名称、价格、封面图片、详细描述、库存数量、分类ID和创建时间等。

<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $id = (int) request_data('id', 0);
    if ($id <= 0) {
        json_fail('商品ID不能为空');
    }

    $stmt = db()->prepare('SELECT id, name, price, cover, detail, stock, category_id, create_time FROM goods WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $goods = $stmt->fetch();

    if (!$goods) {
        json_fail('商品不存在');
    }

    json_ok(['goods' => $goods]);
} catch (Throwable $e) {
    json_fail('获取商品详情失败：' . $e->getMessage());
}