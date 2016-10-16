<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\StatcompanySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statcompany-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'date_at') ?>

    <?= $form->field($model, 'shows') ?>

    <?= $form->field($model, 'clicks') ?>

    <?php // echo $form->field($model, 'costs') ?>

    <?php // echo $form->field($model, 'id_company') ?>

    <?php // echo $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'goods_art') ?>

    <?php // echo $form->field($model, 'tovar_id') ?>

    <?php // echo $form->field($model, 'site_id') ?>

    <?php // echo $form->field($model, 'host') ?>

    <?php // echo $form->field($model, 'shop_id') ?>

    <?php // echo $form->field($model, 'source') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
