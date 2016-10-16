<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Tovar;
use app\models\Sklad;

/* @var $this yii\web\View */
/* @var $model app\models\TovarPrihod */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tovar-prihod-form">

    <?php $form = ActiveForm::begin(); ?>    

	<?	//$tovar_list = ArrayHelper::map(Tovar::find()->where(['active'=>1])->with('category')->all(), 'id', 'name', 'category.name');
	//yii\helpers\VarDumper::dump($tovar_list, 3, true);
	 echo $form->field($model, 'tovar_id')->dropdownList(ArrayHelper::map(Tovar::find()->where(['active'=>1])->with('category')->all(), 'id', 'name', 'category.name'));?>
    
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

    <?= $form->field($model, 'price')->textInput() ?>
    
    <?= $form->field($model, 'price_sale')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'supplier_id')->textInput() ?>

    <?= $form->field($model, 'sklad_id')->dropdownList(Sklad::find()->where(['shop_id'=>Yii::$app->params['user.current_shop']])->select(['name', 'id'])->indexBy('id')->column()) ?>

    <?= $form->field($model, 'doc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
