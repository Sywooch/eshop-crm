<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-view">

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
            'created_at:datetime',
            'created_by',
            'updated_at:datetime',
            'updated_by',
            'status',
            'data_duble',
            'otpravlen',
            'data_otprav',
            'sender_id',
            'dostavlen',
            'data_dostav',
            'oplachen',
            'data_oplata',
            'vkasse',
            'data_vkasse',
            'vozvrat',
            'data_vozvrat',
            'vozvrat_cost',
            'prich_double:ntext',
            'prich_vozvrat:ntext',
            'summaotp',
            'discount',
            'identif',
            'dostavza',
            'manager_id',
            'category_id',
            'fast',
            'packer_id',
            'url:url',
            'client_id',
            'tclient',
            'note:ntext',
            'ip_address',
            'type_oplata',
            'sklad',
            'source',
            'shop_id',
            'old_id',
            'old_id2',
        ],
    ]) ?>

</div>
