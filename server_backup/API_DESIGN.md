# API接口设计文档

## 1. 接口规范

### 1.1 基础信息
- **协议**: HTTPS
- **数据格式**: JSON
- **字符编码**: UTF-8
- **请求方式**: RESTful API
- **认证方式**: JWT Token

### 1.2 请求头
```
Content-Type: application/json
Authorization: Bearer {token}
Accept-Language: zh-TW
```

### 1.3 响应格式
```json
{
    "code": 200,
    "message": "success",
    "data": {}
}
```

### 1.4 状态码
- `200` - 成功
- `400` - 请求参数错误
- `401` - 未授权/Token过期
- `403` - 权限不足
- `404` - 资源不存在
- `422` - 验证失败
- `500` - 服务器内部错误

## 2. 认证相关接口

### 2.1 发送验证码
**POST** `/api/auth/send-code`

**请求参数**:
```json
{
    "phone": "+886912345678",
    "type": "register"
}
```

**响应**:
```json
{
    "code": 200,
    "message": "验证码已发送",
    "data": {
        "expire_seconds": 300
    }
}
```

### 2.2 用户注册
**POST** `/api/auth/register`

**请求参数**:
```json
{
    "phone": "+886912345678",
    "code": "123456",
    "password": "password123",
    "nickname": "小明",
    "gender": 1,
    "birthday": "1995-05-20",
    "invite_code": "ABC123"
}
```

**响应**:
```json
{
    "code": 200,
    "message": "注册成功",
    "data": {
        "user": {
            "id": 10001,
            "phone": "+886912345678",
            "nickname": "小明",
            "avatar": "",
            "gender": 1,
            "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
        },
        "im": {
            "account": "chatmego_10001",
            "token": "im_token_here"
        }
    }
}
```

### 2.3 用户登录
**POST** `/api/auth/login`

**请求参数**:
```json
{
    "phone": "+886912345678",
    "password": "password123"
}
```

**响应**:
```json
{
    "code": 200,
    "message": "登录成功",
    "data": {
        "user": {
            "id": 10001,
            "phone": "+886912345678",
            "nickname": "小明",
            "avatar": "https://...",
            "gender": 1,
            "is_vip": 1,
            "vip_level": 2,
            "vip_expire_at": "2025-12-31",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
        },
        "im": {
            "account": "chatmego_10001",
            "token": "im_token_here"
        }
    }
}
```

### 2.4 验证码登录
**POST** `/api/auth/login-by-code`

**请求参数**:
```json
{
    "phone": "+886912345678",
    "code": "123456"
}
```

**响应**: 同密码登录

### 2.5 刷新Token
**POST** `/api/auth/refresh`

**请求头**: `Authorization: Bearer {refresh_token}`

**响应**:
```json
{
    "code": 200,
    "message": "刷新成功",
    "data": {
        "token": "new_token_here",
        "refresh_token": "new_refresh_token_here",
        "expire_at": "2024-12-31T23:59:59Z"
    }
}
```

## 3. 用户相关接口

