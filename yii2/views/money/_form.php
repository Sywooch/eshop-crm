<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\MoneyItem;
use app\models\MoneyMethod;

/* @var $this yii\web\View */
/* @var $model app\models\Money */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="money-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->field($model, 'date_at')->widget(yii\jui\DatePicker::classname(), [
            'model' => $searchModel,
            'attribute' => 'date_at',
            'dateFormat' => 'yyyy-MM-dd',
            'options' => [
                'class' => 'form-control'
            ],
            'clientOptions' => [
                //'dateFormat' => 'yyyy-MM-dd',
            ]
        ])
    ?>

    <?= $form->field($model, 'summa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'item_id')->dropdownList(MoneyItem::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt'=>'']) ?>

    <?= $form->field($model, 'method_id')->dropdownList(MoneyMethod::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt'=>'']) ?>

    <?= $form->field($model, 'type')->dropDownList($model::itemAlias('type'), ['prompt'=>'']) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
