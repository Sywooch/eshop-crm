<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Проверяем authorID на соответствие с пользователем, переданным через параметры
 */
class IpRule extends Rule
{
    public $name = 'isFromWork';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated width
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return \Yii::$app->request->userIP == '94.41.61.180' ? true : false;
    }
}