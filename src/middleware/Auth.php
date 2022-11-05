<?php
declare (strict_types = 1);

namespace Huydt\ThinkBase\Middleware;

use Huydt\ThinkJwt\JWT;
use app\model\User;

class Auth
{

    public static $user;

    public static $user_id;

    public static $permissions;

    public static function user(){
        if(self::$user)
            return self::$user;
        self::$user = User::find(self::$user_id);
        return self::$user;
    }

    public static function permissions(){
        $user = self::user();
        $role = $user-> role;
        $permissions = (array)array_merge((array)json_decode($user-> permissions), (array)json_decode($role->permissions));

        self::$permissions = $permissions;

        return $permissions;
    }

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {

        $jwt_token = $request-> header('auth');

        if(!$jwt_token)
            return json(['status'=> 0, 'data'=> 'AccessToken denied!']);

        try{
            $payload = JWT::decode($jwt_token);
            self::$user_id = $payload-> sub;
            return $next($request);
        }catch(\Exception $e){
            return response($e-> getMessage())-> code(401);
        }
    }
}
