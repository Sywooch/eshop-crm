<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Прайс';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="price-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить в прайс', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
            	'attribute'=>'tovar_id',
            	'value'=> function($model) {return $model->tovar->name;},            	
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'tovar_id',
	                ArrayHelper::map(\app\models\Tovar::find()->all(), 'id', 'name'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
            ],
            'name',
            'artikul',            
            'price',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
