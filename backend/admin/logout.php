// 后台管理员退出登录页面

<?php
session_start();
session_destroy();
header('Location: login.php');
exit;