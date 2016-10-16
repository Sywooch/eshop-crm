<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\TotalCount;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Остаток товаров';
$this->params['breadcrumbs'][] = $this->title;
//\yii\helpers\VarDumper::dump($dataProvider->getModels(),5,true);
?>
<div class="tovar-ostatok">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php /*$form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?//= $form->field($model, 'date1')->fileInput(['class'=>'form-control']) ?>
    <?= $form->field($searchModel, 's_id')->dropdownList($sklad, ['prompt'=>'']);?>
	<?= $form->field($searchModel, 'cat_id')->dropdownList($category, ['prompt'=>'']);?>
    

    <button type="submit" class="btn btn-primary">Отобрать</button>

	<?php ActiveForm::end() */?>
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'showFooter'=>TRUE,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            [
            	'attribute'=>'t_id',
            	'label'=>$searchModel->getAttributeLabel('t_id'),            	
            ],
            [
            	'attribute'=>'t_name',
            	'label'=>$searchModel->getAttributeLabel('t_name'),
            	'footer'=>'ИТОГО',
            	'footerOptions'=>['class'=>'text-right', 'style'=>'font-weight: bold;'],
            ],
            ['attribute'=>'t_price','label'=>$searchModel->getAttributeLabel('t_price')],
            [
            	'attribute'=>'ostatok',
            	'label'=>$searchModel->getAttributeLabel('ostatok'),
            	'footer' => TotalCount::pageTotal($dataProvider->models,'ostatok'),
            	'footerOptions'=>['style'=>'font-weight: bold;'],
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
        	[
            	'attribute'=>'s_name',
            	'label'=>$searchModel->getAttributeLabel('s_name'),
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                's_id',
	                $sklad,
	                ['class' => 'form-control', 'prompt' => '']//'value' => 'OrderSearch[status][]'
	            ),
        	],
/*
            'id',
            'artikul',
            'name',
            'created_at:datetime',
            'updated_at:datetime',
            'price',
            [
            	'attribute'=>'category_id',
            	'value'=> 'category.name',
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'category_id',
	                $mdlCategory::find()->select(['name', 'id'])->indexBy('id')->column(),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            [
            	'attribute'=>'active',
            	'value'=> function($model) {
            		$st=$model->itemAlias('active',$model->active);
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $searchModel,
	                'active',
	                $searchModel->itemAlias('active'),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],

            ['class' => 'yii\grid\ActionColumn'],
*/            
        ],
    ]); ?>

</div>
