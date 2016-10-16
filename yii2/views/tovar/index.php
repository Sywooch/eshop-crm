<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список товаров';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'artikul',
            'name',
            'created_at:datetime',
            'updated_at:datetime',
            'pprice',
            'price',            
            [
            	'attribute'=>'category_id',
            	'value'=> 'category.name',
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'category_id',
	                $mdlCategory,
	                ['class' => 'form-control','prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            [
            	'attribute'=>'active',
            	'value'=> function($model) {
            		$st=$model->itemAlias('active',$model->active);
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'active',
	                $searchModel->itemAlias('active'),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
