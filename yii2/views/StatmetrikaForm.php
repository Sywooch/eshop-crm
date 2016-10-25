<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */
/* @var $form ActiveForm */
?>
<div class="StatmetrikaForm">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'date_at') ?>
        <?= $form->field($model, 'created_at') ?>
        <?= $form->field($model, 'created_by') ?>
        <?= $form->field($model, 'updated_at') ?>
        <?= $form->field($model, 'updated_by') ?>
        <?= $form->field($model, 'visits') ?>
        <?= $form->field($model, 'page_views') ?>
        <?= $form->field($model, 'new_visitors') ?>
        <?= $form->field($model, 'visit_time') ?>
        <?= $form->field($model, 'label') ?>
        <?= $form->field($model, 'denial') ?>
        <?= $form->field($model, 'depth') ?>
        <?= $form->field($model, 'host') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- StatmetrikaForm -->
