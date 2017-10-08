### AccountService

**Type**: Public

**Description**: GeekApk 全站账户系统，基于 JWT 实现用户身份验证。

**API**:

- `POST /api/account/login`: 登录

Params (Urlencoded):

`name`: 用户名 / 邮箱地址

`password`: 密码

`captcha`: 验证码（可选）

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型

Sets cookie:

`GEEKAPK_TOKEN`: 全站 JWT

- `POST /api/account/register`: 注册

Params (Urlencoded):

`name`: 用户名

`email`: 邮箱地址

`password`: 密码

`captcha`: 验证码（可选）

`optional`: 可选信息 (JSON-encoded map / null)

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型

- `POST /api/account/verify_email`: 验证邮箱地址

Params (Urlencoded):

`token`: 邮件验证码

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型

- `POST /api/account/info`: 获取账户信息

Params (Urlencoded): None

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型

`data`: (Object) 用户信息

-> `name`: 用户名

-> `email`: 邮箱地址

- `POST /api/account/change_password`: 修改密码

Params (Urlencoded):

`old_password`: 旧密码

`new_password`: 新密码

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型

- `POST /api/account/change_email`: 修改邮箱地址

Params (Urlencoded):

`new_email`: 新邮箱地址

Returns:

`ok`: 是否成功 (`true` / `false`)

`err`: 错误类型
