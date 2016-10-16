<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StatcompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анализ рекламы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advert-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?//= $form->field($model, 'date1')->fileInput(['class'=>'form-control']) ?>
    <?/*= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>
	<?= $form->field($model, 'date2')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) */?>
    <?//= $form->field($model, 'date_column')->dropdownList($model->itemAlias('date_column')) ?> 

    <button type="submit" class="btn btn-primary">Отобрать</button>

	<?php ActiveForm::end() ?>
	
	<p>&nbsp;</p>
	
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?/*= GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
    	['class' => 'yii\grid\SerialColumn'],
    	[
    		'label' => 'Наименование',
    		'attribute' => 'tovar_name',           
            'value' => function ($data) {
            	return '['.$data['tovar_id'].'] '.$data['tovar_name'];            	
            },
        ],
        [
        	'label' => 'ID',
        	'attribute' => 'id_company',
        ],
        [
    		'label' => 'Кампания',
    		'attribute' => 'name',            
        ],
        [
    		'label' => 'Показы',
    		'attribute' => 'shows',            
        ],
        [
    		'label' => 'Клики',
    		'attribute' => 'clicks',            
        ],
        [
    		'label' => 'Расход',
    		'attribute' => 'costs',            
        ],
        [
    		'label' => 'Сайт',
    		'attribute' => 'host',            
        ],
        [
    		'label' => 'Источник',
    		'attribute' => 'source',            
        ],
        [
    		'label' => 'Заявки',
    		'attribute' => 'cnt_zz',            
        ],
		[
    		'label' => 'Заказы',
    		'attribute' => 'cnt_zz',            
        ],
        //'after_sum_rashod',
    ]
]);*/?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'id_company',
            'name',
            'date_at',
            'shows',
            'clicks',
            'costs',            
            // 'category_id',
            // 'goods_art',
            // 'tovar_id',
            // 'site_id',
            'host',
            // 'shop_id',
            'source',
            //'cnt_za',
            //'utm.order',
            [
	            'label' => 'Заказ',
	    		//'attribute' => 'utm.order',           
	            'value' => function ($model, $key, $index, $column) {
	            	return print_r($column,true);//'['.$data['tovar_id'].'] '.$data['tovar_name'];
	            },
	        ]

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
