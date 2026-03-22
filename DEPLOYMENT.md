# 部署说明

## ⚠️ 重要：部署流程（必须严格遵守）

### 正确的部署步骤

**1. 本地修改和测试**
```bash
# 在本地修改文件
# 测试修改是否正确

# 提交到 Git
git add -A
git commit -m "修改说明"
```

**2. 上传文件到服务器（使用 SCP，不是 Git）**
```bash
# 上传修改的文件到服务器
scp -i ~/.ssh/id_ed25519 /path/to/modified/file.php b3kxi5lpp7kr@bhr.41c.mytemp.website:/home/b3kxi5lpp7kr/public_html/path/to/file.php

# 在服务器上清除缓存
ssh -i ~/.ssh/id_ed25519 b3kxi5lpp7kr@bhr.41c.mytemp.website "cd /home/b3kxi5lpp7kr/public_html && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear"
```

**3. Git 推送（只推送，永远不要拉取）**
```bash
# 推送到远程仓库
git push origin main
```

### ⛔ 禁止操作

- **永远不要**在服务器上执行 `git pull` 或 `git reset`
- **永远不要**用 Git 覆盖服务器上的文件
- **永远不要**在没经过用户同意时拉取远程代码

### ✅ 正确流程总结

```
本地修改 → 上传服务器 (SCP) → 清除缓存 → Git 推送
```

---

## 环境配置文件管理

### 本地开发环境
- **配置文件**: `.env`（已在 .gitignore 中，不会提交到 Git）
- **配置模板**: `.env.example`（已提交到 Git，作为参考模板）

### 服务器生产环境
- **配置文件**: `.env`（在服务器上手动创建和配置）
- **不要修改**: 服务器上的 `.env` 文件不应该被本地代码覆盖

### 3. 环境差异说明

#### 本地环境 (.env) - MAMP 配置
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=chatmego_db
DB_USERNAME=root
DB_PASSWORD=root
```

> 注意：本地数据库端口取决于您的开发环境：
> - MAMP 默认：8889
> - 原生 MySQL：3306
> - Docker：根据配置而定

#### 服务器环境 (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=chatmego_db
DB_USERNAME=chatmego_user
DB_PASSWORD=vIpqok-1torqi-geqnag
```

## 重要提醒

⚠️ **永远不要**将 `.env` 文件提交到 Git！
- `.env` 文件已添加到 `.gitignore`
- 敏感信息（密码、密钥等）应该只在服务器上配置
- 使用 `.env.example` 作为配置参考模板

## 同步代码时的注意事项

1. **拉取代码前**：确保服务器上的 `.env` 文件已备份
2. **拉取代码后**：检查 `.env.example` 是否有新增配置项
3. **如有新增配置**：手动更新服务器上的 `.env` 文件
4. **清除缓存**：运行 `php artisan config:clear` 等命令

## 数据库管理

### 导出远程数据库到本地
```bash
# 通过 SSH 通道导出
ssh -i ~/.ssh/id_ed25519 user@server "mysqldump -u username -p'password' database_name" > database/backup.sql

# 导入到本地
mysql -u root -p database_name < database/backup.sql
```

### 本地测试数据库迁移
```bash
# 运行迁移
php artisan migrate

# 回滚迁移
php artisan migrate:rollback

# 重置所有迁移
php artisan migrate:reset
```

## 常见问题

### Q: 如何避免 .env 文件冲突？
A: `.env` 文件已在 `.gitignore` 中，不会被 Git 跟踪。每个环境（本地/服务器）都应该有自己的 `.env` 文件。

### Q: 如何知道新增了哪些配置项？
A: 定期查看 `.env.example` 文件，对比服务器上的 `.env` 文件，手动添加缺失的配置项。

### Q: 部署后网站出错怎么办？
A: 
1. 检查 `.env` 文件配置是否正确
2. 清除所有缓存：`php artisan optimize:clear`
3. 检查日志文件：`storage/logs/laravel.log`
