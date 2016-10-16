<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarBalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Остатки текущие';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-balance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'tovar_id',
            [
            	'attribute'=>'tovar.artikul',
            ],
            [
            	'attribute'=>'tovar_id',
            	/*'attribute'=>'tovar_id',
            	'format' => 'html',
            	'value' => function ($model) {
            		return '['.$model->tovar->artikul.'] '.$model->tovar->name;
            	}*/
            	'value'=>'tovar.name'
            ],
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
            //'sklad_id',
            //['attribute'=>'sklad_id', 'value'=>'sklad.name'],
            'amount',
            //'price',
            // 'created_at',
            'updated_at:datetime',
            // 'created_by',
            // 'updated_by',
            // 'shop_id',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
