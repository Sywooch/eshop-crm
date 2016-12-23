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
            //'log_time:datetime',
            [
            	'attribute' => 'log_time',
            	'value' => function ($data) {
                	//$timeInSeconds = $data['log_time'] / 1000;
                	//$millisecondsDiff = (int) (($timeInSeconds - (int) $timeInSeconds) * 1000);

                	//return date('Y-m-d H:i:s.', $timeInSeconds) . sprintf('%03d', $millisecondsDiff);
                	//return Yii::$app->formatter->asDateTime(date('Y-m-d H:i:s', $data['log_time']));
                	return (date('Y-m-d H:i:s', $data['log_time']));
                	//return Yii::$app->formatter->asDateTime(Yii::$app->formatter->asTimestamp($data['log_time']));
            	},
            ],
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
