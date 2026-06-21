// 后台管理员登录页面

<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = trim((string) ($_POST['password'] ?? ''));

    $stmt = db()->prepare('SELECT * FROM admin WHERE username = :username AND password = :password LIMIT 1');
    $stmt->execute(['username' => $username, 'password' => $password]);
    $admin = $stmt->fetch();
    if ($admin) {
        $_SESSION['admin'] = $admin;
        header('Location: index.php');
        exit;
    }
    $error = '账号或密码错误';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>管理员登录</title><style>body{font-family:Arial;background:#f5f7fb;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}.card{background:#fff;padding:32px;border-radius:16px;box-shadow:0 10px 30px rgba(0,0,0,.08);width:360px}input,button{width:100%;padding:12px;margin-top:12px;box-sizing:border-box}button{background:#1677ff;color:#fff;border:0;border-radius:8px}</style></head>
<body>
<div class="card">
  <h2>管理员登录</h2>
  <?php if ($error): ?><p style="color:#d00"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="账号">
    <input name="password" type="password" placeholder="密码">
    <button type="submit">登录</button>
  </form>
</div>
</body>
</html>