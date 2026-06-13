CREATE DATABASE IF NOT EXISTS `goods_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `goods_system`;

DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `goods`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `category`;

CREATE TABLE `admin` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
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

-- INSERT INTO `admin` (`username`, `password`) VALUES ('admin', '123456');
-- INSERT INTO `category` (`name`) VALUES ('数码'), ('服饰'), ('食品'), ('家居');
-- INSERT INTO `goods` (`name`, `price`, `cover`, `detail`, `stock`, `category_id`, `create_time`) VALUES
-- ('无线蓝牙耳机', 199.00, 'https://picsum.photos/seed/a1/600/600', '高品质音效，长续航。', 100, 1, NOW()),
-- ('夏季短袖T恤', 89.90, 'https://picsum.photos/seed/a2/600/600', '舒适透气，日常百搭。', 200, 2, NOW()),
-- ('混合坚果礼盒', 59.80, 'https://picsum.photos/seed/a3/600/600', '多种坚果组合，健康营养。', 150, 3, NOW());

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