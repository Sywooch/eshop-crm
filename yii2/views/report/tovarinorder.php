<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по товарам в заказах';
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
	
	<?= $form->field($model, 'category_id')->dropdownList($categories, ['prompt'=>'']);?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(!empty($results)) { 
		$n = $cnt_zz = $cnt_zot = $cnt_nado = 0;
?>		
		<div class="row">
			<table class="table">
			<thead>
				<tr>
					<th>NN</th>
					<th>Товар</th>					
					<th>В заказах</th>
					<th>Отправлено</th>					
					<th>Не отправлено</th>
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			//if(!empty($row['art'])) :?>
			<tr>
                <td><?=++$n?></td>
                <td>[<?=$id?>] <?=$row['name']?></td>                				
				<td><?=$row['zz']; $cnt_zz=$cnt_zz+$row['zz']?></td>
				<td><?=$row['zot']; $cnt_zot=$cnt_zot+$row['zot']?></td>				
				<td><? //$nado = $row['sklad'] - $row['zot'] - $row['zz']; echo $nado < 0 ? abs($nado) : ''; $nado < 0 ? $cnt_nado = $cnt_nado + abs($nado) : '' ?>
					<? $nado = $row['zz'] - $row['zot']; echo $nado > 0 ? ($nado) : ''; $nado > 0 ? $cnt_nado = $cnt_nado + abs($nado) : 0 ?>
				</td>				
			</tr>
			<?//endif;
			 endforeach; ?>
			<tr>
				<th class="text-right" colspan="2">ИТОГО</th>				
				<th><?=$cnt_zz?></th>				
				<th><?=$cnt_zot?></th>				
				<th><?=$cnt_nado?></th>				
			</tr>
			</tbody>
			</table>
		</div>
<? } ?>