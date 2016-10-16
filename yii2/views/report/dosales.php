<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use app\models\Category;

$this->title = 'Анализ допродаж';
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
	<?= $form->field($model, 'category_id')->dropdownList(Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column())
	?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>
<?//echo '<pre>';print_r($results);echo '</pre>';?>
<?if(!empty($results)) { 
		$n = $sum = $cnt = 0;
?>		
		<div class="row1">
			<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th></th>
					<th>NN</th>					
					<th>Товар</th>
					<th>Кол-во</th>										
					<th>Сумма</th>
					<th>Закуп</th>
				</tr>
			</thead>
			<tbody>
			<? 		
			//echo '<pre>';print_r($row1);echo '</pre>';
			foreach($results as $k=>$v):
			$nn = $c_sum = $c_kol = $c_zak = $cnt_cnt = $cnt_sum = $cnt_zak = 0; $ar=array();
				
			?>
			<tr>				
				<th colspan='6'><?=$k?></th>
			</tr>
				<?foreach($v as $arb=>$row) : //echo '<pre>';print_r($ar);echo '</pre>';?>
			<tr>
				<td></td>			
				<td><?=++$nn?></td>
				<td><?=$arb?></td>
				<td><?=$row['amount']; $cnt_cnt=$cnt_cnt+$row['amount']?></td>
				<td><?=$row['price']; $cnt_sum=$cnt_sum+$row['price'];?></td>
				<td><?=$row['pprice']; $cnt_zak=$cnt_zak+$row['pprice'];?></td>
				
			</tr>
				<? endforeach; ?>
			<tr>
				<th class="text-right" colspan="3">ИТОГО</th>
				<th><?=$cnt_cnt//$c_kol?></th>
				<th><?=$cnt_sum//$c_sum?></th>
				<th><?=$cnt_zak//$c_zak?></th>			
			</tr>	
			<? endforeach; ?>
			
			</tbody>
			</table>
		</div>

<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<? } ?>