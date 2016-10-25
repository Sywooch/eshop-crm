<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statmetrika-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'label')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'visits')->textInput() ?>

    <?= $form->field($model, 'page_views')->textInput() ?>

    <?= $form->field($model, 'new_visitors')->textInput() ?>

    <?= $form->field($model, 'denial')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'depth')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'visit_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