### 3.1 获取当前用户信息
**GET** `/api/user/profile`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "id": 10001,
        "phone": "+886912345678",
        "nickname": "小明",
        "avatar": "https://...",
        "gender": 1,
        "birthday": "1995-05-20",
        "bio": "个性签名",
        "love_declaration": "爱情宣言",
        "location": "台北市",
        "height": 175,
        "weight": 70,
        "body_type": "average",
        "blood_type": "A",
        "zodiac": "金牛座",
        "marital_status": "single",
        "have_children": 0,
        "want_children": 1,
        "smoking": 0,
        "drinking": 1,
        "religion": "无",
        "personality": "开朗,幽默,善良",
        "hobbies": "旅游,摄影,美食",
        "favorite_food": "日式料理,火锅",
        "favorite_music": "流行,摇滚",
        "favorite_movies": "科幻,爱情",
        "sports": "篮球,游泳",
        "travel_places": "日本,欧洲",
        "looking_for": "serious",
        "ideal_partner": "温柔体贴，有共同爱好",
        "is_vip": 1,
        "vip_level": 2,
        "vip_expire_at": "2025-12-31",
        "points": {
            "balance": 500,
            "total_earned": 1000,
            "total_spent": 500
        },
        "photos": [
            {
                "id": 1,
                "url": "https://...",
                "is_main": 1,
                "is_premium": 0,
                "points_price": 0
            },
            {
                "id": 2,
                "url": "https://...",
                "blur_url": "https://...",
                "is_main": 0,
                "is_premium": 1,
                "points_price": 50
            }
        ],
        "interests": ["旅游", "摄影", "美食"]
    }
}
```

### 3.2 更新用户资料
**PUT** `/api/user/profile`

**请求参数**:
```json
{
    "nickname": "小明",
    "avatar": "https://...",
    "gender": 1,
    "birthday": "1995-05-20",
    "bio": "个性签名",
    "love_declaration": "爱情宣言",
    "location": "台北市",
    "height": 175,
    "weight": 70,
    "body_type": "average",
    "blood_type": "A",
    "zodiac": "金牛座",
    "marital_status": "single",
    "have_children": 0,
    "want_children": 1,
    "smoking": 0,
    "drinking": 1,
    "religion": "无",
    "personality": "开朗,幽默,善良",
    "hobbies": "旅游,摄影,美食",
    "favorite_food": "日式料理,火锅",
    "favorite_music": "流行,摇滚",
    "favorite_movies": "科幻,爱情",
    "sports": "篮球,游泳",
    "travel_places": "日本,欧洲",
    "looking_for": "serious",
    "ideal_partner": "温柔体贴，有共同爱好"
}
```

### 3.3 获取其他用户资料
**GET** `/api/user/{user_id}/profile`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "id": 10002,
        "nickname": "小红",
        "avatar": "https://...",
        "gender": 2,
        "age": 26,
        "bio": "个性签名",
        "love_declaration": "爱情宣言",
        "location": "台北市",
        "distance": "3km",
        "height": 165,
        "weight": 50,
        "body_type": "slim",
        "zodiac": "处女座",
        "marital_status": "single",
        "have_children": 0,
        "personality": "温柔,善良",
        "hobbies": "阅读,音乐",
        "looking_for": "serious",
        "ideal_partner": "成熟稳重",
        "is_vip": 1,
        "vip_level": 1,
        "photos": [
            {
                "id": 1,
                "url": "https://...",
                "is_main": 1,
                "is_premium": 0,
                "points_price": 0,
                "is_unlocked": true
            },
            {
                "id": 2,
                "url": "https://...",
                "blur_url": "https://...",
                "is_main": 0,
                "is_premium": 1,
                "points_price": 50,
                "is_unlocked": false
            }
        ],
        "interests": ["阅读", "音乐"],
        "is_liked": false,
        "is_matched": false
    }
}
```

## 4. 照片相关接口

### 4.1 上传照片
**POST** `/api/user/photos`

**请求参数** (multipart/form-data):
```
photo: [文件]
is_main: 0
is_premium: 1
points_price: 50
```

**响应**:
```json
{
    "code": 200,
    "message": "上传成功",
    "data": {
        "id": 3,
        "url": "https://...",
        "blur_url": "https://...",
        "is_main": 0,
        "is_premium": 1,
        "points_price": 50,
        "status": 0
    }
}
```

### 4.2 获取用户照片列表
**GET** `/api/user/photos`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "photos": [
            {
                "id": 1,
                "url": "https://...",
                "is_main": 1,
                "is_premium": 0,
                "points_price": 0,
                "purchase_count": 0,
                "status": 1
            },
            {
                "id": 2,
                "url": "https://...",
                "blur_url": "https://...",
                "is_main": 0,
                "is_premium": 1,
                "points_price": 50,
                "purchase_count": 10,
                "status": 1
            }
        ]
    }
}
```

### 4.3 删除照片
**DELETE** `/api/user/photos/{photo_id}`

**响应**:
```json
{
    "code": 200,
    "message": "删除成功"
}
```

### 4.4 设置主照片
**PUT** `/api/user/photos/{photo_id}/set-main`

**响应**:
```json
{
    "code": 200,
    "message": "设置成功"
}
```

### 4.5 购买照片
**POST** `/api/photos/{photo_id}/purchase`

**响应**:
```json
{
    "code": 200,
    "message": "购买成功",
    "data": {
        "photo": {
            "id": 2,
            "url": "https://...",
            "is_unlocked": true
        },
        "points_deducted": 50,
        "balance_after": 450
    }
}
```

### 4.6 获取购买记录
**GET** `/api/user/photo-purchases`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "purchases": [
            {
                "id": 1,
                "photo": {
                    "id": 2,
                    "url": "https://...",
                    "owner": {
                        "id": 10002,
                        "nickname": "小红",
                        "avatar": "https://..."
                    }
                },
                "points_price": 50,
                "created_at": "2024-01-15T10:30:00Z"
            }
        ]
    }
}
```

## 5. 匹配相关接口

### 5.1 获取推荐用户列表
**GET** `/api/discover/users`

