# 数据库设计文档

## 1. 数据库概述

- **数据库类型**: MySQL 8.0
- **字符集**: utf8mb4_unicode_ci（支持繁体中文和emoji）
- **存储引擎**: InnoDB
- **设计原则**: 简洁、高效、易维护

## 2. 数据表清单

### 2.1 用户相关表
- `users` - 用户基础信息
- `user_profiles` - 用户详细资料
- `user_photos` - 用户照片
- `user_interests` - 用户兴趣爱好
- `user_settings` - 用户设置

### 2.2 好友和匹配相关表
- `matches` - 匹配关系
- `likes` - 喜欢记录
- `friendships` - 好友关系
- `blocks` - 黑名单

### 2.3 积分和会员相关表
- `user_points` - 用户积分余额
- `point_transactions` - 积分交易记录
- `memberships` - 会员信息
- `membership_levels` - 会员等级配置

### 2.4 礼品相关表
- `gifts` - 礼品信息
- `gift_categories` - 礼品分类
- `gift_transactions` - 礼品交易记录
- `user_gifts` - 用户收到的礼品

### 2.5 系统相关表
- `admin_users` - 后台管理员
- `system_settings` - 系统设置
- `logs` - 操作日志

## 3. 详细表结构

### 3.1 用户基础信息表 (users)

