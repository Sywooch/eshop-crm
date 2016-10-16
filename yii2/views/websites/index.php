<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WebsitesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Websites';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="websites-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Websites', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'host',
            //'created_at',
            //'updated_at',
            //'created_by',
            // 'updated_by',
            'active',
            'category_id',
            'tovar_id',
            // 'shop_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
