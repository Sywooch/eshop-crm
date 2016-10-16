<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $model app\models\Tovar */
/* @var $form yii\widgets\ActiveForm */
?>
<?
$this->registerJsFile(\Yii::$app->request->baseUrl.'/lib/fancybox/jquery.fancybox.pack.js?v=2.1.5', ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile(\Yii::$app->request->baseUrl.'/lib/fancybox/jquery.fancybox.css?v=2.1.5', ['media' => 'screen']);
$this->registerJsFile(\Yii::$app->request->baseUrl.'/lib/inventory.js', ['depends' => 'yii\web\JqueryAsset']);
?>
<div class="tovar-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <?//= $form->field($model, 'artikul')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'artikul')->begin();?>
    <?= Html::activeLabel($model,'artikul');?>
    <p class="help-block">Если ничего не задано сгенерируется автоматом по шаблону {ID категории}.{XXX}. Если оканчивается точкой - будет автоматом добавлен следующий номер .{XXX+1}</p>
    <?= Html::activeTextInput($model, 'artikul', ['class'=>'form-control', 'maxlength' => true]);?>
    <?= Html::error($model,'artikul', ['class' => 'help-block']);?>
    <?= $form->field($model, 'artikul')->end();?>
    
    <?= $form->field($model, 'type')->dropdownList($model::itemAlias('type'));?>
    
    <div class="table-responsive" id="kit" style="display: none">
	<table id="tovar-list" class="table table-striped table-bordered">
	<thead>
		<th>#</th>
		<th>Наименование</th>
		<th>Склад</th>
		<th>Цена 1 ед.</th>
		<th>Кол-во</th>
		<th>Сумма</th>
		<th></th>
	</thead>
	<tbody>
<?
//$kit_list = $model->kit;
if (count($kit_list) > 0) {
	//$model->price_old = $model->rashod;	
	//$spec_old = '';
	$n=0;
	$total_sum = $total_qnt = 0;
	foreach ($kit_list as $kit) {
?>
		<tr class="kit-row">
			<td class="num"><?= ++$n ?></td>
			<td class="name"><?= $kit->tovar->name ?><?= Html::hiddenInput("tovar_list[old][{$kit->id}][id]", $kit->id);?></td>
			<td class="sklad_id"><?= $kit->sklad->name ?><?= Html::hiddenInput("tovar_list[old][{$kit->id}][sklad_id]", $kit->sklad->id);?></td>
			<td class="price"><?= $kit->price ?></td>
			<td class="amount"><?= Html::input('text',"tovar_list[old][{$kit->id}][amount]",$kit->amount,["class"=>"form-control amount"]); $total_qnt = $total_qnt + $kit->amount; ?></td>
			<td class="sum"><?= $kit->price * $kit->amount; $total_sum = $total_sum + ($kit->price * $kit->amount) ?></td>
			<td><button type="button" class="btn btn-default btn-sm" aria-label="Удалить"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>
		</tr>
<?	
	}
}	
//echo '<pre>';print_r($model->rashod);echo '</pre>';
?>	
		<tr id="last_row">
			<th colspan="3" class="text-right"></th>
			<th colspan="" class="text-right">Итого</th>
			<th id="total_qnt"><?=$total_qnt?></th>
			<th id="total_sum"><?=$total_sum?></th>
			<td></td>
		</tr>
	</tbody>
	</table>
	</div>   
    
    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>   
    
    <?= $form->field($model, 'category_id')->dropdownList(Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column());?>
	
	<?= $form->field($model, 'active')->dropdownList($model::itemAlias('active'));?>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
$(document).ready(function() {
	show_kit();
	function show_kit() {				
		$('select[name="Tovar[type]"]').change();			
	}
})
</script>