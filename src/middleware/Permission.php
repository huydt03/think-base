<?php
declare (strict_types = 1);

namespace Huydt\ThinkJwt\Middleware;

class Permission
{

    function _check($permissions, $permission){

        if(Auth::$user_id && Auth::user()-> is_superuser)
            return true;

        if(isset($permissions[$permission])) 
            if(Auth::$user_id)
                return (isset(Auth::$permissions[$permission]) || isset(Auth::permissions()[$permission]));
            else
                return false;
        else
            return true;
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
        $controller = $request-> controller();
        $action = $request-> action();
        $permission = strtolower($controller).'.'.$action;

        $Controller = "\app\controller\\$controller";

        $permissions = $Controller::$permissions;

        if($this-> _check($permissions, $permission))
            return $next($request);
        return json([
            'status'=> 0,
            'data'=> 'Permission denied!'
        ]);
    }
}
