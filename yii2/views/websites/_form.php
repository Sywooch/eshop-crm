<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use app\models\Tovar;

/* @var $this yii\web\View */
/* @var $model app\models\Websites */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="websites-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'created_at')->textInput() ?>

    <?//= $form->field($model, 'updated_at')->textInput() ?>

    <?//= $form->field($model, 'created_by')->textInput() ?>

    <?//= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'active')->dropdownList($model::itemAlias('active'));?>

    <?= $form->field($model, 'category_id')->dropdownList(Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column(),['prompt'=>'']);?>
    
    <?= $form->field($model, 'tovar_id')->dropdownList(Tovar::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column(),['prompt'=>'']);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
