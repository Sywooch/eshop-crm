<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по клиенту/товару';
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
					<th>Дата</th>
					<th>Клиент</th>					
					<th>Тел</th>
					<th>Заказ</th>
					<th>Товар</th>
					<th>Цена</th>
					<th>Кол-во</th>
					<th>Скидка</th>
					<th>Сумма</th>					
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			//if(!empty($row['art'])) :?>
			<tr>
                <td><?=++$n?></td>
                <td><?=$row['date']?></td>
                <td><?=$row['fio']?></td>
				<td><?=$row['phone']?></td>
				<td><?=$row['order_id']?></td>
				<td><?=$row['artikul']?></td>				
				<td><?=$row['price'] ?></td>
				<td><?=$row['amount']; $cnt_zz=$cnt_zz+$row['zz']?></td>				
				<td><?=$row['discount']?></td>
				<td><?=$row['summa']?></td>
			</tr>
			<?//endif;
			 endforeach; ?>
			
			</tbody>
			</table>
		</div>
<? } ?>