<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Смс';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?if(Yii::$app->user->can('sms/config') or Yii::$app->user->can('root')) { ?>
    
    <?= Html::a('Настройка','#',array('class'=>'sms-button btn btn-default')); ?>    
   
    <?php echo $this->render('_config', ['model' => $searchModel]); ?>
    
    <? } ?>
    
    <?if(Yii::$app->user->can('smsMailing')){// or Yii::$app->user->can('root')) { ?>    
    	<?= Html::a('Рассылка','mailing',array('class'=>'btn btn-default')); ?>    
    <? } ?>
	
	<p>&nbsp;</p>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            //'created_at:datetime',
            [
	            'attribute' => 'created_at',
	            'format' => 'date',
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
            'sms_id',
            'phone',
            'order_id',
            [
            	'attribute'=>'event',
            	'value'=> function($model) {
            		$st=$model->itemAlias('event',$model->event);
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'event',
	                $searchModel->itemAlias('event'),
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            //'event',
            'status',
            'cost',
            'msg:ntext',
            //'note:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
