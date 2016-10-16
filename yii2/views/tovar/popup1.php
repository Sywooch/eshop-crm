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
            	'attribute'=>'tovar.artikul',
            ],
            [
            	'attribute'=>'tovar_id',
            	/*'attribute'=>'tovar_id',
            	'format' => 'html',
            	'value' => function ($model) {
            		return '['.$model->tovar->artikul.'] '.$model->tovar->name;
            	}*/
            	'value'=>'tovar.name'
            ],
            'tovar.price',
            'amount',
            [
            	'attribute'=>'sklad_id',
            	'value'=>'sklad.name',
            	//'label'=>$searchModel->getAttributeLabel('s_name'),
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'sklad_id',
	                $sklad,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            
        ],
    ]); ?>

	<? //Pjax::end(); ?>

<?$this->registerJsFile('/lib/invent.js', ['depends' => 'yii\web\JqueryAsset']); ?>
</div>
