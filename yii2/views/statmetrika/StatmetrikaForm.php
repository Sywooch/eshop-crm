<?php

//use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */
/* @var $form ActiveForm */
?>
<div class="StatmetrikaForm">

    <?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]]) ?>
    
    <?= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>  

    <button type="submit" class="btn btn-primary">Получить данные</button>

<?php ActiveForm::end() ?>

</div><!-- StatmetrikaForm -->
