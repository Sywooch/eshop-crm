<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = 'Клиент #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Создать заявку', ['orders/create', 'client_id' => $model->id], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fio',
            'phone',
            'email:email',
            ['label' => $model->getAttributeLabel('region_id'),'value' => $model->region->kname,],
            ['label' => $model->getAttributeLabel('area_id'),'value' => $model->area->kname,],
            ['label' => $model->getAttributeLabel('city_id'),'value' => $model->city->kname,],
            ['label' => $model->getAttributeLabel('settlement_id'),'value' => $model->settlement->kname,],
            'address:ntext',
            'ident:ntext',
            'note:ntext',
        ],
    ]) ?>
	
	<h3>Заявки клиента</h3>
	
	<?echo GridView::widget([
        'dataProvider' => $dataProvider,//$model->orders,
        'filterModel' => $searchModel,
        //'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''], 
        'pager' => ['maxButtonCount'=>999],			
        'columns' => [
        	['class' => '\yii\grid\SerialColumn'],
        	'id',
        	'date_at' => [
	            'attribute' => 'date_at',
	            'format' => 'datetime',
	            'filter' => DatePicker::widget(
	                [
	                    'model' => $searchModel,
	                    'attribute' => 'date_at',
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
        	'status' => [
            	'attribute'=>'status',
            	'value'=> function($model) {
            		$st=$model->itemAlias('status',$model->status);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'status',
	                $searchModel->itemAlias('status'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
        	],
            'otpravlen' => [
            	'attribute'=>'otpravlen',
            	'value'=> function($model) {
            		$st=$model->itemAlias('otpravlen',$model->otpravlen);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'otpravlen',
	                $searchModel->itemAlias('otpravlen'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
        	],
            'dostavlen' => [
            	'attribute'=>'dostavlen',
            	'value'=> function($model) {
            		$st=$model->itemAlias('dostavlen',$model->dostavlen);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'dostavlen',
	                $searchModel->itemAlias('dostavlen'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
       		],
            'oplachen' =>[
            	'attribute'=>'oplachen',
            	'value'=> function($model) {
            		$st=$model->itemAlias('oplachen',$model->oplachen);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'oplachen',
	                $searchModel->itemAlias('oplachen'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
        	],
            'vkasse' => [
            	'attribute'=>'vkasse',
            	'value'=> function($model) {
            		$st=$model->itemAlias('vkasse',$model->vkasse);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'vkasse',
	                $searchModel->itemAlias('vkasse'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
        	],
            'vozvrat' => [
            	'attribute'=>'vozvrat',
            	'value'=> function($model) {
            		$st=$model->itemAlias('vozvrat',$model->vozvrat);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'vozvrat',
	                $searchModel->itemAlias('vozvrat'),
	                ['class' => 'form-control', 'prompt' => '-All-']
	            ),
        	],
            // 'vozvrat_cost',
            // 'prich_double:ntext',
            // 'prich_vozvrat:ntext',
            // 'summaotp',
            // 'discount',
            // 'identif',
            // 'dostavza',            
            // 'category',
            // 'fast',            
            // 'url:url',            
            // 'tclient',
            [
            	'class' => 'yii\grid\ActionColumn',
            	'template' => '{update}',
            	'buttons' => [
            		'update' => function ($url, $model, $key) {
	  					return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['orders/update', 'id'=>$model->id, 'client_id' => $model->client_id]), ['title' => 'Изменить заявку']);
	  				},
    			],
         	],
        ]
    ]); ?>

</div>
