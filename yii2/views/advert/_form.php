<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Statcompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statcompany-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_at')->textInput() ?>

    <?= $form->field($model, 'shows')->textInput() ?>

    <?= $form->field($model, 'clicks')->textInput() ?>

    <?= $form->field($model, 'costs')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_company')->textInput() ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'goods_art')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tovar_id')->textInput() ?>

    <?= $form->field($model, 'site_id')->textInput() ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_id')->textInput() ?>

    <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
