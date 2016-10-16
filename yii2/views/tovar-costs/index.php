<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TovarCostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tovar Costs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-costs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tovar Costs', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tovar_id',
            'cost',
            'current',
            'active',
            // 'note',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
