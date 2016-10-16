<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;

$this->title = 'Анализ по хостам';
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
    <?= $form->field($model, 'host') ?>
    
    <?= $form->field($model, 'rowTotal')->checkbox() ?>    

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(!empty($results)) { ?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Дата</th>
			<th>Регион</th>
			<th>Хост</th>
			<th>Клики ЯД</th>
			<th>Визиты всего</th>			
			<th>Заявки ЯД [дубли]</th>
			<th>Заявки всего [дубли]</th>
			<th>Конв. сайта</th>
			<th>Расход на ЯД</th>
			<th>Сумма заявок в руб</th>
			<th>Цена заявки ЯД</th>
			<th>Цена заявки всего</th>							
		</tr>
	</thead>
	<tbody>
<?	if($model->rowTotal == 1) {
		$cnt_za_ya = $cnt_za_ya_dub = 0;
		$cnt_all = $cnt_all_dub = 0;
		$cnt_clicks = 0;
		$cnt_costs = 0;
		$cnt_visits = 0;
		$cnt_sum = 0;
	}
	foreach($results as $rdate=>$result) {
		
		
		foreach($result as $rseg=>$res_1){
			if($model->rowTotal == 0) {
				$cnt_za_ya = $cnt_za_ya_dub = 0;
				$cnt_all = $cnt_all_dub = 0;
				$cnt_clicks = 0;
				$cnt_costs = 0;
				$cnt_visits = 0;
				$cnt_sum = 0;
			}
		
			foreach($res_1 as $url){
				
				if($url['url']=='' or is_null($url['url']) or $url['url']=='null') {
					$url['url'] = "<span style='font-style:italic'>Звонок и т.д.</span>";
					if(empty($url['cnt_all_dub'])) $url['cnt_all_dub'] = $url['cnt_all'];
				}
				$cnt_clicks = $cnt_clicks + $url['clicks'];
				$cnt_visits = $cnt_visits + $url['visits'];
				$cnt_za_ya = $cnt_za_ya + $url['cnt_za_ya'];
				$cnt_za_ya_dub = $cnt_za_ya_dub + $url['cnt_za_ya_dub'];
				$cnt_all = $cnt_all + $url['cnt_all'];
				$cnt_all_dub = $cnt_all_dub + $url['cnt_all_dub'];
				$cnt_costs = $cnt_costs + $url['costs'];
				$cnt_sum = $cnt_sum + $url['sum']
?>
	<tr>
		<td><?=$rdate?></td>
		<td><?=$rseg?></td>
		<td><?=$url['url']?></td>		
		<td><?=$url['clicks'];?></td>
		<td><?=$url['visits']; ?></td>
		<td><?=$url['cnt_za_ya'];?> <span style="color:#aaa">[<?=$url['cnt_za_ya_dub'];?>]</span></td>
		<td><?=$url['cnt_all'];?> <span style="color:#aaa">[<?=$url['cnt_all_dub'];?>]</span></td>		
		<td><?=($url['clicks'] >0) ? round($url['cnt_all']*100 / $url['clicks'],2) : ''?></td>
		<td><?=round($url['costs'],2); ?></td>
		<td><?=$url['sum']; ?></td>
		<td><?=($url['cnt_za_ya'] >0) ? round($url['costs'] / $url['cnt_za_ya'],2) : ''?></td>	
		<td><?=($url['cnt_all'] >0) ? round($url['costs'] / $url['cnt_all'],2) : ''?></td>
	</tr>	
<?			}
	//echo "<pre>";print_r($url);echo "</pre>\n";
			if($model->rowTotal == 0) {
?>
	<tr class='itog'>
		<th colspan="3" class="text-right">Итого по <?=$rseg?> за <?=$rdate?></th>
		<th><?=$cnt_clicks?></th>
		<th><?=$cnt_visits;?></th>
		<th><?=$cnt_za_ya?> <span style="color: #aaa">[<?=$cnt_za_ya_dub?>]</span></th>		
		<th><?=$cnt_all?> <span style="color: #aaa">[<?=$cnt_all_dub?>]</span></th>
		<th><?=($cnt_visits >0) ? round($cnt_all*100 / $cnt_visits,2) : ''?></th>
		<th><?=round($cnt_costs,2)?></th>
		<th><?=$cnt_sum?></th>
		<th><?=($cnt_za_ya >0) ? round(($cnt_costs / $cnt_za_ya),2) :''?></th>
		<th><?=($сnt_all >0) ? round(($cnt_costs / $cnt_all),2) : ''?></th>
	</tr>

<?			}
		}		
	}
	if($model->rowTotal == 1) { ?>
		
	<tr class='itog'>
		<th colspan="3" class="text-right">Итого за период</th>
		<th><?=$cnt_clicks?></th>
		<th><?=$cnt_visits;?></th>
		<th><?=$cnt_za_ya?> <span style="color: #aaa">[<?=$cnt_za_ya_dub?>]</span></th>		
		<th><?=$cnt_all?> <span style="color: #aaa">[<?=$cnt_all_dub?>]</span></th>
		<th><?=round($cnt_all*100 / $cnt_visits,2)?></th>			
		<th><?=round($cnt_costs,2)?></th>
		<th><?=$cnt_sum?></th>
		<th><?=round(($cnt_costs / $cnt_za_ya),2)?></th>
		<th><?=round(($cnt_costs / $cnt_all),2)?></th>
	</tr>
			
	<? }		
		
?>
	</tbody>
</table>
<? } ?>