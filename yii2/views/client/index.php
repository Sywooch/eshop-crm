<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Список клиентов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать клиента', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	<? $action_column = [];
	$action_column['class'] = 'yii\grid\ActionColumn';
	$action_column['buttons'] = [
	  	'order' => function ($url, $model) {
	  		return Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['orders/create', 'client_id' => $model->id]), ['title' => 'Создать заявку']);
	  	},
	];
	if (Yii::$app->user->can('dpt_head')) 
		$action_column['template'] = '{order}&nbsp;&nbsp;{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}';
	else 
		$action_column['template'] = '{order}&nbsp;&nbsp;{update}&nbsp;&nbsp;{view}';
	?>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fio',
            'phone',
            'email:email',
            'address:ntext',
            // 'ident:ntext',
            // 'note:ntext',

            $action_column
        ],
    ]); ?>

</div>
