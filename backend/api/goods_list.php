<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/helpers.php';

try {
    $keyword = trim((string) request_data('keyword', ''));
    $page = max(1, (int) request_data('page', 1));
    $pageSize = max(1, min(20, (int) request_data('pageSize', 10)));
    $offset = ($page - 1) * $pageSize;

    $where = 'WHERE 1=1';
    $params = [];
    if ($keyword !== '') {
        $where .= ' AND name LIKE :keyword';
        $params['keyword'] = '%' . $keyword . '%';
    }

    $pdo = db();
    $countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM goods $where");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetch()['total'];

    $listStmt = $pdo->prepare("SELECT id, name, price, cover, stock, category_id, create_time FROM goods $where ORDER BY id DESC LIMIT :offset, :pageSize");
    foreach ($params as $key => $value) {
        $listStmt->bindValue(':' . $key, $value);
    }
    $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listStmt->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
    $listStmt->execute();

    json_ok([
        'list' => $listStmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
    ]);
} catch (Throwable $e) {
    json_fail('获取商品列表失败：' . $e->getMessage());
    // die("真正的数据库报错是: " . $e->getMessage());
}