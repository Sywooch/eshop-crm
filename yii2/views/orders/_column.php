<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->registerjs("
$('.column-button').click(function(){
	$('#orders-column').toggle();
	return false;
});")
?>
<div class="col-cm-12 well" id="orders-column" style="display:none">

   <?php $form = ActiveForm::begin([
        'action' => ['index'],
        //'method' => 'get',
        //'layout' => 'inline',        
        /*'fieldConfig' => [
	        'template' => '{label}{input}{error}',
	        'labelOptions' => ['class' => '0'],
	    ],*/
       // 'fieldConfig' => ['template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"],
    ]); ?>
   
   
    <?//=Html::beginForm(['order/index'], 'post', ['enctype' => 'multipart/form-data']) ?>

	<?= $form->field($model, 'column_visible')->checkBoxList($model->column_list) ?>

  	<?/* $num_col = count($model->attributeLabels());
	$num_div = 4;
	$num_checkbox = ceil($num_col/$num_div);
	$all = $model->attributeLabels();
	$n=1;
	
	echo '<div class="row">';
	
	foreach($all as $key=>$attr) {
		if($n < $num_checkbox) {echo "<div class='col-sm-3'>";}
		if($n=$num_checkbox) $n=0;
				
		echo '<div class="checkbox">';
		echo Html::checkbox($key, true, ['label' => $attr]);
		echo $n++.'</div>';
		
		if($n = $num_checkbox) {echo "</div>";$n++;}
		
	}
	
	echo '</div>';
	*/?>

    <div class="form-group">
        <?= Html::submitButton('Запомнить', ['class' => 'btn btn-primary']) ?>
    </div>

	<?//= Html::endForm() ?>
	<?php ActiveForm::end(); ?>

</div>
