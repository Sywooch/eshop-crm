<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заявки от клиента #'.$model->client_id. ' ('.$model->client->fio.', '.$model->client->phone.')';
$this->params['breadcrumbs'][] = ['label' => 'Заявки', 'url' => ['index']];
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать заявку', ['create','client_id'=>$model->client_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''], 
        'pager' => ['maxButtonCount'=>999],			
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date_at:datetime',
            //'status',
            [
            	'attribute'=>'status',
            	'value'=> function($model) {
            		$st=$model->itemAlias('status',$model->status);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
                $searchModel,
                'status',
                $model->itemAlias('status'),
                ['class' => 'form-control', 'prompt' => '-All-']
            ),
            ],
            //'duplicate',
           /* [
            	'attribute'=>'dublicate',
            	'value'=> function($model) {            	
            		$dd = $model->dublicate;
            		$dd = (is_null($dd)) ? '0' : '1';
            		return $model->itemAlias('dublicate',$dd);
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'dublicate',
	                $searchModel->itemAlias('dublicate'),
	                ['class' => 'form-control', 'prompt' => '-All-']
                ),
            ],*/
            'otpravlen',
            [
                'attribute'=>'manager_id',
                'value'=>'manager.fullname',
              /*  'value'=>function ($model) {
                    return $model->manager->name;
                }*/
            ],            
            [
                'attribute'=>'packer_id',   
                'value'=>'packer.fullname',
               /* 'value'=>function ($model) {
                    return $model->packer->name;
                }*/
            ],
            'dostavlen',
            'oplachen',
            // 'vkasse',
            'vozvrat',
            // 'vozvrat_cost',
            // 'prich_double:ntext',
            // 'prich_vozvrat:ntext',
            // 'summaotp',
            // 'discount',
            // 'identif',
            // 'dostavza',            
            // 'category',
            // 'fast',
            // 'packer_id',
            'url',            
            // 'tclient',
             'note:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}'
            ]
        ],
    ]); ?>

</div>
