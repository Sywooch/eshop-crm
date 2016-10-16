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
					<th>NN</th>					
					<th>Товар</th>
					<th>Кол-во</th>										
					<th>Сумма</th>
					<th>Закуп</th>
					<th>Кроссел сумма</th>
					<th>Кроссел закуп</th>
					<th>Реклама</th>
					<th>ФОТ</th>
					<th>Расход</th>
					<th>Доставка</th>
					<th>[Кол] Возврат [%]</th>
					<th>Прибыль [%]</th>
					<th>Остатки</th>
				</tr>
			</thead>
			<tbody>
			<? 		
			//echo '<pre>';print_r($row1);echo '</pre>';
			$nn = $cnt_cnt = $cnt_sum = $cnt_zak = $cnt_dosum = $cnt_dozak = $cnt_reklama = $cnt_profit = $cnt_summaotp = $cnt_ostatok = $cnt_vozvrat = 0;
			foreach($results as $k=>$v):
			?>		
			<tr>							
				<td><?=++$nn?></td>
				<td>[<?= $v['id']?>] <?=$v['name']?></td>
				<td><?=$v['amount']; $cnt_cnt=$cnt_cnt+$v['amount']?></td>
				<td><?=$v['price']; $cnt_sum=$cnt_sum+$v['price'];?></td>
				<td><?=$v['pprice']; $cnt_zak=$cnt_zak+$v['pprice'];?></td>
				<td><?=$v['dosales']['price']; $cnt_dosum=$cnt_dosum+$v['dosales']['price'];?></td>
				<td><?=$v['dosales']['pprice']; $cnt_dozak=$cnt_dozak+$v['dosales']['pprice'];?></td>
				<td><?=$v['reklama']; $cnt_reklama=$cnt_reklama+$v['reklama'];?></td>
				<td></td>
				<td></td>
				<td><?= $v['summaotp']; $cnt_summaotp = $cnt_summaotp + $v['summaotp'];?></td>
				<td><?= ($v['vozvrat']['amount'] >0) ? ('['.$v['vozvrat']['amount'].'] '.$v['vozvrat']['price'].' ['.round($v['vozvrat']['price'] * 100 / $v['price']).']') : ''; $cnt_vozvrat += $v['vozvrat']['price'];?></td>
				<td><?= $cnt_profit=($v['price'] + $v['dosales']['price']) - ($v['pprice'] + $v['dosales']['pprice'] + $v['reklama'] + $v['summaotp']); ?> [<?= ($v['price'] > 0) ? round($cnt_profit * 100 / $v['price']) : '' ?>]</td>
				<td><?= $v['ostatok']; $cnt_ostatok = $cnt_ostatok + $v['ostatok'];?></td>
			</tr>
			<? endforeach; ?>
			<tr>
				<th class="text-right" colspan="2">ИТОГО</th>
				<th><?=$cnt_cnt//$c_kol?></th>
				<th><?=$cnt_sum//$c_sum?></th>
				<th><?=$cnt_zak//$c_zak?></th>			
				<th><?=$cnt_dosum;?></th>
				<th><?=$cnt_dozak;?></th>
				<th><?=$cnt_reklama;?></th>
				<th></th>				
				<th></th>
				<th><?= $cnt_summaotp;?></th>
				<th><?= $cnt_vozvrat;?></th>
				<th><?= ($cnt_sum + $cnt_dosum) - ($cnt_zak + $cnt_dozak + $cnt_reklama + $cnt_summaotp); ?></th>
				<th><?= $cnt_ostatok?></th>
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