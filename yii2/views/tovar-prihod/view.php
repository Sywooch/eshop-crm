<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TovarPrihod */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tovar Prihods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-prihod-view">

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
            'created_at:datetime',
            'updated_at:datetime',
            [
            	'label' => $model->getAttributeLabel('tovar_id'),
            	'value' => $model->tovar->name,
            ],
            'date_at:date',
            'price',
            'price_sale',
            'amount',
            'supplier_id',
            [
            	'label' => $model->getAttributeLabel('sklad_id'),
            	'value' => $model->sklad->name,
            ],
            'doc',
            'note:ntext',
        ],
    ]) ?>

</div>
