<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';

if(Yii::$app->user->isGuest) {
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Вы не авторизованы. Укажите свои данные для входа:</p>

    <div class="row">
        <div class="col-sm-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<? }
else {
?>	
<p>Начинайте уже работать, <?=Yii::$app->user->identity->username?>!</p>

<?}?>