```sql
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `phone` VARCHAR(20) NOT NULL UNIQUE COMMENT '手机号',
    `password` VARCHAR(255) NOT NULL COMMENT '密码哈希',
    `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
    `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像URL',
    `gender` TINYINT DEFAULT 0 COMMENT '性别:0未知,1男,2女',
    `birthday` DATE DEFAULT NULL COMMENT '生日',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0禁用,1正常',
    `is_vip` TINYINT DEFAULT 0 COMMENT '是否VIP:0否,1是',
    `vip_level` TINYINT DEFAULT 0 COMMENT 'VIP等级',
    `vip_expire_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'VIP过期时间',
    `im_token` VARCHAR(255) DEFAULT NULL COMMENT 'IM Token（系统自动生成）',
    `last_login_at` TIMESTAMP NULL DEFAULT NULL COMMENT '最后登录时间',
    `last_login_ip` VARCHAR(50) DEFAULT NULL COMMENT '最后登录IP',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT '软删除时间',
    INDEX `idx_phone` (`phone`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户基础信息表';
```

### 3.2 用户详细资料表 (user_profiles)

```sql
CREATE TABLE `user_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `bio` TEXT DEFAULT NULL COMMENT '个性签名',
    `love_declaration` TEXT DEFAULT NULL COMMENT '愛情宣言',
    `location` VARCHAR(100) DEFAULT NULL COMMENT '地區',
    `height` INT DEFAULT NULL COMMENT '身高(cm)',
    `weight` INT DEFAULT NULL COMMENT '體重(kg)',
    `body_type` VARCHAR(50) DEFAULT NULL COMMENT '體型: slim, average, athletic, curvy等',
    `blood_type` VARCHAR(10) DEFAULT NULL COMMENT '血型: A, B, AB, O',
    `zodiac` VARCHAR(50) DEFAULT NULL COMMENT '星座',
    `marital_status` VARCHAR(50) DEFAULT NULL COMMENT '婚姻狀況: single, divorced, widowed等',
    `have_children` TINYINT DEFAULT 0 COMMENT '是否有小孩: 0無, 1有',
    `want_children` TINYINT DEFAULT NULL COMMENT '是否想要小孩: 0否, 1是, 2不確定',
    `smoking` TINYINT DEFAULT NULL COMMENT '吸煙習慣: 0不吸, 1偶爾, 2經常',
    `drinking` TINYINT DEFAULT NULL COMMENT '飲酒習慣: 0不喝, 1偶爾, 2經常',
    `religion` VARCHAR(50) DEFAULT NULL COMMENT '宗教信仰',
    `personality` VARCHAR(255) DEFAULT NULL COMMENT '性格特點: 逗號分隔的多選',
    `hobbies` VARCHAR(255) DEFAULT NULL COMMENT '興趣愛好: 逗號分隔的多選',
    `favorite_food` VARCHAR(255) DEFAULT NULL COMMENT '喜歡的食物',
    `favorite_music` VARCHAR(255) DEFAULT NULL COMMENT '喜歡的音樂類型',
    `favorite_movies` VARCHAR(255) DEFAULT NULL COMMENT '喜歡的電影類型',
    `sports` VARCHAR(255) DEFAULT NULL COMMENT '喜歡的運動',
    `travel_places` VARCHAR(255) DEFAULT NULL COMMENT '想去的地方',
    `looking_for` VARCHAR(50) DEFAULT NULL COMMENT '尋找對象類型: serious, casual, friends等',
    `ideal_partner` TEXT DEFAULT NULL COMMENT '理想對象描述',
    `language` VARCHAR(20) DEFAULT 'zh-TW' COMMENT '語言偏好',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_id` (`user_id`),
    INDEX `idx_location` (`location`),
    INDEX `idx_marital_status` (`marital_status`),
    INDEX `idx_looking_for` (`looking_for`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户详细资料表';
```

### 3.3 用户照片表 (user_photos)

```sql
CREATE TABLE `user_photos` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `url` VARCHAR(255) NOT NULL COMMENT '照片URL',
    `blur_url` VARCHAR(255) DEFAULT NULL COMMENT '模糊预览图URL（付费照片使用）',
    `sort_order` INT DEFAULT 0 COMMENT '排序',
    `is_main` TINYINT DEFAULT 0 COMMENT '是否主照片:0否,1是',
    `is_premium` TINYINT DEFAULT 0 COMMENT '是否付费照片:0否,1是',
    `points_price` INT UNSIGNED DEFAULT 0 COMMENT '积分价格（付费照片有效）',
    `purchase_count` INT UNSIGNED DEFAULT 0 COMMENT '被购买次数',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0审核中,1正常,2拒绝',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_main` (`is_main`),
    INDEX `idx_is_premium` (`is_premium`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户照片表';
```

### 3.4 用户兴趣爱好表 (user_interests)

```sql
CREATE TABLE `user_interests` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `interest` VARCHAR(50) NOT NULL COMMENT '兴趣标签',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_interest` (`user_id`, `interest`),
    INDEX `idx_interest` (`interest`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户兴趣爱好表';
```

### 3.5 喜欢记录表 (likes)

```sql
CREATE TABLE `likes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `from_user_id` BIGINT UNSIGNED NOT NULL COMMENT '发起者ID',
    `to_user_id` BIGINT UNSIGNED NOT NULL COMMENT '被喜欢者ID',
    `type` TINYINT DEFAULT 1 COMMENT '类型:1普通喜欢,2超级喜欢',
    `is_match` TINYINT DEFAULT 0 COMMENT '是否匹配:0否,1是',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_from_to` (`from_user_id`, `to_user_id`),
    INDEX `idx_to_user` (`to_user_id`),
    INDEX `idx_is_match` (`is_match`),
    FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='喜欢记录表';
```

### 3.6 匹配关系表 (matches)

```sql
CREATE TABLE `matches` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id_1` BIGINT UNSIGNED NOT NULL COMMENT '用户1ID',
    `user_id_2` BIGINT UNSIGNED NOT NULL COMMENT '用户2ID',
    `status` TINYINT DEFAULT 1 COMMENT '状态:1正常,0解除',
    `matched_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '匹配时间',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_users` (`user_id_1`, `user_id_2`),
    INDEX `idx_user2` (`user_id_2`),
    FOREIGN KEY (`user_id_1`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id_2`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='匹配关系表';
```

### 3.7 好友关系表 (friendships)

```sql
CREATE TABLE `friendships` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `friend_id` BIGINT UNSIGNED NOT NULL COMMENT '好友ID',
    `remark` VARCHAR(50) DEFAULT NULL COMMENT '备注名',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0删除,1正常',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_friend` (`user_id`, `friend_id`),
    INDEX `idx_friend` (`friend_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='好友关系表';
```

### 3.8 黑名单表 (blocks)

```sql
CREATE TABLE `blocks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `blocked_user_id` BIGINT UNSIGNED NOT NULL COMMENT '被拉黑用户ID',
    `reason` VARCHAR(255) DEFAULT NULL COMMENT '拉黑原因',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_blocked` (`user_id`, `blocked_user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`blocked_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='黑名单表';
```

### 3.9 用户积分表 (user_points)

```sql
CREATE TABLE `user_points` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `balance` INT UNSIGNED DEFAULT 0 COMMENT '积分余额',
    `total_earned` INT UNSIGNED DEFAULT 0 COMMENT '累计获得',
    `total_spent` INT UNSIGNED DEFAULT 0 COMMENT '累计消费',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户积分表';
```

### 3.10 积分交易记录表 (point_transactions)

```sql
CREATE TABLE `point_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `type` TINYINT NOT NULL COMMENT '类型:1收入,2支出',
    `amount` INT NOT NULL COMMENT '金额',
    `balance_after` INT NOT NULL COMMENT '交易后余额',
    `source` VARCHAR(50) NOT NULL COMMENT '来源:signin,task,gift,purchase等',
    `source_id` BIGINT UNSIGNED DEFAULT NULL COMMENT '来源ID',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_source` (`source`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='积分交易记录表';
```

### 3.11 会员等级配置表 (membership_levels)

```sql
CREATE TABLE `membership_levels` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL COMMENT '等级名称',
    `name_en` VARCHAR(50) DEFAULT NULL COMMENT '英文名称',
    `level` TINYINT NOT NULL COMMENT '等级值',
    `price_monthly` DECIMAL(10, 2) NOT NULL COMMENT '月费',
    `price_quarterly` DECIMAL(10, 2) DEFAULT NULL COMMENT '季费',
    `price_yearly` DECIMAL(10, 2) DEFAULT NULL COMMENT '年费',
    `daily_likes` INT DEFAULT 10 COMMENT '每日喜欢次数',
    `daily_super_likes` INT DEFAULT 0 COMMENT '每日超级喜欢次数',
    `daily_points` INT DEFAULT 0 COMMENT '每日赠送积分',
    `can_see_who_liked` TINYINT DEFAULT 0 COMMENT '查看谁喜欢我:0否,1是',
    `can_hide_profile` TINYINT DEFAULT 0 COMMENT '隐身模式:0否,1是',
    `can_recall_message` TINYINT DEFAULT 0 COMMENT '消息撤回:0否,1是',
    `priority_in_search` TINYINT DEFAULT 0 COMMENT '搜索优先:0否,1是',
    `badge_url` VARCHAR(255) DEFAULT NULL COMMENT '徽章图标',
    `avatar_frame_url` VARCHAR(255) DEFAULT NULL COMMENT '头像框图标',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0禁用,1启用',
    `sort_order` INT DEFAULT 0 COMMENT '排序',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_level` (`level`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员等级配置表';
```

### 3.12 用户会员信息表 (memberships)

```sql
CREATE TABLE `memberships` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL COMMENT '用户ID',
    `level_id` INT UNSIGNED NOT NULL COMMENT '等级ID',
    `started_at` TIMESTAMP NOT NULL COMMENT '开始时间',
    `expires_at` TIMESTAMP NOT NULL COMMENT '过期时间',
    `is_auto_renew` TINYINT DEFAULT 0 COMMENT '自动续费:0否,1是',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_user_id` (`user_id`),
    INDEX `idx_expires_at` (`expires_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`level_id`) REFERENCES `membership_levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户会员信息表';
```

### 3.13 礼品分类表 (gift_categories)

```sql
CREATE TABLE `gift_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
    `name_en` VARCHAR(50) DEFAULT NULL COMMENT '英文名称',
    `icon` VARCHAR(255) DEFAULT NULL COMMENT '图标',
    `sort_order` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0禁用,1启用',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='礼品分类表';
```

### 3.14 礼品信息表 (gifts)

```sql
CREATE TABLE `gifts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED DEFAULT NULL COMMENT '分类ID',
    `name` VARCHAR(100) NOT NULL COMMENT '礼品名称',
    `name_en` VARCHAR(100) DEFAULT NULL COMMENT '英文名称',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
    `icon` VARCHAR(255) NOT NULL COMMENT '图标',
    `animation_url` VARCHAR(255) DEFAULT NULL COMMENT '动画文件URL',
    `sound_url` VARCHAR(255) DEFAULT NULL COMMENT '音效URL',
    `points` INT UNSIGNED NOT NULL COMMENT '所需积分',
    `sort_order` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0禁用,1启用',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category_id` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_points` (`points`),
    FOREIGN KEY (`category_id`) REFERENCES `gift_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='礼品信息表';
```

### 3.15 礼品交易记录表 (gift_transactions)

```sql
CREATE TABLE `gift_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `gift_id` INT UNSIGNED NOT NULL COMMENT '礼品ID',
    `from_user_id` BIGINT UNSIGNED NOT NULL COMMENT '赠送者ID',
    `to_user_id` BIGINT UNSIGNED NOT NULL COMMENT '接收者ID',
    `points` INT UNSIGNED NOT NULL COMMENT '消耗积分',
    `message` VARCHAR(255) DEFAULT NULL COMMENT '留言',
    `is_converted` TINYINT DEFAULT 0 COMMENT '是否已兑换积分:0否,1是',
    `converted_points` INT UNSIGNED DEFAULT 0 COMMENT '兑换积分数量',
    `converted_at` TIMESTAMP NULL DEFAULT NULL COMMENT '兑换时间',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_gift_id` (`gift_id`),
    INDEX `idx_from_user` (`from_user_id`),
    INDEX `idx_to_user` (`to_user_id`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`gift_id`) REFERENCES `gifts` (`id`),
    FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='礼品交易记录表';

### 3.16 照片购买记录表 (photo_purchases)

```sql
CREATE TABLE `photo_purchases` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `photo_id` BIGINT UNSIGNED NOT NULL COMMENT '照片ID',
    `owner_id` BIGINT UNSIGNED NOT NULL COMMENT '照片所有者ID',
    `buyer_id` BIGINT UNSIGNED NOT NULL COMMENT '购买者ID',
    `points_price` INT UNSIGNED NOT NULL COMMENT '购买价格（积分）',
    `platform_fee` INT UNSIGNED DEFAULT 0 COMMENT '平台抽成（积分）',
    `owner_earned` INT UNSIGNED NOT NULL COMMENT '所有者实际获得（积分）',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_photo_id` (`photo_id`),
    INDEX `idx_owner_id` (`owner_id`),
    INDEX `idx_buyer_id` (`buyer_id`),
    INDEX `idx_created_at` (`created_at`),
    UNIQUE KEY `idx_buyer_photo` (`buyer_id`, `photo_id`),
    FOREIGN KEY (`photo_id`) REFERENCES `user_photos` (`id`),
    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='照片购买记录表';
```

### 3.17 后台管理员表 (admin_users)

```sql
CREATE TABLE `admin_users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码哈希',
    `email` VARCHAR(100) DEFAULT NULL COMMENT '邮箱',
    `role` VARCHAR(20) DEFAULT 'admin' COMMENT '角色:admin,editor',
    `status` TINYINT DEFAULT 1 COMMENT '状态:0禁用,1启用',
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_ip` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台管理员表';
```

### 3.17 系统设置表 (system_settings)

```sql
CREATE TABLE `system_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE COMMENT '设置键',
    `value` TEXT DEFAULT NULL COMMENT '设置值',
    `type` VARCHAR(20) DEFAULT 'string' COMMENT '值类型:string,int,bool,json',
    `description` VARCHAR(255) DEFAULT NULL COMMENT '描述',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统设置表';
```

### 3.18 操作日志表 (logs)

```sql
CREATE TABLE `logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED DEFAULT NULL COMMENT '用户ID',
    `admin_id` INT UNSIGNED DEFAULT NULL COMMENT '管理员ID',
    `action` VARCHAR(100) NOT NULL COMMENT '操作类型',
    `description` TEXT DEFAULT NULL COMMENT '操作描述',
    `ip_address` VARCHAR(50) DEFAULT NULL COMMENT 'IP地址',
    `user_agent` VARCHAR(255) DEFAULT NULL COMMENT '用户代理',
    `data` JSON DEFAULT NULL COMMENT '操作数据',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志表';
```

## 4. 初始化数据

### 4.1 会员等级初始化

```sql
INSERT INTO `membership_levels` (`name`, `name_en`, `level`, `price_monthly`, `price_quarterly`, `price_yearly`, `daily_likes`, `daily_super_likes`, `daily_points`, `can_see_who_liked`, `can_hide_profile`, `can_recall_message`, `priority_in_search`, `sort_order`) VALUES
('普通會員', 'Free', 0, 0.00, NULL, NULL, 10, 0, 0, 0, 0, 0, 0, 0),
('銀卡會員', 'Silver', 1, 30.00, 80.00, 280.00, 50, 0, 100, 1, 0, 0, 0, 1),
('金卡會員', 'Gold', 2, 68.00, 180.00, 600.00, 9999, 10, 300, 1, 0, 1, 1, 2),
('鑽石會員', 'Diamond', 3, 128.00, 340.00, 1100.00, 9999, 50, 800, 1, 1, 1, 1, 3);
```

### 4.2 礼品分类初始化

```sql
INSERT INTO `gift_categories` (`name`, `name_en`, `sort_order`) VALUES
('熱門', 'Hot', 1),
('新品', 'New', 2),
('專屬', 'Exclusive', 3),
('愛心', 'Love', 4);
```

### 4.3 系统设置初始化

```sql
INSERT INTO `system_settings` (`key`, `value`, `type`, `description`) VALUES
('site_name', 'ChatMeGo', 'string', '网站名称'),
('site_logo', '', 'string', '网站Logo'),
('default_language', 'zh-TW', 'string', '默认语言'),
('signup_points', '100', 'int', '注册赠送积分'),
('daily_signin_points', '10', 'int', '每日签到积分'),
('gift_convert_rate', '0.5', 'float', '礼品兑换积分比例'),
('im_service', 'easemob', 'string', 'IM服务商:easemob,rongcloud'),
('max_photos', '6', 'int', '用户最大照片数量'),
('login_bg_images', '["bg1.jpg","bg2.jpg","bg3.jpg"]', 'json', '登录页背景图列表'),
('login_bg_overlay', 'dark', 'string', '背景遮罩层: dark, light, none'),
('login_bg_blur', '0', 'int', '背景模糊程度: 0-20'),
('login_logo_show', '1', 'int', '是否显示Logo: 0隐藏, 1显示'),
('login_logo_url', '', 'string', '登录页Logo图片URL'),
('photo_sale_enabled', '1', 'int', '是否开启照片售卖: 0关闭, 1开启'),
('photo_sale_platform_fee_rate', '0.1', 'float', '平台抽成比例: 0-1之间'),
('photo_sale_min_price', '10', 'int', '照片最低售价（积分）'),
('photo_sale_max_price', '1000', 'int', '照片最高售价（积分）');
```

## 5. 数据库优化建议

### 5.1 索引优化
- 所有外键字段都建立了索引
- 常用查询字段建立了索引
- 避免过多索引影响写入性能

### 5.2 分区建议
- `point_transactions` 表可以按时间分区
- `gift_transactions` 表可以按时间分区
- `logs` 表可以按时间分区

### 5.3 定期维护
- 定期清理过期日志
- 定期优化表
- 监控慢查询

## 6. 备份策略

### 6.1 自动备份
- 每日全量备份
- 保留最近7天备份
- 保留每月1号备份

### 6.2 手动备份
- 重大更新前手动备份
- 导出SQL文件保存到本地

## 7. 安全考虑

### 7.1 数据加密
- 密码使用 bcrypt 加密
- 敏感信息加密存储

### 7.2 权限控制
- 不同角色访问不同数据
- SQL注入防护（使用预处理语句）

### 7.3 审计日志
- 记录关键操作
- 定期审查日志

## 8. IM账号生成规则

### 8.1 账号格式
- **IM账号**: `chatmego_{user_id}`
  - 示例: `chatmego_10001`
  - 使用用户ID作为唯一标识，确保不重复
  
- **IM密码**: 系统自动生成的随机字符串
  - 存储在 `users.im_token` 字段
  - 长度: 32位随机字符
  - 定期更换（可选）

### 8.2 用户无感知设计
- 用户注册时，系统自动创建IM账号
- 用户登录时，后台返回IM账号和密码
- 前端自动连接IM服务器，用户无感知
- 界面完全自定义，不显示第三方IM品牌

### 8.3 账号管理
- 用户禁用: 同步禁用IM账号
- 用户删除: 保留IM账号（用于消息历史）
- 密码重置: 重新生成IM Token
