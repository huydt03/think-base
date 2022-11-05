##Install
composer require huydt/think-base
##Usages
#####Model
```sh
use Huydt\ThinkBase\BaseModel;

class User extends BaseModel
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
```
#####Controller
```sh
use Huydt\ThinkBase\BaseController;

class Users extends BaseController
{
    protected $model = User::class;

    public function profile(){
    	return $this-> Auth::user();
    }

    public function _update(Request $request){
    	return $this-> update($request, $this-> Auth::$user_id);
    }

}
```
#####Middleware
######Auth
```sh
use Huydt\ThinkBase\Middleware\Auth;
```
######Permission
```sh
use Huydt\ThinkBase\Middleware\Permission
```