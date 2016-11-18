<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

?>
<h1>Анализ по менеджерам</h1>
<?php //if(!empty($errors)) {print_r($errors);}?>
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
    
    <?= $form->field($model, 'category_id')->dropdownList($model->categorylist, array('prompt'=>'')) ?>
    
    <?= $form->field($model, 'source')->dropdownList($model->sourcelist, array('prompt'=>'')) ?>
    
    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?php if(!empty($results)) { ?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>			
			<th>Менеджер</th>
			<th>Заявки</th>
                        <th>Заявки ТП</th>
			<th>Заявки чистые <span class="badge" data-toggle="tooltip" data-placement="bottom" title="В работе, обработан, заказ, отказ, консультация">?</span></th>
			<th>Заказы</th>
			<th>%</th>
                        <th>Осн. товар</th>
                        <th>Апселл</th>
			<th>Продано всего</th>
			<th>Ср. сумма</th>
		</tr>
	</thead>
	<tbody>
<?php
$cnt_all = $cnt_za = $cnt_zz = $cnt_osntovar = $cnt_upsell = $cnt_sum = $cnt_avg = 0;

foreach($results as $manager=>$result) {
	$cnt_all = $cnt_all + $result['cnt_all'];
        $cnt_tp = $cnt_tp + $result['cnt_tp'];
	$cnt_za = $cnt_za + $result['cnt_za'];
	$cnt_zz = $cnt_zz + $result['cnt_zz'];
        $cnt_osntovar = $cnt_osntovar + $result['osntovar'];
        $cnt_apsell = $cnt_apsell + $result['upsell'];
	$cnt_sum = $cnt_sum + $result['summ'];
	$cnt_avg = $cnt_avg + $result['avg'];
?>
	<tr>
		<td><?=$manager?></td>			
		<td><?=$result['cnt_all'] > 0 ? $result['cnt_all'] : '';?></td>
                <td><?=$result['cnt_tp'] > 0 ? $result['cnt_tp'] : '';?></td>
		<td><?=$result['cnt_za'] >0 ? $result['cnt_za'] : '';?></td>
		<td><?=$result['cnt_zz'] >0 ? $result['cnt_zz'] : '';?></td>
		<td><?=($result['cnt_za'] >0) ? (round($result['cnt_zz']*100 / $result['cnt_za'],2)) : '' ?></td>
                <td><?=($result['osntovar'] >0) ? round($result['osntovar'],2) : '' ?></td>
                <td><?=($result['upsell'] >0) ? round($result['upsell'],2) : '' ?></td>
		<td><?=($result['summ'] >0) ? round($result['summ'],2) : '' ?></td>
		<td><?=($result['avg'] >0) ? round($result['avg'],2) : '' ?></td>
	</tr>	
<?php } //foreach?>
	<tr class='itog'>
		<th class="text-right">Итого</th>
		<th><?=$cnt_all;?></th>
                <th><?=$cnt_tp;?></th>
		<th><?=$cnt_za;?></th>
		<th><?=$cnt_zz;?></th>		
		<th><?=($cnt_za >0) ? round($cnt_zz * 100 / $cnt_za, 2) : ''?></th>
                <th><?=$cnt_osntovar;?></th>
                <th><?=$cnt_apsell;?></th>
		<th><?=$cnt_sum;?></th>
		<th><?=($cnt_za >0) ? round($cnt_sum / $cnt_zz,2) : '';?></th>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<?php } ?>