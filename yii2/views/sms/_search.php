<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SmsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sms_id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'order_status') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'msg') ?>

    <?php // echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
