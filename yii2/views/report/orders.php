<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по заявкам';
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
    <?= $form->field($model, 'rowTotal')->checkbox() ?>
    
    <?= $form->field($model, 'cat_id')->dropDownList($categories, ['prompt'=>'']) ?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(!empty($results)) { ?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Дата</th>
			<th>Показы</th>
			<th>Клики</th>
			<th>CTR</th>
			<th>Заявки <span class="badge" data-toggle="tooltip" data-placement="bottom" title="Кроме тестов и дублей">?</span></th>			
			<!--<th>Заявки чистые <span class="badge" data-toggle="tooltip" data-placement="bottom" title="В работе, заказ, отказ">?</span></th>-->
			<th>CV1</th>
			<th>Заказы</th>
			<th>CV2</th>
			<th>Сумма CV2</th>
			<th>Ср. чек</th>
			<th>Отправки</th>
			<th>CV3</th>
			<th>Сумма CV3</th>
			<th>Реклама</th>
			<th>Цена заявки</th>
			<th>Цена заказа</th>
			<th>Оплачен</th>
			<th>В кассе</th>
		</tr>
	</thead>
	<tbody>
<?
$cnt_show = $cnt_clicks = 0;
$cnt_all = $cnt_dub = $cnt_work = $cnt_nedozvon = $cnt_otkaz = $cnt_tp = 0;
$cnt_zz = $zz_sum = $cnt_otp = $otp_sum = $cnt_reklama = $cnt_oplachen = $cnt_vkasse = 0;

foreach($results as $rdate=>$result) {
	if($result['show'] >0 or $result['cnt_za']) :
	
	$cnt_show = $cnt_show + $result['show'];
	$cnt_clicks = $cnt_clicks + $result['clicks'];	
	//$cnt_all = $cnt_all + $result['cnt_all'];
	$cnt_za = $cnt_za + $result['cnt_za'];
	/*$cnt_dub = $cnt_dub + $result['cnt_dub'];
	$cnt_work = $cnt_work + $result['cnt_work'];
	$cnt_nedozvon = $cnt_nedozvon + $result['cnt_nedozvon'];
	$cnt_otkaz = $cnt_otkaz + $result['cnt_otkaz'];
	$cnt_tp = $cnt_tp + $result['cnt_tp'];*/
	$cnt_zz = $cnt_zz + $result['cnt_zz'];
	$zz_sum = $zz_sum + $result['zz_sum'];
	$cnt_otp = $cnt_otp + $result['cnt_otp'];
	$otp_sum = $otp_sum + $result['otp_sum'];
	$cnt_reklama = $cnt_reklama + $result['reklama'];
	$cnt_oplachen = $cnt_oplachen + $result['cnt_oplachen'];
	$cnt_vkasse = $cnt_vkasse + $result['cnt_vkasse'];
?>
	<tr>
		<td><?=$rdate?></td>		
		<td><?=$result['show'] >0 ? $result['show'] : ''?></td>		
		<td><?=$result['clicks'] >0 ? $result['clicks'] : '';?></td>
		<td><?=(isset($result['show']) or !empty($result['show'])) ? round($result['clicks']*100 / $result['show'],2) : ''?></td>		
		<!--<td><?//=$result['cnt_all'] > 0 ? $result['cnt_all'] : '';?></td>-->
		<td><?=$result['cnt_za'] > 0 ? $result['cnt_za'] : '';?></td>
		<td><?=(isset($result['clicks'])) ? (round($result['cnt_za']*100 / $result['clicks'],2)) : '' ?></td>
		<td><?=$result['cnt_zz'] >0 ? $result['cnt_zz'] : '';?></td>
		<td><?=($result['cnt_za'] >0) ? (round($result['cnt_zz']*100 / $result['cnt_za'],2)) : ''?></td>
		<td><?=$result['zz_sum'] >0 ? $result['zz_sum'] : '';?></td>
		<td><?=$result['cnt_zz'] >0 ? round($result['zz_sum']/$result['cnt_zz'],2) : '';?></td>
		<td><?=$result['cnt_otp'] >0 ? $result['cnt_otp'] : '';?></td>
		<td><?=($result['cnt_zz'] >0 and $result['cnt_otp'] >0) ? round($result['cnt_otp']*100 / $result['cnt_zz'],2) : ''?></td>
		<td><?=$result['otp_sum'] >0 ? $result['otp_sum'] : '';?></td>
		<td><?=$result['reklama'] >0 ? $result['reklama'] :''; ?></td>		
		<td><?=$result['cnt_za'] >0 ? round($result['reklama'] / $result['cnt_za'],2) : ''?></td>	
		<td><?=$result['cnt_zz'] >0 ? round($result['reklama'] / $result['cnt_zz'],2) : ''?></td>
		<td><?=$result['cnt_oplachen'] >0 ? $result['cnt_oplachen'] : ''?></td>
		<td><?=$result['cnt_vkasse'] >0 ? $result['cnt_vkasse'] : ''?></td>
	</tr>	
<? endif;
} //foreach?>
	<tr class='itog'>
		<th class="text-right">Итого</th>
		<th><?=$cnt_show;?></th>
		<th><?=$cnt_clicks?></th>
		<th><?=($cnt_show >0) ? round($cnt_clicks * 100 / $cnt_show, 2) : ''?></th>
		<!--<th><?//=$cnt_all;?></th>-->
		<th><?=$cnt_za;?></th>
		<th><?=($cnt_clicks >0) ? round($cnt_za * 100 / $cnt_clicks,2) : ''?></th>
		<th><?=$cnt_zz;?></th>
		<th><?=($cnt_za >0) ? round($cnt_zz * 100 / $cnt_za,2) : ''?></th>
		<th><?=$zz_sum;?></th>
		<th><?=($cnt_zz >0) ? round($zz_sum / $cnt_zz,2) : ''?></th>
		<th><?=$cnt_otp?></th>		
		<th><?=($cnt_zz >0) ? round($cnt_otp * 100 / $cnt_zz,2) : ''?></th>
		<th><?=$otp_sum;?></th>
		<th><?=$cnt_reklama?></th>			
		<th><?=($cnt_za >0) ? round($cnt_reklama / $cnt_za,2) : ''?></th>	
		<th><?=($cnt_zz >0) ? round($cnt_reklama / $cnt_zz,2) : ''?></th>
		<th><?=$cnt_oplachen?></th>
		<th><?=$cnt_vkasse?></th>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<? } ?>