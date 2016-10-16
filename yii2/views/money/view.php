<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Money */

$this->title = 'Приход/расход денег #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'created_at:datetime',
            [
            	'label' => $model->getAttributeLabel('created_by'),
            	'value' => $model->user->fullname,
        	],            
            'updated_at:datetime',
            [
            	'label' => $model->getAttributeLabel('updated_by'),
            	'value' => $model->user->fullname,
        	],           
            'summa',
            [
            	'label' => $model->getAttributeLabel('item_id'),
            	'value' => $model->item->name,
        	],
        	[
            	'label' => $model->getAttributeLabel('method_id'),
            	'value' => $model->method->name,
        	],
        	[
            	'label' => $model->getAttributeLabel('type'),
            	'value' => $model->itemAlias('type', $model->type),
        	],
            'note:ntext',
        ],
    ]) ?>

</div>
