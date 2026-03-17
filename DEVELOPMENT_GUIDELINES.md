# ChatMeGo 项目开发规范文档

## ⚠️ 数据库操作红线（必须遵守）

### 绝对禁止的操作

1. **禁止使用 `migrate:fresh` 命令**
   ```bash
   ❌ 禁止：php artisan migrate:fresh
   ❌ 禁止：php artisan migrate:fresh --seed
   ❌ 禁止：php artisan migrate:refresh
   ```
   这些命令会**删除所有表和数据**！

2. **禁止使用 `DROP TABLE` 或 `TRUNCATE`**
   ```sql
   ❌ 禁止：DROP TABLE users;
   ❌ 禁止：TRUNCATE TABLE users;
   ❌ 禁止：DROP DATABASE chatmego_db;
   ```

3. **禁止删除包含生产数据的表**
   - `users` - 用户数据
   - 任何包含真实用户信息的表

### 允许的操作

1. **可以添加新数据**
   ```bash
   ✅ 允许：php artisan migrate（只运行新迁移）
   ✅ 允许：php artisan db:seed（添加测试数据）
   ✅ 允许：创建新的迁移文件
   ```

2. **可以修改表结构（使用 ALTER）**
   ```bash
   ✅ 允许：php artisan make:migration add_xxx_to_users_table
   ✅ 允许：添加新字段
   ✅ 允许：修改字段类型（确保数据兼容）
   ```

3. **可以删除测试数据**
   ```bash
   ✅ 允许：删除测试用户（非真实用户）
   ✅ 允许：删除聊天记录（测试数据）
   ✅ 允许：删除好友关系（测试数据）
   ```

## 📋 数据库操作流程

### 添加新功能时的正确流程

1. **创建新的迁移文件**
   ```bash
   php artisan make:migration create_xxx_table
   php artisan make:migration add_xxx_to_yyy_table
   ```

2. **编写迁移代码**
   ```php
   // 创建新表
   Schema::create('new_table', function (Blueprint $table) {
       $table->id();
       $table->string('name');
       $table->timestamps();
   });

   // 修改现有表
   Schema::table('existing_table', function (Blueprint $table) {
       $table->string('new_column')->nullable();
   });
   ```

3. **运行迁移**
   ```bash
   php artisan migrate
   ```

### 需要修改表结构时

1. **创建修改迁移**
   ```bash
   php artisan make:migration update_xxx_column_in_yyy_table
   ```

2. **确保向后兼容**
   - 新字段设置 `nullable()` 或默认值
   - 不要删除正在使用的字段
   - 如需删除，先确认无数据依赖

## 🔒 数据备份规范

### 本地开发环境

1. **定期备份数据库**
   ```bash
   # 导出整个数据库
   mysqldump -u root -p chatmego_db > backup_$(date +%Y%m%d).sql
   
   # 或只导出重要表
   mysqldump -u root -p chatmego_db users > users_backup.sql
   ```

2. **从服务器同步数据**
   ```bash
   # 导出服务器数据
   ssh user@server "cd /path && php artisan db:export" > backup.sql
   
   # 导入本地
   php artisan db:import backup.sql
   ```

### 服务器环境

1. **修改前必须备份**
   ```bash
   # 备份到服务器
   scp backup.sql user@server:~/backups/
   ```

2. **保留至少 3 个历史版本**

## 🚀 安全开发检查清单

### 执行数据库操作前

- [ ] 这个操作会删除数据吗？
- [ ] 是否需要先备份？
- [ ] 是否影响生产数据？
- [ ] 是否可以回滚？
- [ ] 是否通知了团队成员？

### 创建迁移文件时

- [ ] 迁移是否可逆（down 方法）？
- [ ] 是否会影响现有数据？
- [ ] 字段是否有默认值或 nullable？
- [ ] 外键约束是否正确？
- [ ] 是否添加了适当的索引？

### 测试新功能时

- [ ] 使用测试数据而非生产数据
- [ ] 在本地环境充分测试
- [ ] 确认不会影响现有功能
- [ ] 准备好回滚方案

## 📝 常见场景处理

### 场景 1：需要添加新字段

```bash
# ✅ 正确做法
php artisan make:migration add_avatar_to_users_table

# 在迁移文件中
Schema::table('users', function (Blueprint $table) {
    $table->string('avatar')->nullable()->after('email');
});

php artisan migrate
```

### 场景 2：需要修改字段类型

```bash
# ✅ 正确做法
php artisan make:migration update_age_column_in_users_table

# 确保有数据迁移方案
Schema::table('users', function (Blueprint $table) {
    $table->integer('age')->change();
});
```

### 场景 3：需要删除字段

```bash
# ✅ 正确做法
# 1. 先确认该字段无数据依赖
# 2. 备份数据
# 3. 创建迁移
php artisan make:migration remove_old_column_from_table

Schema::table('table_name', function (Blueprint $table) {
    $table->dropColumn('old_column');
});
```

### 场景 4：表结构完全错误需要重建

```bash
# ✅ 正确做法
# 1. 导出所有数据
# 2. 创建新迁移文件重命名表
# 3. 创建新表结构
# 4. 迁移数据
# 5. 删除旧表

# ❌ 绝对禁止
# php artisan migrate:fresh
```

## 🎯 开发原则

1. **数据第一** - 代码可以重写，数据无法恢复
2. **备份优先** - 任何修改前先备份
3. **渐进式更新** - 使用增量迁移而非全量替换
4. **可回滚** - 每个操作都要有回退方案
5. **团队沟通** - 重大修改前通知团队
6. **禁止擅自上传** - 没有明确指令时，不要上传任何文件到服务器

## 📤 服务器上传规范

### 禁止擅自上传的情况

- ❌ 没有收到明确的上传指令
- ❌ 本地功能还未完全测试
- ❌ 不确定修改是否会影响其他功能
- ❌ 只是为了"备份"或"同步"

### 允许上传的情况

- ✅ 用户明确要求"上传服务器"
- ✅ 用户说"可以将这次改动上传服务器"
- ✅ 修复了紧急 bug 需要立即部署
- ✅ 明确指示上传特定文件

### 上传前检查清单

在执行任何 scp/ssh 上传命令前，必须确认：

- [ ] 用户是否明确要求上传？
- [ ] 本地功能是否已测试通过？
- [ ] 是否会影响服务器正常运行？
- [ ] 是否需要先备份服务器文件？
- [ ] 上传后是否需要重启服务？

### 正确的上传流程

1. **确认指令** - 确保用户明确要求上传
2. **确认范围** - 明确需要上传哪些文件
3. **备份服务器** - 备份服务器上的原文件
4. **执行上传** - 使用 scp 上传指定文件
5. **清理缓存** - 必要时清除服务器缓存
6. **验证功能** - 确认上传后功能正常

```bash
# ✅ 正确的上传示例（仅在用户要求后执行）
scp -i ~/.ssh/id_ed25519 ./app/Http/Controllers/ChatController.php user@server:~/public_html/app/Http/Controllers/

# 清理缓存
ssh user@server "cd ~/public_html && php artisan cache:clear && php artisan view:clear"
```

## 📞 紧急联系人

如果遇到数据库问题：
1. 立即停止操作
2. 检查最近的备份
3. 联系团队成员
4. 从备份恢复

---

**最后更新**：2026-03-18  
**创建原因**：避免误操作删除数据库数据、避免擅自上传服务器  
**执行级别**：必须严格遵守
