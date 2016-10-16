<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;
use app\models\Tovar;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Statcompany */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="statcompany-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'date_at')->textInput() ?>
    
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

    <?= $form->field($model, 'shows')->textInput() ?>

    <?= $form->field($model, 'clicks')->textInput() ?>

    <?= $form->field($model, 'costs')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_company')->textInput() ?>

    <?= $form->field($model, 'category_id')->dropdownList(Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column()) ?>

    <?//= $form->field($model, 'goods_art')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tovar_id')->dropdownList(ArrayHelper::map(Tovar::find()->where(['active'=>1, 'shop_id'=>Yii::$app->params['user.current_shop']])->with('category')->all(), 'id', 'name', 'category.name'),['prompt'=>'']) ?>

    <?//= $form->field($model, 'site_id')->textInput() ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'shop_id')->textInput() ?>

    <?= $form->field($model, 'source')->dropdownList($model::itemAlias('source')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
