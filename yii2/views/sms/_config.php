<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SmsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?$this->registerjs("
$('.sms-button').click(function(){
	$('#sms-config').toggle();
	return false;
});")
?>
<div class="col-cm-12 well" id="sms-config" style="display:none">

    <?php $form = ActiveForm::begin([
        'action' => ['config'],        
    ]); ?>

    <?= $form->field($model, 'eventlist')->checkBoxList($model->itemAlias('event')); ?>

    <div>
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
