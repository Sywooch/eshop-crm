<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по товарам+реклама';
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
		$n = $shows = $clicks = $costs = $sum = $cnt_zz = $cnt_za = $zot_sum = $cnt_zot = $costs_sum = $cnt_zop = $cnt_zvk = 0;
?>		
		<div class="row">
			<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th>NN</th>
					<th>Товар</th>
					<th>Показы</th>
					<th>Клики</th>
					<th>CTR</th>
					<th>В заявках <span class="badge" data-toggle="tooltip" data-placement="bottom" title="Кроме тестов и дублей">?</span></th>			
					<th>CV1</th>
					<th>В заказах</th>
					<th>CV2</th>
					<th>Сумма CV2</th>
					<th>Ср. чек</th>
					<th>Отправки</th>
					<th>CV3</th>
					<th>Сумма CV3</th>
					<th>Реклама</th>
					<th>CPC</th>
					<th>Цена в заявке</th>
					<th>Цена в заказе</th>
					<th>Оплачен</th>
					<th>В кассе</th>
				</tr>
			</thead>
			<tbody>
			<? foreach($results as $id => $row):
			?>
			<tr>
                <td><?=++$n?></td>
                <td><?=$row['name']?></td>
                <td><?=$row['shows']; $shows = $row['shows'] + $shows; ?></td>
                <td><?=$row['clicks']; $clicks = $row['clicks'] + $clicks; ?></td>
                <td><? if($row['shows'] >0) {$ctr = round($row['clicks'] *100 / $row['shows'], 2); if($ctr>0) echo $ctr; $ctr =0;} ?></td>
				<td><?=$row['za']; $cnt_za=$cnt_za+$row['za']?></td>
				<td><? if($row['clicks'] >0) {$ctr = round($row['za'] *100 / $row['clicks'], 2); if($ctr>0) echo $ctr; $ctr =0;} ?></td>
				<td><?=$row['zz']; $cnt_zz=$cnt_zz+$row['zz']?></td>
		<?//cv2?><td><?=($row['za'] >0) ? (round($row['zz']*100 / $row['za'],2)) : ''?></td>
				<td><?=$row['zz_summ']; $sum=$sum+$row['zz_summ'];?></td>				
				<td><?=$row['zz'] >0 ? round($row['zz_summ'] / $row['zz'],2) : '';?></td>				
				<td><?=$row['zot']; $cnt_zot=$cnt_zot+$row['zot']?></td>
				<td><? if($row['zz'] >0) {$ctr = round($row['zot'] *100 / $row['zz'], 2); if($ctr>0) echo $ctr; $ctr =0;} ?></td>
				<td><?=$row['zot_summ']; $zot_sum = $zot_sum +$row['zot_summ']?></td>
				<td><?=$row['costs']; $costs_sum = $costs_sum + $row['costs']; ?></td>
				<td><? if($row['clicks'] >0) {$ctr = round($row['costs'] / $row['clicks'], 2); if($ctr>0) echo $ctr; $ctr =0;} ?></td>
				<td><?=$row['za'] >0 ? round($row['costs'] / $row['za'],2) : '';?></td>
				<td><?=$row['zz'] >0 ? round($row['costs'] / $row['zz'],2) : '';?></td>
				<td><?=$row['zop'] > 0 ? $row['zop'] : ''; $cnt_zop=$cnt_zop+$row['zop']?></td>
				<td><?=$row['zvk'] > 0 ? $row['zvk'] : ''; $cnt_zvk=$cnt_zvk+$row['zvk']?></td>
			</tr>
			<?//endif;
			 endforeach; ?>
			<tr>
				<th class="text-right" colspan="2">ИТОГО</th>
				<th><?=$shows?></th>
				<th><?=$clicks?></th>
				<th><? $ctr = round($clicks / $shows *100, 2); if($ctr>0) echo $ctr; ?></th>
				<th><?=$cnt_za?></th>
				<th><? if($clicks >0) {$ctr = round($cnt_za *100 / $clicks, 2); if($ctr>0) echo $ctr; $ctr =0;} ?></th>
				<th><?=$cnt_zz?></th>
		<?//cv2?><th><?=($cnt_za >0) ? (round($cnt_zz*100 / $cnt_za,2)) : ''?></th>
				<th><?=$sum ?></th>	
				<th><?=round($sum/$cnt_zz, 2);?></th>	
				<th><?=$cnt_zot?></th>
				<th><?=($cnt_zz >0) ? (round($cnt_zot*100 / $cnt_zz,2)) : ''?></th>				
				<th><?=$zot_sum?></th>
				<th><?=$costs_sum?></th>
				<th><?= ($clicks >0) ? (round($costs_sum / $clicks,2)) : ''?></th>
				<th><?=round($costs_sum/$cnt_za, 2);?></th>
				<th><?=round($costs_sum/$cnt_zz, 2);?></th>
				<th><?=$cnt_zop?></th>
				<th><?=$cnt_zvk?></th>
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