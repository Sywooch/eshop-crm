<?php

use yii\helpers\Html;
//use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Приход/расход денег';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать движуху', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'created_at:datetime',
            [
            	'attribute' => 'created_by',
            	'value' => 'user.fullname',
            ],
            'updated_at:datetime',            
            [
            	'attribute' => 'updated_by',
            	'value' => 'user.fullname',
            ],
            'date_at:date',
            'summa',
            [
            	'attribute' => 'item_id',
            	'value' => 'item.name',
            ],
            [
            	'attribute' => 'method_id',
            	'value' => 'method.name',
            ],
            [
            	'attribute'=>'type',
            	'value'=> function($model) {
            		return $model->itemAlias('type',$model->type);            		
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'type',
	                $searchModel->itemAlias('type'),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
