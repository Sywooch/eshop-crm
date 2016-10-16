<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Statcompany */

$this->title = 'Анализ кампании';
$this->params['breadcrumbs'][] = ['label' => 'Statcompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statcompany-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']],'method'=>'get', 'action'=>['/advert/campaign','idc'=>$idc]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?//= $form->field($model, 'date1')->fileInput(['class'=>'form-control']) ?>
    <?= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>
	<?= $form->field($model, 'date2')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>
    <?= $form->field($model, 'campaign') ?> 

    <button type="submit" class="btn btn-primary">Отобрать</button>

	<?php ActiveForm::end() ?>

	<p>&nbsp;</p>
	
	<?= GridView::widget([
    'dataProvider' => $provider,
    'layout' => "{items}",
    'columns' => [
    	//['label'=>'Дата', 'value'=>function($data) {return $data['date1'].' / '.$data['date2'];}],    	
        [
        	'label' => 'ID',
        	'attribute' => 'id_company',        	
        ],
        [
    		'label' => 'Кампания',
    		'attribute' => 'name',            
        ],
        [
    		'label' => 'Показы',
    		'attribute' => 'shows',            
        ],
        [
    		'label' => 'Клики',
    		'attribute' => 'clicks',            
        ],
        [
    		'label' => 'Расход',
    		'attribute' => 'costs',            
        ],
        [
    		'label' => 'Сайт',
    		'attribute' => 'host',
        ],
        [
    		'label' => 'Источник',
    		'attribute' => 'source',
        ],
        [
    		'label' => 'Заявки',
    		'attribute' => 'cnt_za',
    		'format' => 'raw',
    		'value' => function ($model, $key, $index, $column) {
    			$p = 0;
    			$p = ($model['costs'] >0 and $model['cnt_za'] >0) ? round($model['costs']/$model['cnt_za'],2) : $p;
	            return $model['cnt_za'].' <span style="color: #aaa"> /'.$p.'</span>';
	        },
        ],
		[
    		'label' => 'Заказы',
    		'attribute' => 'cnt_zz',
    		'format' => 'raw',
    		'value' => function ($model, $key, $index, $column) {
    			$p = 0;
    			$p = ($model['costs'] >0 and $model['cnt_zz'] >0) ? round($model['costs']/$model['cnt_zz'],2) : $p;
	            return $model['cnt_zz'].' <span style="color: #aaa"> /'.$p.'</span>';
	        },
        ],
        //'after_sum_rashod',
    ]
    ]);?>

	<? Pjax::begin(['enablePushState' => false]); ?>
	<?= GridView::widget([		 				
        'dataProvider' => $dataProvider,//new \yii\data\ActiveDataProvider(['query' => $model->getUtmLabel()]),
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           
           [
           	'attribute'=>'order.status',
           	'value'=>function($data) {
           		//return print_r($data->order->status);
           		return \app\models\Orders::itemAlias('status',$data->order->status);
           	}],            
            'utm_content:ntext',
            'utm_source',
            'utm_medium',
            'utm_term',
            'source_type',
            'source',
            //'group_id',
            'banner_id',
            'position',
            'position_type',
            'region_name',
            'device',
        ],
    ]); ?>
    <? Pjax::end(); ?>

</div>
