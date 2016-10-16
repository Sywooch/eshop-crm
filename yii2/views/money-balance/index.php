<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MoneyBalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Money Balances';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-balance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Money Balance', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
            	'attribute' => 'method_id',
            	'value' => 'method.name',
            ],
            'summa', 
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
            [
            	'attribute' => 'money_id',
            	//'format' => 'date',
            	'value' => function ($model) {
            		return '['.$model->money->id.'] от '.yii::$app->formatter->asDate($model->money->date_at);
            	},
            ],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
