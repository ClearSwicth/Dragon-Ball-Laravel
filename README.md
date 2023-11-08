# Dragon-Ball-Laravel

对laravel10 进行个性化的定制

# 安装要求

- php >=8.0
- Laravel >10

# 优化的功能

- 模型的命名规则

> 蛇形的命名规则，例如表名字order_item 定模型的时候，需要写成OrderItem

```php
    use ClearSwitch\DragonBallLaravel\Models\AbstractMode;
    class OrderItem extends AbstractMode{
    
    }
  ```

> 模型都必须继承

```php
   use ClearSwitch\DragonBallLaravel\Models\AbstractMode;
   class User extends AbstractMode
  {
    use AuthenticatableTrait;
  }
```

> 添加数据的数据的时候,如果没填写主键值,自动添加用雪花规格自动补充主键

```php
    //当填写了主键的值，那么存储的数据主键值就是1
    $model=new OrderItem([
      "id"=>'1'
    ])
    $model->save();
    //当没有写主键的时候，主键自动已雪花的格式补充
    $model=new OrderItem([
      "sku"=>'1123443545'
    ])
    $model->save();
    //如果你想自定义,这个自动补充主键的规则你需要写如下的代码
   use Illuminate\Support\ServiceProvider;
   class SnowflakeServiceProvider extends ServiceProvider
   {
       public function register()
       {
        // 首先解除包中绑定的snowflake服务
        $this->app->forgetInstance('snowflake');
        //绑定自定义的名叫snowflake的服务,名字必须和包中的服务名字保持一直
        $this->app->singleton('snowflake', function ($app) {
            // 你自己的服务代码
            return new YourCustomSnowflakeClass();
        });
       }
   }
   //然后注册自己定义的服务
   //您可以在 `config/app.php` 文件中的 `providers` 数组中添加它：
   'providers' => [
    // ...
    App\Providers\SnowflakeServiceProvider::class,
    // ...
   ],
```

- token 的验证

> token 使用的openssl 加密和解密,需要定义创建一个token的配置文件

```php
   //在app/config 的目录下增加一个token.php 的文件,配置如下
   return [
    //定义自己的加密密钥
    'tokenKey' => env("TOKEN_KEY", 'JVLNCYUupnPqgJ$x3t92$#6RMcm7F%rk'),
    //定义自己加密的时候的偏移量
    'vi' => env('TOKEN_VI', 'jSjJax*^gmZNa4r&')
    //定义token的过期时间
    'expires' => env('EXPIRES', now()->addYear(100))
    ];
```

> 做好这些就可以在auth.php 配置文件中就可以配置叫token的驱动

```php
  //例如
 'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,//false true
         ],
```

> 路由使用token的守卫

```php
    Route::middleware('auth')->group(function () {});
```

> 用户认证器的模型需要调用

```php
    use ClearSwitch\DragonBallLaravel\Traits\AuthenticatableTrait;
    use ClearSwitch\DragonBallLaravel\Models\AbstractMode;
   class User extends AbstractMode
  {
    use AuthenticatableTrait;
  }
```

> 获得验证通过的用户

```php
   auth('api')->user()
```

- 缓存

> 在cache.php 配置中增加一个驱动，这个驱动是token用的

```php
   'token_file' => [
            'driver' => 'token_file',
            'path'=>storage_path('token'),
      ],
```

- 异常提企业微信和邮箱

> 在config 目录中配置文件 文件名字 robot.php

```php
   return [
    'qy_we_chat' => [
        'corpid' => '企业微信群组',
        'corpsecret' => '密钥',
        //如果批量设置接受者xxx|xxxx|xxx
        'touser'=>'接受者'
    ],
    'mail'=>[
        'username'=>'发送者的邮箱',
        'password'=>'邮箱密码',
        'sender'=>'发送者名字',
        'host'=>'邮箱服务器'
    ]
];
```

> 调用

```php
  use  ClearSwitch\DragonBallLaravel\Traits\Robot
  class sendMessage{
       use Robot;
       public function send(){
          $this->qyWeChat("类容")
          $this->qyWeChat("EmailSend")
       }
  }
```
