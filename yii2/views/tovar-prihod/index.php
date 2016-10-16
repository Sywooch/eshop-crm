<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarPrihodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приход товара';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-prihod-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить приход товара', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
	                ]
	            )
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
	                ]
	            )
	    	],
	    	[
	    		'attribute' => 'tovar_id',
            	'value' => 'tovar_id',
            	'label' => 'Товар ID'
            ],
            [
            	'attribute' => 'tovar.name',
            	'value' => 'tovar.name',
            	'label' => 'Товар'
            ],
            'date_at:datetime',
            'price',
            'price_sale',
            'amount',
            'supplier_id',
            [
            	'attribute' => 'sklad_id',
            	'value' => 'sklad.name',
            ],
            'doc',
            'note:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
