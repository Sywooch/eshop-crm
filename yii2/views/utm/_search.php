<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UtmSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utm-label-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'utm_campaign') ?>

    <?= $form->field($model, 'utm_content') ?>

    <?= $form->field($model, 'utm_source') ?>

    <?php // echo $form->field($model, 'utm_medium') ?>

    <?php // echo $form->field($model, 'utm_term') ?>

    <?php // echo $form->field($model, 'source_type') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'group_id') ?>

    <?php // echo $form->field($model, 'banner_id') ?>

    <?php // echo $form->field($model, 'position') ?>

    <?php // echo $form->field($model, 'position_type') ?>

    <?php // echo $form->field($model, 'region_name') ?>

    <?php // echo $form->field($model, 'device') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
