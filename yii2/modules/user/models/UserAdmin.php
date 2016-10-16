<?php

namespace app\modules\user\models;

use Yii;
use app\modules\user\models\User;
use app\models\Shops;
use app\models\UserShops;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $fullname
 */
class UserAdmin extends \yii\db\ActiveRecord
{
    public $password;
    protected $shop_id = array();
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }
    
    public function behaviors()
	{
	    return [
	        TimestampBehavior::className(),
	        BlameableBehavior::className(),
	    ];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
			['username', 'unique'],
            ['username', 'filter', 'filter' => 'trim'],
            [['username', 'fullname'], 'string', 'min' => 2, 'max' => 255],
            ['password', 'string', 'min' => 6],
            ['password', 'required', 'on' => 'create'],            
            [['email', 'fullname'], 'default', 'value' => null],
            ['email', 'email'],            
            ['email', 'unique'],
            ['status', 'default', 'value' => '0'],           
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'auth_key','password_hash','password_reset_token', 'shop_id'], 'safe'],            
        ];
    }
    
    public function scenarios()
    {
    	return [
            'create' => ['username', 'password', 'status', 'email', 'fullname'],
            'update' => ['username', 'password', 'status', 'email', 'fullname'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'created_by' => 'Создан кем',
            'updated_at' => 'Изменен',
            'updated_by' => 'Изменен кем',
            'fullname' => 'Полное имя',
            'password' => 'Пароль',
            'shop_id' => 'Доступ к магазинам'
        ];
    }
    /**
	* Должен соответствовать статусам в /common/models/user
	* @param undefined $type
	* @param undefined $item
	* 
	* @return
	*/
    public function itemAlias($type, $item=false) {
		$_items = array(
			'status' => User::getStatusesArray()			
		);
		if ($item === false)
			return isset($_items[$type]) ? $_items[$type] : false;
		else
			return isset($_items[$type][$item]) ? $_items[$type][$item] : false;
	}
	
	public function setPassword($password=false)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
    }
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    public function getShops() {
		return $this->hasMany(Shops::className(), ['id' => 'shop_id'])
            ->viaTable('user_shops', ['user_id' => 'id']);
	}
	public function setShop_id($id)
    {
        $this->shop_id = (array) $id;
    }
	/**
	* список ID магазинов доступных юзеру
	* 
	* @return array
	*/
	public function getShop_id(){
		return ArrayHelper::getColumn($this->getShops()->all(), 'id');
	}
/*	
	public function saveShops()
	{			
		if(isset($_POST['UserAdmin']['shop_id']) and !empty($_POST['UserAdmin']['shop_id'])) {			
			$old_shops = $new_shops = array();
			foreach($this->shops as $shops) {
				$old_shops[] = $shops->id;
			}
			$new_shops = $_POST['UserAdmin']['shop_id'];
			$new_id = array_diff($new_shops, $old_shops);
			$del_id = array_diff($old_shops, $new_shops);
			if (!empty($del_id)) {
				$iddel = '';
				$numdel =0;
				foreach ($del_id as $id=>$v) {
					if (empty($iddel)) $iddel .= $id;
					else $iddel .= ','.$id;
				}
				$numdel = UserShops::deleteAll('user_id = :user_id and shop_id IN (:shop_id)', [':shop_id'=>$iddel, ':user_id'=>$this->id]);
				$return .= 'Запрещены магазины: '.$iddel.'. ';
			}
			if (!empty($new_id)) {
				foreach ($new_id as $id) {
			        $values[] = [$this->id, $id];
			    }
			    $ins = self::getDb()->createCommand()
			        ->batchInsert(UserShops::tableName(), ['user_id', 'shop_id'], $values)->execute();
			    $return = 'Разрешены магазины '.implode(",", $new_id);
			}				
		}
		elseif(isset($_POST['UserAdmin']['shop_id']) and empty($_POST['UserAdmin']['shop_id'])) {
			UserShops::deleteAll(['user_id' => $this->id]);
			$return = 'Запрещены все магазины';
		}
	}
*/	
	public function afterSave($insert, $changedAttributes)
	{
		if(isset($_POST['UserAdmin']['shop_id']) and !empty($_POST['UserAdmin']['shop_id'])) {			
			$old_shops = $new_shops = array();
			foreach($this->shops as $shops) {
				$old_shops[] = $shops->id;
			}
			$new_shops = $_POST['UserAdmin']['shop_id'];
			$new_id = array_diff($new_shops, $old_shops);
			$del_id = array_diff($old_shops, $new_shops);
			//\yii\helpers\VarDumper::dump($del_id,10,true);
			if (!empty($del_id)) {
				$iddel = '';
				$numdel =0;
				foreach ($del_id as $id=>$v) {
					if (empty($iddel)) $iddel .= $v;
					else $iddel .= ','.$v;
				}
				$numdel = UserShops::deleteAll('user_id = :user_id and shop_id IN (:shop_id)', [':shop_id'=>$iddel, ':user_id'=>$this->id]);
				$return .= 'Запрещены магазины: '.$iddel.'. ';
			}
			if (!empty($new_id)) {
				foreach ($new_id as $id) {
			        $values[] = [$this->id, $id];
			    }
			    $ins = self::getDb()->createCommand()
			        ->batchInsert(UserShops::tableName(), ['user_id', 'shop_id'], $values)->execute();
			    $return = 'Разрешены магазины '.implode(",", $new_id);
			}				
		}
		elseif(isset($_POST['UserAdmin']['shop_id']) and empty($_POST['UserAdmin']['shop_id'])) {
			UserShops::deleteAll(['user_id' => $this->id]);
			$return = 'Запрещены все магазины';
		}

	    parent::afterSave($insert, $changedAttributes);
	}
}
