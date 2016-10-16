<?php

namespace app\modules\user\controllers;
 
use app\modules\user\models\LoginForm;
use app\modules\user\models\PasswordResetRequestForm;
use app\modules\user\models\ResetPasswordForm;
use app\modules\user\models\SignupForm;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
 
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
 
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
    
        $model = new LoginForm();        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
        	
        	Yii::info('Вход: ' . Yii::$app->user->identity->username . ' IP: '.Yii::$app->request->userIP,'logged');
        
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
 
    public function actionLogout()
    {
        Yii::$app->user->logout();
 
        return $this->goHome();
    }
        /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    public function actionSaveshops(){
		if(isset($_POST) and array_key_exists('select_user_shop_menu', $_POST)) {
			$select_shop = (int) $_POST['select_user_shop_menu'];
			$user = new \app\modules\user\models\User;			
			$shops = $user->getShops($select_shop);
			
			//\yii\helpers\VarDumper::dump(Yii::$app->user->id,true,10);
			if(!empty($shops) and in_array($select_shop, $shops['0']) ) {
				//\yii\helpers\VarDumper::dump($shops,10,true);
			
				Yii::$app->response->cookies->add(new \yii\web\Cookie([
			        'name' => 'select_user_shop_menu',
			        'value' => $select_shop,
			        'expire' => time() + 86400 * 365,
			    ]));
			    Yii::$app->session->setFlash('success', 'Магазин выбран.');
			    return $this->goHome();
			}
			else {
				Yii::$app->session->setFlash('error', 'Магазин не найден.');
			    return $this->goHome();
			}
		}
		return $this->goHome();
	}
}