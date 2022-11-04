<?php
namespace Huydt\ThinkBase;

use think\Model;

class BaseModel extends Model
{

	public static $middleware = [Middleware\Permission::class];

	public static $auth_middleware = Middleware\Auth::class;

	public static $permissions = ['index', 'create', 'save', 'read', 'edit', 'update', 'delete'];

    public static $fillable = [];

    public static $rules = [];

    public function rules(){
        return [];
    }

}