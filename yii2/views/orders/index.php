<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use app\models\Senders;
use app\models\OrderSearch;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список заявок';

?>
<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>  

    <p>
    	<?= Html::a('Создать заявку', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Фильтр по данным','#',array('class'=>'search-button btn btn-default')); ?>
        <?= Html::a('Фильтр по колонкам','#',array('class'=>'column-button btn btn-default')); ?> 
        <?= (Yii::$app->user->can('utils/exportmsk') or Yii::$app->user->can('utils')) ? Html::a('Экспорт в Москву',['utils/exportmsk'],array('class'=>'btn btn-default')) : ''?>
    </p>
    
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_column', ['model' => $searchModel]); ?>
    
    <?php $columns[] = ['class' => '\yii\grid\SerialColumn'];  
	foreach ($searchModel->column_visible as $key) {
		if($key=='id')  $columns[] = 'id';
		elseif($key=='date_at') $columns[] = [
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
    	];
    	elseif($key=='data_duble') $columns[] = [
            'attribute' => 'data_duble',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_duble',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='data_otprav') $columns[] = [
            'attribute' => 'data_otprav',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_otprav',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='send_moskva') $columns[] = [
            	'attribute'=>'send_moskva',
            	'value'=> function($model) {
            		$st=$model->itemAlias('send_moskva',$model->send_moskva);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'send_moskva',
	                $searchModel->itemAlias('send_moskva'),
	                ['class' => 'form-control', 'prompt' => '']
	            ),
        ];
    	elseif($key=='data_dostav') $columns[] = [
            'attribute' => 'data_dostav',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_dostav',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='data_oplata') $columns[] = [
            'attribute' => 'data_oplata',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_oplata',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='data_vkasse') $columns[] = [
            'attribute' => 'data_vkasse',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_vkasse',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='data_vozvrat') $columns[] = [
            'attribute' => 'data_vozvrat',
            'format' => 'date',
            'filter' => DatePicker::widget(
                [
                    'model' => $searchModel,
                    'attribute' => 'data_vozvrat',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ]
            )
    	];
    	elseif($key=='status') $columns[] = [
            	'attribute'=>'status',
            	'value'=> function($model) {
            		$st=$model->itemAlias('status',$model->status);
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'status',
	                $searchModel->itemAlias('status'),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        ];
        elseif($key=='identif') $columns[] = [
            'attribute' => 'identif',
            'format' => 'html',
            'value' => function ($model) {
            	$sender = $model->sender->code;            	
            	if($sender == 'russianpost') return "<a target='_blank' href='https://www.pochta.ru/tracking#{$model->identif}'>{$model->identif}</a>";
            	elseif($sender == 'cdek') return '<a href="http://www.edostavka.ru/track.html?order_id='.$model->identif.'" target="_blank">'.$model->identif.'</a>';
            	else return $model->identif;
            },                      
    	];
        elseif($key=='otpravlen') $columns[] = [
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
        ];
        elseif($key=='dostavlen') $columns[] = [
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
        ];
        elseif($key=='oplachen') $columns[] = [
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
        ];
        elseif($key=='vkasse') $columns[] = [
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
        ];
        elseif($key=='vozvrat') $columns[] = [
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
        ];
       elseif($key=='client_id') $columns[] = 'client_id'; 
       /*[
            'attribute' => 'client_id',
            'format' => 'html',
            'value' => function ($model) {
            	$phone = $model->client->phone;
            	$n = $model->countRegular();
            	if($n>1) return Html::a($phone, ['client', 'id' => $model->client_id], ['data-pjax' => 0]).' -'.$model->client_id;
            	else return $phone.' -'.$model->client_id;
            },            
    	];*/
		elseif($key=='client.phone') $columns[] = //'client.phone'; 
       [
            'attribute' => 'client.phone',
            'format' => 'raw',
            'value' => function ($model) {
            	$phone = $model->client->phone;
            	$n = $model->countRegular();
            	if($n>1) $r = Html::a($phone, ['client/view', 'id' => $model['client_id']], ['data-pjax' => 0]);//.' -'.$model->client_id;
            	else $r = $phone;//.' -'.$model->client_id;            	
            	return $r.' <a class="btn btn-default" href="tel:'.$phone.'"><span class="glyphicon glyphicon-earphone"></span></a>';
            },                      
    	];
    	elseif($key=='sender_id') $columns[] = [
    		'attribute'=>'sender_id',
    		'value'=>'sender.name',
    		'filter' => Html::activeDropDownList(
	            $searchModel,
	            'sender_id',
	            Senders::find()->select(['name', 'id'])->indexBy('id')->column(),            
	            ['class' => 'form-control', 'prompt' => '']
	        ),
    	];
    	elseif($key=='totalSumm') $columns[] = [// 'totalSumm';
    		'attribute' =>'totalSumm',
    		'value' => function ($model) {return $model->tovarSumma;},
    		'footer' => OrderSearch::getTotalSumma($dataProvider->models, 'summ'),
    	];
    	elseif($key=='prich_double') $columns[] = [
            	'attribute'=>'prich_double',
            	'value'=> function($model) {
            		$st=$model->itemAlias('prich_double',$model->prich_double);
            		return $st;
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'prich_double',
	                $searchModel->itemAlias('prich_double'),
	                ['class' => 'form-control', 'prompt' => '']
	            ),
        ];
        elseif($key=='b2c_id') $columns[] = 'b2c_id';
    	elseif($key=='client.fio') $columns[] = ['attribute'=>'client.fio','value'=>'client.fio',];
    	elseif($key=='client.region_id') $columns[] = ['attribute'=>'client.region_id','value'=>'client.region.kname',];
    	elseif($key=='client.area_id') $columns[] = ['attribute'=>'client.area_id','value'=>'client.area.name',];
    	elseif($key=='client.city_id') $columns[] = ['attribute'=>'client.city_id','value'=>'client.city.name',];
    	elseif($key=='client.settlement_id') $columns[] = ['attribute'=>'client.settlement_id','value'=>'client.settlement.name',];
    	elseif($key=='client.fulladdress') $columns[] = ['attribute'=>'client.fulladdress','value'=>function ($model) {return $model->client->getFulladdress();},];
    	elseif($key=='client.email') $columns[] = ['attribute'=>'client.email','value'=>'client.email',];
    	elseif($key=='manager_id') $columns[] = ['attribute'=>'manager_id', 'value'=>'manager.fullname',];    	
    	elseif($key=='packer_id') $columns[] =  ['attribute'=>'packer_id', 'value'=>'packer.fullname',];
        elseif($key=='old_id') $columns[] = 'old_id';
        elseif($key=='old_id2') $columns[] = 'old_id2';
        elseif($key=='sklad') $columns[] = 'sklad';
        elseif($key=='type_oplata') $columns[] = 'type_oplata';
        elseif($key=='ip_address') $columns[] = 'ip_address';
        elseif($key=='url') $columns[] = 'url';
        elseif($key=='note') $columns[] = 'note:ntext';
        //elseif($key=='prich_double') $columns[] = 'prich_double:ntext';
        elseif($key=='utm_term') $columns[] = ['attribute'=>'utmLabel.utm_term', 'value'=>'utmLabel.utm_term',];
        elseif($key=='utm_content') $columns[] = ['attribute'=>'utmLabel.utm_content', 'value'=>'utmLabel.utm_content',];
        elseif($key=='utm_campaign') $columns[] = ['attribute'=>'utmLabel.utm_campaign', 'value'=>'utmLabel.utm_campaign',];
        elseif($key=='utm_source') $columns[] = ['attribute'=>'utmLabel.utm_source', 'value'=>'utmLabel.utm_source',];
        elseif($key=='utm_medium') $columns[] = ['attribute'=>'utmLabel.utm_medium', 'value'=>'utmLabel.utm_medium',];        
        elseif($key=='source_type') $columns[] = ['attribute'=>'utmLabel.source_type', 'value'=>'utmLabel.source_type',];        
        elseif($key=='source') $columns[] = ['attribute'=>'utmLabel.source', 'value'=>'utmLabel.source',];
        elseif($key=='group_id') $columns[] = ['attribute'=>'utmLabel.group_id', 'value'=>'utmLabel.group_id',];
        elseif($key=='banner_id') $columns[] = ['attribute'=>'utmLabel.banner_id', 'value'=>'utmLabel.banner_id',];
        elseif($key=='position') $columns[] = ['attribute'=>'utmLabel.position', 'value'=>'utmLabel.position',];
        elseif($key=='position_type') $columns[] = ['attribute'=>'utmLabel.position_type', 'value'=>'utmLabel.position_type',];
        elseif($key=='region_name') $columns[] = ['attribute'=>'utmLabel.region_name', 'value'=>'utmLabel.region_name',];        
	}
	$columns[] = ['class' => 'yii\grid\ActionColumn',
		'template'=>'{view}&nbsp;{update}&nbsp;{116}&nbsp;{112}&nbsp;{7p}&nbsp;{sms}',
		'buttons' => [
	         '116' => function ($url, $model) {
	         	return Html::a('116', Url::to(['/orders/f116/', 'id' => $model->id]), [
	            	'title' => 'Форма 116'
	            ]);
	        },
	        '112' => function ($url, $model) {
	         	return Html::a('112', Url::to(['/orders/f112/', 'id' => $model->id]), [
	            	'title' => 'Форма 112'
	            ]);
	        },
	        '7p' => function ($url, $model) {
	         	return Html::a('Ф7П', Url::to(['/orders/f7p/', 'id' => $model->id]), [
	            	'title' => 'Форма Ф7П'
	            ]);
	        },
	        'sms' => function ($url, $model) {
	         	return Html::a('SMS', Url::to(['/orders/sms/', 'id' => $model->id]), [
	            	'title' => 'Форма SMS для почты'
	            ]);
	        },
	    ]        
    ];
	
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter' => true,
        //'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''], 
        'pager' => ['maxButtonCount'=>30],			
        'columns' => $columns,
        	//['id','client.phone','utmLabel.utm_term'],
            //'otpravlen',
            // 'dostavlen',
            // 'oplachen',
            // 'vkasse',
            // 'vozvrat',
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
             

        
    ]); ?>

</div>
