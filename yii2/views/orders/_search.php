<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->registerjs("
$('.search-button').click(function(){
	$('#orders-search').toggle();
	return false;
});")
?>
<div class="col-cm-12 well" id="orders-search" style="display:none">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'layout' => 'inline',        
        /*'fieldConfig' => [
	        'template' => '{label}{input}{error}',
	        'labelOptions' => ['class' => '0'],
	    ],*/
       // 'fieldConfig' => ['template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"],
    ]); ?>

    <?php //= $form->field($model, 'id') ?>
	
	<div class="row">
		<div class="col-md-4">
			<p class="form-control-static"><strong><?=$model->getAttributeLabel('created_at')?></strong></p>
			<?= $form->field($model, 'date_order_start')->widget(\yii\jui\DatePicker::classname(), [
		    	'language' => 'ru',
		    	'dateFormat' => 'yyyy-MM-dd',
		    	'options' => ['class' => 'form-control', 'placeholder'=>$model->getAttributeLabel('date_order_start')],
		    ]) ?>

    
    <?= $form->field($model, 'date_order_end')->widget(\yii\jui\DatePicker::classname(), [
		    	'language' => 'ru',
		    	'dateFormat' => 'yyyy-MM-dd',
		    	'options' => ['class' => 'form-control','placeholder'=>$model->getAttributeLabel('date_order_end')],
		    ]) ?>
		</div>
		<div class="col-md-6">
			<p class="form-control-static"><strong><?=$model->getAttributeLabel('status')?></strong></p>
			<?= $form->field($model, 'status_array')->checkBoxList(
    			$model->itemAlias('status')    			
			);?>
		</div>
	</div>
    <?php //= $form->field($model, 'status') ?>

    <?php //= $form->field($model, 'dublicate') ?>

    <?php //= $form->field($model, 'otpravlen') ?>

    <?php // echo $form->field($model, 'dostavlen') ?>

    <?php // echo $form->field($model, 'oplachen') ?>

    <?php // echo $form->field($model, 'vkasse') ?>

    <?php // echo $form->field($model, 'vozvrat') ?>

    <?php // echo $form->field($model, 'vozvrat_cost') ?>

    <?php // echo $form->field($model, 'prich_double') ?>

    <?php // echo $form->field($model, 'prich_vozvrat') ?>

    <?php // echo $form->field($model, 'summaotp') ?>

    <?php // echo $form->field($model, 'discount') ?>

    <?php // echo $form->field($model, 'identif') ?>

    <?php // echo $form->field($model, 'dostavza') ?>

    <?php // echo $form->field($model, 'manager_id') ?>

    <?php // echo $form->field($model, 'category') ?>

    <?php // echo $form->field($model, 'fast') ?>

    <?php // echo $form->field($model, 'packer_id') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'client_id') ?>

    <?php // echo $form->field($model, 'tclient') ?>

    <?php // echo $form->field($model, 'note') ?>

	<div class="row" style="margin-top: 1em;">
		<div class="col-md-12">
			<div class="form-group">
				<?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
				<?php //= Html::resetButton('Сброс', ['class' => 'btn btn-default']) ?>
				<?= Html::a('Очистить фильтр', ['index'], ['class' => 'btn btn-default']) ?>
			</div>
		</div>
	</div>
   

    <?php ActiveForm::end(); ?>

</div>
