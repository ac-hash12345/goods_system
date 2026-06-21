<?php
require_once __DIR__ . '/../config/db.php';

try {
    db()->exec("INSERT IGNORE INTO `admin` (`username`, `password`) VALUES ('admin', '123456')");
    echo "<h1 style='color: green; text-align: center; margin-top: 50px;'>🎉 补丁打入成功！管理员账号 admin 已生成！<br>请直接返回登录页登录！</h1>";
} catch (Exception $e) {
    echo "<h1 style='color: red; text-align: center;'>报错了：" . $e->getMessage() . "</h1>";
}
?>