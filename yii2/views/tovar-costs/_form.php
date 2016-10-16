<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Tovar;

/* @var $this yii\web\View */
/* @var $model app\models\TovarCosts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tovar-costs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tovar_id')->dropdownList(Tovar::find()->select(['name', 'id'])->indexBy('id')->column()); ?>

    <?= $form->field($model, 'cost')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'current')->dropdownList($model::itemAlias('current')) ?>

    <?= $form->field($model, 'active')->dropdownList($model::itemAlias('active')) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
