<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
?>
<h1>Свод продаж</h1>
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
	
	<?= $form->field($model, 'sklad_id')->dropdownList($sklads, ['prompt'=>'']);?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(!empty($results)) { 
		$n = $sum = $cnt = 0;
?>		
		<div class="row">
			<table class="table">
			<thead>
				<tr>
					<th>NN</th>
					<th>Товар</th>
					<th>Артикул</th>
					<th>Кол-во</th>
					<th>Сумма</th>					
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			//print_r($row);?>
			<tr>
                <td><?=++$n?></td>
                <td>[<?=$id?>] <?=$row['name']?></td>
                <td><?=$row['artikul']?></td>
                <td><?=$row['cnt']; $cnt = $cnt + $row['cnt']?></td> 
				<td><?=$row['summ']; $sum = $sum + $row['summ'];?></td>				
			</tr>
			<?//endif;
			 endforeach; ?>
			<tr>
				<th class="text-right" colspan="3">ИТОГ</th>
				<th><?=$cnt?></th>
				<th><?=$sum?></th>				
			</tr>
			</tbody>
			</table>
		</div>

<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<? } ?>