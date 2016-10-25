<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Statmetrikas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statmetrika-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'date_at',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'host',
            'label:ntext',
            'visits',
            'page_views',
            'new_visitors',
            'denial',
            'depth',
            'visit_time:datetime',
        ],
    ]) ?>

</div>
