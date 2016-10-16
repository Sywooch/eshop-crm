<?php
namespace app\components;

use Yii;
use yii\web\Controller;
use yii\flash\Flash;

if(\Yii::$app->request->userIP != '94.41.61.180') die('Not allow');//!\Yii::$app->user->can('root') and  

class BaseController extends Controller
{
	public $shop_id = 0;
	
    public function init()
    {
        parent::init();
        
        //$usershop = 0;
        
        if(isset(Yii::$app->request->cookies['select_user_shop_menu'])) {        	
        	if(array_key_exists(Yii::$app->request->cookies['select_user_shop_menu']->value, \yii\helpers\ArrayHelper::map(Yii::$app->user->identity->shops, 'id', 'name'))) 
        		$this->shop_id = (Yii::$app->request->cookies['select_user_shop_menu']->value);
        }
        		
		Yii::$app->params['user.current_shop'] = $this->shop_id;
    }
    
    public function beforeAction($action)
	{
	    // your custom code here, if you want the code to run before action filters,
	    // wich are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
//\yii\helpers\VarDumper::dump($action,10,true);
	    if (!parent::beforeAction($action)) {
	        return false;
	    }

	    // other custom code here

	    return true; // or false to not run the action
	}
    
}