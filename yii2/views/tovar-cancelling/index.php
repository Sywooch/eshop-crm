<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarCancellingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Списанные товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-cancelling-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Списать товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'created_at',
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
	                ]
	            )
	    	],
            //'created_by',
            //'updated_at',
            //'updated_by',
            // 'tovar_id',
            [
            	'attribute'=>'tovar.artikul',
            ],
            [
            	'attribute'=>'tovar_id',
            	/*'format' => 'html',
            	'value' => function ($model) {return '['.$model->tovar->artikul.'] '.$model->tovar->name;}*/
            	'value'=>'tovar.name',
            	//'label'=>$searchModel->getAttributeLabel('s_name'),
            	/*'filter' => Html::activedropDownList(
	                $searchModel,
	                'tovar_id',
	                $tovar_list,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),*/
        	],
            // 'price',
            'amount',
            // 'sklad_id',
            [
            	'attribute'=>'sklad_id',
            	'value'=>'sklad.name',
            	//'label'=>$searchModel->getAttributeLabel('s_name'),
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'sklad_id',
	                $sklad_list,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            'reason:ntext',
            // 'shop_id',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
