<?php use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */
?>
<h1>Загрузка общей статистики рекламных кампаний</h1>

<?
if(!empty($errors)) {
	print_r($errors);
}
if(empty($data)) {
?>
<p><?= Html::a('Загрузить', ['importstat'], ['class' => 'btn btn-success']) ?></p>
<? } else { 
//echo "<pre>";print_r($data);echo "</pre>";
?>

<h2>Добавлено:</h2>
<?= GridView::widget([
        'dataProvider' => $provider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'date_at',
            'id_company',
            'name',
            'shows',
            'clicks',
            'costs',
            //'goods_art',
            'category_id',
            //'site_id',
            'host',
            'tovar_id',
            'shop_id'

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); /*?>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>NN</th>
			<th>ID company</th>
			<th>Название</th>
			<th>Дата</th>
			<th>Показы</th>
			<th>Клики</th>
			<th>Расход</th>			
			<th>Артикул</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data as $n=>$item):?>
		<tr>
			<td><?=$item['id_company']; ?></td>	
			<td><?=$item['name']; ?></td>
			<td><?=$item['date_at']; ?></td>
			<td><?=$item['shows']; ?></td>
			<td><?=$item['clicks']; ?></td>
			<td><?=$item['costs']; ?></td>
			<td><?=$item['goods_art']; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<? */} ?>