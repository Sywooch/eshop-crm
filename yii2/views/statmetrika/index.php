<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StatmetrikaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Statmetrikas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statmetrika-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Statmetrika', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date_at',
            'created_at',
            'created_by',
            'updated_at',
            // 'updated_by',
            // 'host',
            // 'label:ntext',
            // 'visits',
            // 'page_views',
            // 'new_visitors',
            // 'denial',
            // 'depth',
            // 'visit_time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
