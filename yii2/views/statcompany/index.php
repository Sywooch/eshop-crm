<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StatcompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Statcompanies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statcompany-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Statcompany', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'date_at',
            'shows',
            'clicks',
            'costs',
            'id_company',
            //'category_id',
            [
	    		'attribute'=>'category_id',
	    		'value'=>'category.name',
	    		'filter' => Html::activeDropDownList(
		            $searchModel,
		            'category_id',
		            Category::find()->select(['name', 'id'])->where(['shop_id'=>Yii::$app->params['user.current_shop']])->indexBy('id')->column(),            
		            ['class' => 'form-control', 'prompt' => '']
		        ),
	    	],
            //'goods_art',
            //'tovar_id',
            //'tovar.name',
            [
	    		'attribute'=>'tovar.name',
	    		'value'=>'tovar.name',	    		
	    	],
            //'site_id',
            'host',
            // 'shop_id',
            //'source',
            [
            	'attribute'=>'source',
            	'value'=> function($model) {
            		return $model->itemAlias('source',$model->source);            		
            	},
            	'filter' => Html::activeDropDownList(
	                $searchModel,
	                'source',
	                $searchModel->itemAlias('source'),
	                ['class' => 'form-control', 'prompt' => '']
	            ),
        	],

            ['class' => 'yii\grid\ActionColumn', 'template'=>'{update}{delete}'],
        ],
    ]); ?>

</div>
