# CMG 项目重要信息记录

## 🔗 Git 仓库信息
- **仓库地址**: `git@github.com:visiis/chatmego.git`
- **推送方式**: SSH (需要配置SSH密钥)
- **分支**: main

## 🖥️ 服务器信息
- **服务器地址**: bhr.41c.mytemp.website
- **用户名**: b3kxi5lpp7kr
- **SSH密钥**: ~/.ssh/id_ed25519
- **项目路径**: ~/public_html/
- **本地端口**: 8888
- **数据库连接**: 通过SSH隧道连接服务器数据库

## 📁 重要文件路径

### 本地开发
- 项目根目录: /Volumes/MyWork/APP/ChatMeGo
- 前端视图: resources/views/
- 控制器: app/Http/Controllers/
- 语言文件: resources/lang/
- 静态资源: public/images/

### 服务器部署
- 视图文件: ~/public_html/resources/views/
- 控制器: ~/public_html/app/Http/Controllers/
- 语言文件: ~/public_html/resources/lang/

## 🔄 常用命令

### 上传到服务器
```bash
scp -i ~/.ssh/id_ed25519 /Volumes/MyWork/APP/ChatMeGo/resources/views/profile.blade.php b3kxi5lpp7kr@bhr.41c.mytemp.website:~/public_html/resources/views/
```

### 清除服务器缓存
```bash
ssh -i ~/.ssh/id_ed25519 b3kxi5lpp7kr@bhr.41c.mytemp.website "cd ~/public_html && php artisan view:clear && php artisan cache:clear"
```

### Git推送
```bash
cd /Volumes/MyWork/APP/ChatMeGo
git remote set-url origin git@github.com:visiis/chatmego.git
git push -u origin main
```

## 📝 项目功能清单

### 已实现功能
1. ✅ 多语言支持 (中文繁体/简体/英文)
2. ✅ 管理员权限系统
3. ✅ 用户头像上传和显示
4. ✅ 交友列表页面
5. ✅ 个人资料修改 (带验证)
6. ✅ 设置页面 (语言切换)
7. ✅ 默认头像显示

### 验证规则
- 年龄: min:18, max:120
- 身高: min:100, max:250 (cm)
- 体重: min:30, max:300 (kg)

## ⚠️ 注意事项
1. 服务器PHP版本: 8.3
2. 本地PHP版本: 8.2
3. 数据库通过SSH隧道连接
4. 默认语言: 中文繁体
5. 本地开发端口: 8888

## 🔑 关键配置
- Filament后台管理已配置
- 用户模型有is_admin字段
- 头像存储路径: storage/app/public/avatars/
- 默认头像: public/images/default-avatar.svg
