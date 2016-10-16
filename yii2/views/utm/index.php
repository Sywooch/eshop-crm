<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UtmSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Utm Labels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utm-label-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Utm Label', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'utm_campaign',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
