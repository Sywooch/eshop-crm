<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <? if (Yii::$app->user->can('root')) echo Html::a('Delete', ['delete', 'id' => $model->id], [
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
            [
            	'label' => $model->getAttributeLabel('status'),
            	'value' => $model->itemAlias('status', $model->status),
        	],
        	[
            	'label' => $model->getAttributeLabel('otpravlen'),
            	'value' => $model->itemAlias('otpravlen', $model->otpravlen),
        	],
        	'data_otprav:datetime',
        	[
            	'label' => $model->getAttributeLabel('dostavlen'),
            	'value' => $model->itemAlias('dostavlen', $model->dostavlen),
        	],
        	[
            	'label' => $model->getAttributeLabel('oplachen'),
            	'value' => $model->itemAlias('oplachen', $model->oplachen),
        	],
        	[
            	'label' => $model->getAttributeLabel('vkasse'),
            	'value' => $model->itemAlias('vkasse', $model->vkasse),
        	],
        	[
            	'label' => $model->getAttributeLabel('vozvrat'),
            	'value' => $model->itemAlias('vozvrat', $model->vozvrat),
        	],
        	'vozvrat_cost',        	            
            'prich_vozvrat:ntext',
            'summaotp',
            'discount',
            'identif',
            [
            	'label' => $model->getAttributeLabel('dostavza'),
            	'value' => $model->itemAlias('dostavza', $model->dostavza),
        	],            
            [
            	'label' => $model->getAttributeLabel('manager_id'),
            	'value' => $model->manager->fullname,
        	],
        	[
            	'label' => $model->getAttributeLabel('category_id'),
            	'value' => $model->category->name,
        	],
        	[
            	'label' => $model->getAttributeLabel('fast'),
            	'value' => $model->itemAlias('fast', $model->fast),
        	],
        	[
            	'label' => $model->getAttributeLabel('packer_id'),
            	'value' => $model->packer->fullname,
        	],
            'url',
            [
            	'label' => $model->getAttributeLabel('client_id'),
            	'value' => $model->client->fio. ', '.$model->client->address,
        	],
            'tclient',
            'note:ntext',
        ],
    ]) ?>

</div>
