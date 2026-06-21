/*
goods_system数据库见表与初始化脚本

5张核心表：
1. admin：管理员账号表，包含id、username、password字段。
2. user：用户表，包含id、nickname、avatar、openid、create_time字段
3. category：商品分类表，包含id、name字段。
4. goods：商品表，包含id、name、price、cover、detail、stock、category_id、create_time字段。
5. orders：订单表，包含id、order_no、user_id、goods_info、total_price、status、create_time字段。

修改：
http://127.0.0.1/goodsSystem/backend/admin/index.php
或
http://localhost/phpMyAdmin4.8.5/
*/

CREATE DATABASE IF NOT EXISTS `goods_system` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `goods_system`; // 切换到goods_system数据库

// 删除旧表并创建新表
// 在创建新表之前，先检查并删除同名的旧表
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `goods`; // 
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `category`;

CREATE TABLE `admin` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`) // 确保管理员用户名唯一
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` VARCHAR(100) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `openid` VARCHAR(100) NOT NULL,
  `create_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_openid` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `goods` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `cover` VARCHAR(255) DEFAULT NULL,
  `detail` TEXT,
  `stock` INT NOT NULL DEFAULT 0,
  `category_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `create_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no` VARCHAR(50) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `goods_info` TEXT NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` TINYINT NOT NULL DEFAULT 0,
  `create_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

// 快速清空表里的所有数据，并且把自增 id 重新归位为 1
TRUNCATE TABLE `category`;
TRUNCATE TABLE `goods`;

INSERT INTO `category` (`name`) VALUES ('外设硬件'), ('考研资料'), ('极客潮玩'), ('提神补给');

INSERT INTO `goods` (`name`, `price`, `cover`, `detail`, `stock`, `category_id`, `create_time`) VALUES
('客制化机械键盘 珂芝K75', 399.00, 'https://picsum.photos/seed/geek1/600/600', 'TTC快银轴，Gasket结构，敲代码神器。', 50, 1, NOW()),
('王道408计算机考研复习全书', 128.00, 'https://picsum.photos/seed/geek2/600/600', '2026版最新，包含数据结构、组成原理、操作系统、计网。', 200, 2, NOW()),
('Raspberry Pi 5 树莓派8G版', 688.00, 'https://picsum.photos/seed/geek3/600/600', '极客首选开发板，支持多种AI模型本地部署。', 30, 3, NOW()),
('现磨挂耳黑咖啡 (50包)', 49.90, 'https://picsum.photos/seed/geek4/600/600', '0糖0脂，深度烘焙，期末复习/打代码熬夜必备。', 500, 4, NOW()),
('人体工学鼠标 罗技MX Master 3', 599.00, 'https://picsum.photos/seed/geek5/600/600', '电磁滚轮，无缝跨屏，保护你的手腕。', 80, 1, NOW()),
('算法导论 (原书第3版)', 99.00, 'https://picsum.photos/seed/geek6/600/600', '计算机科学经典著作，MIT教材。', 150, 2, NOW());