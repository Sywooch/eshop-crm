<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use app\models\Senders;

$this->title = 'Возвраты';
?>
<h1><?= ($this->title) ?></h1>
<?if(!empty($errors)) {
	print_r($errors);
}?>
<?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?//= $form->field($model, 'date1')->fileInput(['class'=>'form-control']) ?>
    <?= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>
	<?= $form->field($model, 'date2')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>  
	
	<?= $form->field($model, 'sender_id')->dropdownList($senders, ['prompt'=>'']);?>
	
	<?//= $form->field($model, 'category_id')->dropdownList($categories, ['prompt'=>'']);?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(count($results)>0) { 
		$n = $sum = $cnt = 0;
?>		
		<div class="row1">
			<table class="table">
			<thead>
				<tr>
					<th>NN</th>
					<th>Служба</th>
					<th>Сумма</th>					
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			if($row >0) ://print_r($row);?>
			<tr>
                <td><?= ++$n?></td>
                <td><?= $id?></td>               
                <td><?= round($row, 2); $sum = $sum + $row;?></td>                			
			</tr>
			<? endif;
			 endforeach; ?>
			 <tfoot style="font-weight: bold">
			 <tr>
			 	<td></td>
			 	<td>Итого</td>
			 	<td><?= round($sum, 2); ?></td>
			 </tr>
			 </tfoot>
			</tbody>
			</table>
		</div>

<? } ?>