<?php
declare (strict_types = 1);

namespace Huydt\ThinkBase;

use think\Request;
use think\exception\ValidateException;

class BaseController
{

    protected $model;

    protected $class_name;

    protected $middleware;

    protected $Auth;

    public static $permissions;

    function _getVar($var, $default = []){
        return isset($this-> model::$$var)? $this-> model::$$var: $default;
    }

    function _toPermission($name){
        return strtolower($this->class_name).'s.'.$name;
    }

    function __construct(){
        $this-> class_name = substr(strrchr($this-> model, "\\"), 1);
        $this-> middleware = $this-> _getVar('middleware');
        $this-> Auth = $this-> _getVar('auth_middleware', null);

        // init
        if(isset($this-> Auth))
            $this-> middleware = array_merge([$this-> Auth], $this-> middleware);

        $permissions = $this-> _getVar('permissions');
        foreach ($permissions as $value) {
            self::$permissions[$this-> _toPermission($value)] = 1;
        }
    }

    public function permissions()
    {
        return json(self::$permissions);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {   
        if(!$this-> Auth)
            return $this-> model::select();

        $user = $this-> Auth::user();
        $name = strtolower($this-> class_name);
        return $user[$name]? $user[$name] : $user[$name."s"];
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

        $data = $request-> post($this-> model::$fillable);

        try{   
            validate($this-> model::$rules)->check($data);
        }catch (ValidateException $e) {
            return json([
                'status'    => 0,
                'data'      => $e->getError()
            ]);
        }

        $model = $this-> model::create($data);
            
        return json([
            'status'    => 1,
            'data'      => $model
        ]);

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $data = $this->model::find($id);
        return view('read', ['model'=>$data]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = $this->model::find($id);
        return view('create', ['model'=>$data]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $model = $this-> model::find($id);

        if(!$model)
            return json([
                'status'    => 0,
                'data'      => $this-> class_name." '$id' not found!"
            ]);

        $data = $request-> post($this-> model::$fillable);

         try{   
            validate($model-> rules())->check($data);
            try{
                $model-> save($data);
            }catch(\Exception $e){
                return json([
                    'status'    => 0,
                    'data'      => $data
                ]);
            }
        }catch (ValidateException $e) {
            return json([
                'status'    => 0,
                'data'      => $e->getError()
            ]);
        }

        return $model;

    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $model = $this-> model::find($id);

        if(!$model)
            return json([
                'status'    => 0,
                'data'      => $this-> class_name." '$id' not found!"
            ]);

        $model-> delete();

        return json([
            'status'    => 1,
            'data'      => $model
        ]);
    }
}
