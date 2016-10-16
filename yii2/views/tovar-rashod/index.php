<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarRashodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Расход товара';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-rashod-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?if(Yii::$app->user->can('tovar-rashod/cancelling') or Yii::$app->user->can('root')) { ?>    
    	<?= Html::a('Списание','cancelling',array('class'=>'btn btn-default')); ?>    
    <? } ?>
    
    <?if(Yii::$app->user->can('tovar-rashod/move') or Yii::$app->user->can('root')) { ?>    
    	<?= Html::a('Перемещение','move',array('class'=>'btn btn-default')); ?>    
    <? } ?>
     
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
	            'attribute' => 'created_at',
	            'format' => 'datetime',
	            'filter' => DatePicker::widget(
	                [
	                    'model' => $searchModel,
	                    'attribute' => 'created_at',
	                    'dateFormat' => 'yyyy-MM-dd',
	                    'options' => [
	                        'class' => 'form-control'
	                    ],
	                    'clientOptions' => [
	                        //'dateFormat' => 'yyyy-MM-dd',
	                    ]
	                ]),
           	],
           	[
	            'attribute' => 'updated_at',
	            'format' => 'datetime',
	            'filter' => DatePicker::widget(
	                [
	                    'model' => $searchModel,
	                    'attribute' => 'updated_at',
	                    'dateFormat' => 'yyyy-MM-dd',
	                    'options' => [
	                        'class' => 'form-control'
	                    ],
	                    'clientOptions' => [
	                        //'dateFormat' => 'yyyy-MM-dd',
	                    ]
	                ]),
           	],
           	[
           		'attribute' => 'order_id',
           		'format' => 'raw',
           		'value' => function ($model) {
           			$s = 'Заказ №'.$model->order_id . ' от ' . yii::$app->formatter->asDateTime($model->order->date_at);
           			return Html::a($s, Url::to(['orders/update', 'id' => $model->order_id]));
           		},
           	], 
           	'tovar_id',
           	'tovar.name',          
            /*[
            	'attribute' => 'tovar.name_id',
            	'value' => 'tovar.name',
            ],*/
            'price',
            'amount',
            [
            	'attribute' => 'sklad_id',
            	'value' => 'sklad.name',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