**请求参数**:
```
page: 1
per_page: 10
min_age: 20
max_age: 30
gender: 2
location: 台北市
```

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "users": [
            {
                "id": 10002,
                "nickname": "小红",
                "avatar": "https://...",
                "age": 26,
                "gender": 2,
                "location": "台北市",
                "distance": "3km",
                "bio": "个性签名",
                "main_photo": "https://...",
                "photo_count": 5,
                "premium_photo_count": 2,
                "interests": ["阅读", "音乐"],
                "is_vip": 1
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 10,
            "total": 100,
            "last_page": 10
        }
    }
}
```

### 5.2 喜欢用户
**POST** `/api/users/{user_id}/like`

**响应**:
```json
{
    "code": 200,
    "message": "喜欢成功",
    "data": {
        "is_match": true,
        "match": {
            "id": 1,
            "user": {
                "id": 10002,
                "nickname": "小红",
                "avatar": "https://..."
            }
        }
    }
}
```

### 5.3 不喜欢用户
**POST** `/api/users/{user_id}/dislike`

**响应**:
```json
{
    "code": 200,
    "message": "已标记为不喜欢"
}
```

### 5.4 获取匹配列表
**GET** `/api/matches`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "matches": [
            {
                "id": 1,
                "user": {
                    "id": 10002,
                    "nickname": "小红",
                    "avatar": "https://...",
                    "is_online": true
                },
                "matched_at": "2024-01-15T10:30:00Z",
                "last_message": {
                    "content": "你好",
                    "created_at": "2024-01-15T10:35:00Z"
                }
            }
        ]
    }
}
```

### 5.5 获取谁喜欢我
**GET** `/api/likes/received`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "users": [
            {
                "id": 10003,
                "nickname": "小花",
                "avatar": "https://...",
                "age": 25,
                "location": "台北市",
                "created_at": "2024-01-15T10:30:00Z"
            }
        ]
    }
}
```

## 6. 积分相关接口

### 6.1 获取积分信息
**GET** `/api/user/points`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "balance": 500,
        "total_earned": 1000,
        "total_spent": 500
    }
}
```

### 6.2 获取积分明细
**GET** `/api/user/point-transactions`

**请求参数**:
```
page: 1
per_page: 20
type: income
```

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "transactions": [
            {
                "id": 1,
                "type": "income",
                "amount": 100,
                "balance_after": 500,
                "source": "signin",
                "description": "每日签到",
                "created_at": "2024-01-15T10:30:00Z"
            },
            {
                "id": 2,
                "type": "expense",
                "amount": -50,
                "balance_after": 400,
                "source": "photo_purchase",
                "description": "购买照片",
                "created_at": "2024-01-15T11:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total": 50
        }
    }
}
```

### 6.3 每日签到
**POST** `/api/user/check-in`

**响应**:
```json
{
    "code": 200,
    "message": "签到成功",
    "data": {
        "points_earned": 10,
        "consecutive_days": 5,
        "bonus_points": 5,
        "balance_after": 515
    }
}
```

## 7. 会员相关接口

### 7.1 获取会员等级列表
**GET** `/api/membership-levels`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "levels": [
            {
                "id": 1,
                "name": "銀卡會員",
                "name_en": "Silver",
                "level": 1,
                "price_monthly": 30,
                "price_quarterly": 80,
                "price_yearly": 280,
                "daily_likes": 50,
                "daily_super_likes": 0,
                "daily_points": 100,
                "can_see_who_liked": true,
                "badge_url": "https://...",
                "avatar_frame_url": "https://..."
            }
        ]
    }
}
```

### 7.2 获取当前会员信息
**GET** `/api/user/membership`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "level": {
            "id": 2,
            "name": "金卡會員",
            "name_en": "Gold",
            "level": 2,
            "badge_url": "https://...",
            "avatar_frame_url": "https://..."
        },
        "started_at": "2024-01-01T00:00:00Z",
        "expires_at": "2024-12-31T23:59:59Z",
        "is_auto_renew": true,
        "remaining_days": 350
    }
}
```

### 7.3 购买会员
**POST** `/api/membership/purchase`

**请求参数**:
```json
{
    "level_id": 2,
    "duration": "yearly",
    "payment_method": "wechat"
}
```

**响应**:
```json
{
    "code": 200,
    "message": "创建订单成功",
    "data": {
        "order_id": "ORDER123456",
        "payment_info": {
            "app_id": "wx123456",
            "prepay_id": "prepay_123456",
            "nonce_str": "random_string",
            "timestamp": "1234567890",
            "sign": "signature"
        }
    }
}
```

## 8. 礼品相关接口

### 8.1 获取礼品列表
**GET** `/api/gifts`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "categories": [
            {
                "id": 1,
                "name": "熱門",
                "gifts": [
                    {
                        "id": 1,
                        "name": "玫瑰花",
                        "icon": "https://...",
                        "animation_url": "https://...",
                        "points": 50
                    }
                ]
            }
        ]
    }
}
```

