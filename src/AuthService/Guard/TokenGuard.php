<?php
/**
 * TokenGuard.php
 * 文件描述
 * Created on 2023/11/1 17:23
 * Creat by ClearSwitch
 */

namespace ClearSwitch\DragonBallLaravel\AuthService\Guard;

use App\Exceptions\ValidationException;
use ClearSwitch\DragonBallLaravel\Utils\Token;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/**
 * 如果缓存token，就需要保证修改了用户的权限必须修改缓存。否则不能起作用
 * Class TokenGuard
 * @package App\AuthService\Guard
 */
class TokenGuard implements Guard
{

    /**
     * 数据提供者
     * @var
     */
    public $provider;

    /**
     * 请求
     * @var Request
     */
    public $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * 获得token过期的时间
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|int|mixed
     * @author clearSwitch
     */
    public function getExpires()
    {
        if (!empty(config('token.expires'))) {
            return time() + config('token.expires');
        } else {
            return now()->addYear(100)->getTimestamp();
        }
    }

    /**
     * 检查是否已经验证
     * @return bool
     * @author clearSwitch
     */
    public function check()
    {
        return !is_null($this->user());
    }

    /**
     * 判断当前的用户是否为一个访客
     * @return bool|void
     * @author clearSwitch
     */
    public function guest()
    {
        // TODO: Implement guest() method.
    }

    //返回已经认证的用户
    public function user()
    {
        return $this->authenticate();
    }

    /**
     * 获得认证的用户ID
     * @return bool|int|mixed|string|null
     * @author clearSwitch
     */
    public function id()
    {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        } else {
            return false;
        }
    }

    /**
     * 验证账户和密码
     * @param array $credentials
     * @return bool|Authenticatable
     * @author clearSwitch
     */
    public function validate(array $credentials = [])
    {
        //只验证了用户
        $user = $this->provider->retrieveByCredentials($credentials);
        if ($user) {
            if ($this->provider->validateCredentials($user, $credentials)) {
                return $user;
            } else {
                false;
            }
        } else {
            return false;
        }
    }

    /**
     * 登陆获得token
     * @param Authenticatable $user
     * @return false|string
     * @author clearSwitch
     */
    public function login(Authenticatable $user)
    {
        $loginTime = $this->getExpires();
        $userToken = $user->token;
        $str = $user->id . "&" . $loginTime;
        //加密获得token
        $token = Token::generate($str);
        if ($userToken) {
            Cache::store('token_file')->forget($userToken);
        }
        //Cache::add($token, $user, $this->getExpires() - time());
        Cache::store('token_file')->add($token, $user, $this->getExpires() - time());
        $user->login_at = $loginTime;
        $user->token = $token;
        $user->save();
        return $token;
    }

    /**
     * 设置已经认证过的用户
     * @param Authenticatable $user
     * @author clearSwitch
     */
    public function setUser(Authenticatable $user)
    {
        $user->getAuthIdentifier();
    }

    /**
     * 验证token
     * @return bool|false|string
     * @author clearSwitch
     */
    public function authenticate()
    {
        $routeMiddleware = collect(Route::current()->middleware());
        $middleware = $routeMiddleware->merge((Route::current()->controllerMiddleware()));
        if ($middleware->contains('auth')) {
            //那些token 返回用户
            $token = $this->request->bearerToken();
            if (empty($token)) {
                throw new ValidationException("Missing Bearer Token", 401);
            }
            //解析token 查找用户;
            if ($parse = Token::parse($token)) {
                $parseData = explode("&", $parse);
                if (count($parseData) == 2) {
                    if ($parseData[1] > time()) {
                        return Cache::store('token_file')->get($token);
                        //return $this->provider->retrieveById($parseData[0]);
                    } else {
                        Cache::store('token_file')->forget($token);
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * 推出登陆
     * @return bool
     * @throws ValidationException
     * @author clearSwitch
     */
    public function logout()
    {
        $token = $this->request->bearerToken();
        if (empty($token)) {
            throw new ValidationException("Missing Bearer Token", 401);
        }
        Cache::store('token_file')->forget($token);
        return true;
    }

    public function hasUser()
    {
        // TODO: Implement hasUser() method.
    }
}
