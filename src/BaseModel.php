<?php
namespace app\model;

use think\Model;

class BaseModel extends Model
{

	public static $middleware = [];

	// public static $auth_middleware = \app\middleware\Auth::class;

    public static $fillable = [];

    public static $rules = [];

    public function rules(){
        return [];
    }
}