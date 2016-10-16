<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Проверяем authorID на соответствие с пользователем, переданным через параметры
 */
class managerNoEdit extends Rule
{
    public $name = 'managerNoEdit';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated width
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        //if(Yii::$app->user->can('manager'))
        {
			Yii::info($user);
			//echo '<pre>';print_r($item);echo '</pre>';
			//echo '<pre>';print_r($params);echo '</pre>';
		}
        //return isset($params['post']) ? $params['post']->createdBy == $user : false;
    }
}