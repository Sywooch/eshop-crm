<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Log;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'level',            
            'log_time:datetime',
            [
	    		'attribute'=>'category',
	    		'value'=>'category',
	    		'filter' => Html::activeDropDownList(
		            $searchModel,
		            'category',
		            Log::find()->select('category, category as id')->groupBy('category')->indexBy('id')->column(),            
		            ['class' => 'form-control', 'prompt' => '']
		        ),
	    	],
            //'category',
            //'prefix:ntext',
            'message:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
