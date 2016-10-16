<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по товарам';
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
		$n = $sum = $cnt_zz = $cnt_za = $cnt_sklad = $cnt_zot = $cnt_nado = $cnt_dolg = $cnt_zop = 0;
?>		
		<div class="row">
			<table class="table">
			<thead>
				<tr>
					<th>NN</th>
					<th>Товар</th>
					<?//<th>Кол-во</th>?>
					<th>Сумма</th>
					<th>В заявках</th>
					<th>В заказах</th>
					<th>В отправ-х</th>
					<th>Сейчас есть</th>
					<th>Надо еще!</th>
					<th>В оплач-х</th>
					<th>Должны</th>
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			//if(!empty($row['art'])) :?>
			<tr>
                <td><?=++$n?></td>
                <td>[<?=$id?>] <?=$row['name']?></td>
                <!--<td><?=$row['cnt']?></td>-->
				<td><?=$row['summ']; $sum=$sum+$row['summ'];?></td>
				<td><?=$row['za']; $cnt_za=$cnt_za+$row['za']?></td>
				<td><?=$row['zz']; $cnt_zz=$cnt_zz+$row['zz']?></td>
				<td><?=$row['zot']; $cnt_zot=$cnt_zot+$row['zot']?></td>
				<td><?=$row['sklad']; $cnt_sklad=$cnt_sklad+$row['sklad']?></td>
				<td><? //$nado = $row['sklad'] - $row['zot'] - $row['zz']; echo $nado < 0 ? abs($nado) : ''; $nado < 0 ? $cnt_nado = $cnt_nado + abs($nado) : '' ?>
					<? $nado = $row['sklad'] - $row['zz']; echo $nado < 0 ? abs($nado) : ''; $nado < 0 ? $cnt_nado = $cnt_nado + abs($nado) : '' ?>
				</td>
				<td><?=$row['zop'] > 0 ? $row['zop'] : ''; $cnt_zop=$cnt_zop+$row['zop']?></td>
				<td><?=($row['zot']-$row['zop']); $cnt_dolg=$cnt_dolg+($row['zot']-$row['zop'])?></td>
			</tr>
			<?//endif;
			 endforeach; ?>
			<tr>
				<th class="text-right" colspan="2">Общая сумма</th>
				<th><?=$sum?></th>
				<th><?=$cnt_za?></th>
				<th><?=$cnt_zz?></th>				
				<th><?=$cnt_zot?></th>
				<th><?=$cnt_sklad?></th>
				<th><?=$cnt_nado?></th>
				<th><?=$cnt_zop?></th>
				<th><?=$cnt_dolg?></th>
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