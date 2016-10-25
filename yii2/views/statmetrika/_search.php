<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StatmetrikaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statmetrika-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date_at') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'created_by') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'host') ?>

    <?php // echo $form->field($model, 'label') ?>

    <?php // echo $form->field($model, 'visits') ?>

    <?php // echo $form->field($model, 'page_views') ?>

    <?php // echo $form->field($model, 'new_visitors') ?>

    <?php // echo $form->field($model, 'denial') ?>

    <?php // echo $form->field($model, 'depth') ?>

    <?php // echo $form->field($model, 'visit_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
