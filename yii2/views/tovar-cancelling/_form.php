<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\TovarCancelling */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tovar-cancelling-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>    

    <?//= $form->field($model, 'tovar_id')->textInput() ?>
    
    <?= $form->field($model, 'tovar_id')->dropdownList($tovar_list,['prompt'=>'']);?>

    <?//= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'sklad_id')->dropDownList($sklad_list,['prompt'=>'']) ?>

    <?= $form->field($model, 'reason')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 'shop_id')->textInput() ?>

    <div class="form-group">
    	<div class="col-sm-offset-3 col-sm-9">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
