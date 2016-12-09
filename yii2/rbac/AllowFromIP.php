<?php
namespace app\rbac;

use yii;
use yii\rbac\Rule;

/**
 * Проверяем authorID на соответствие с пользователем, переданным через параметры
 */
class AllowFromIP extends Rule
{
    public $name = 'AllowFromIP';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated width
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
     /*   if(empty(\Yii::$app->params['access.ip4'])) {
        	return true;
        }
        
        if(!empty(\Yii::$app->params['access.ip4'])) {
        	$ip = \Yii::$app->request->userIP;
        	$ips = \Yii::$app->params['access.ip4'];
			//$ips = array_merge($ips, array('127.0.0.1'));
			//if(!in_array(\Yii::$app->request->userIP, $arIP)) die('Not allow');
			foreach ($ips as $rule) {
	            if ($rule === '*' || $rule === $ip || (($pos = strpos($rule, '*')) !== false && !strncmp($ip, $rule, $pos))) {
	                return true;
	            }
        	}	
		}
	*/	      
        return false;
        //if(\Yii::$app->request->userIP == '94.41.61.180' ? true : false;
    }
}