### 8.2 赠送礼品
**POST** `/api/users/{user_id}/send-gift`

**请求参数**:
```json
{
    "gift_id": 1,
    "message": "送你一朵花"
}
```

**响应**:
```json
{
    "code": 200,
    "message": "赠送成功",
    "data": {
        "gift_transaction_id": 1,
        "points_deducted": 50,
        "balance_after": 450
    }
}
```

### 8.3 获取收到的礼品
**GET** `/api/user/gifts-received`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "gifts": [
            {
                "id": 1,
                "gift": {
                    "id": 1,
                    "name": "玫瑰花",
                    "icon": "https://..."
                },
                "from_user": {
                    "id": 10002,
                    "nickname": "小红",
                    "avatar": "https://..."
                },
                "message": "送你一朵花",
                "points_value": 50,
                "is_converted": false,
                "created_at": "2024-01-15T10:30:00Z"
            }
        ]
    }
}
```

### 8.4 兑换礼品为积分
**POST** `/api/gifts/{gift_id}/convert`

**响应**:
```json
{
    "code": 200,
    "message": "兑换成功",
    "data": {
        "points_earned": 25,
        "balance_after": 475
    }
}
```

## 9. 系统相关接口

### 9.1 获取系统配置
**GET** `/api/system/config`

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "site_name": "ChatMeGo",
        "site_logo": "https://...",
        "default_language": "zh-TW",
        "signup_points": 100,
        "daily_signin_points": 10,
        "gift_convert_rate": 0.5,
        "photo_sale_enabled": true,
        "photo_sale_min_price": 10,
        "photo_sale_max_price": 1000,
        "login_bg_images": ["bg1.jpg", "bg2.jpg", "bg3.jpg"],
        "login_bg_overlay": "dark"
    }
}
```

### 9.2 上传文件
**POST** `/api/upload`

**请求参数** (multipart/form-data):
```
file: [文件]
type: avatar|photo|gift
```

**响应**:
```json
{
    "code": 200,
    "message": "上传成功",
    "data": {
        "url": "https://...",
        "blur_url": "https://..."
    }
}
```

## 10. 环信IM相关接口

### 10.1 刷新环信Token
**POST** `/api/im/refresh-token`

**响应**:
```json
{
    "code": 200,
    "message": "刷新成功",
    "data": {
        "im": {
            "account": "chatmego_10001",
            "token": "new_im_token_here",
            "expire_at": "2024-12-31T23:59:59Z"
        }
    }
}
```

### 10.2 获取环信消息历史
**GET** `/api/im/messages`

**请求参数**:
```
from: 10002
limit: 20
before: 1609459200000
```

**响应**:
```json
{
    "code": 200,
    "message": "success",
    "data": {
        "messages": [
            {
                "id": "msg_123",
                "from": 10002,
                "to": 10001,
                "content": "你好",
                "type": "text",
                "created_at": "2024-01-01T10:00:00Z",
                "is_read": true
            }
        ]
    }
}
```

### 10.3 发送消息
**POST** `/api/im/messages`

**请求参数**:
```json
{
    "to": 10002,
    "content": "你好",
    "type": "text",
    "ext": {
        "gift_id": 1,
        "gift_points": 50
    }
}
```

**响应**:
```json
{
    "code": 200,
    "message": "发送成功",
    "data": {
        "message_id": "msg_123"
    }
}
```

### 10.4 标记消息已读
**PUT** `/api/im/messages/{message_id}/read`

**响应**:
```json
{
    "code": 200,
    "message": "标记成功"
}
```

### 10.5 撤回消息
**DELETE** `/api/im/messages/{message_id}`

**响应**:
```json
{
    "code": 200,
    "message": "撤回成功"
}
```

## 11. 错误处理示例

### 11.1 参数验证错误
```json
{
    "code": 422,
    "message": "验证失败",
    "errors": {
        "phone": ["手机号格式不正确"],
        "password": ["密码不能少于6位"]
    }
}
```

### 11.2 积分不足
```json
{
    "code": 403,
    "message": "积分不足",
    "data": {
        "required": 50,
        "balance": 30
    }
}
```

### 11.3 照片已购买
```json
{
    "code": 400,
    "message": "您已经购买过这张照片"
}
```
