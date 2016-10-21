<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Выбрать товар для добавления';
$this->params['breadcrumbs'][] = $this->title;

//\yii\helpers\VarDumper::dump($_REQUEST,5,true);
?>
<div class="tovar-popup">

    <h1><?= Html::encode($this->title) ?></h1>

	<? //Pjax::begin(['enablePushState' => false]);// ?> 
	
	<?//default sklad
	//$searchModel['s_id'] = \app\models\Sklad::defaultId();
	//echo \app\models\Sklad::defaultId();
	?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
        	[
            	'class' => 'yii\grid\ActionColumn',
            	'buttons' => ['add_to_order' => function ($url, $model) {return '<button type="button" class="add_to_order" title="Добавить в заявку"><span class="glyphicon glyphicon-ok"></span></button>';}],
            	'template' => '{add_to_order}',
            ],
            [
            	'attribute'=>'t_art',
            	'label'=>$searchModel->getAttributeLabel('t_art'),
            ],           	
            [
            	'attribute'=>'t_name',
            	'format'=>'raw',
            	'label'=>$searchModel->getAttributeLabel('t_name'),
            	'value'=> function($model, $key, $index, $column) {
            		$st = $model['t_name']."<input type='hidden' class='tovar_id' value='".$model['t_id']."' />";
            		return $st;
            	},
            	'contentOptions' => ['class' => 'name'],
            ],            
            [
            	'attribute'=>'t_price',
            	'label'=>$searchModel->getAttributeLabel('t_price'),
            	'contentOptions' => ['class' => 'price'],
            ],
            [
            	'attribute'=>'cat_name',
            	'label'=>$searchModel->getAttributeLabel('cat_name'),
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'cat_id',
	                $category,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),	            
        	],
        	//['attribute'=>'ostatok','label'=>$searchModel->getAttributeLabel('ostatok')],        	
        	[
            	'attribute'=>'s_name',
            	'label'=>$searchModel->getAttributeLabel('s_name'),
            	'format'=>'raw',
            	'value'=> function($model, $key, $index, $column) {            		
            		$st = $model['s_name']."<input type='hidden' class='sklad_id' value='".$model['s_id']."' />";
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                's_id',
	                $sklad,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
	            'contentOptions' => ['class' => 'sklad_name'],
        	],            
        ],        
    ]); ?>

	<? //Pjax::end(); ?>

<?$this->registerJsFile('/lib/inventory.js', ['depends' => 'yii\web\JqueryAsset']); ?>
</div>
