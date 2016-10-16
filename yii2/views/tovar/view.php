<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tovar */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tovars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Правда-правда удалить? Пожалеете!',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'artikul',
            'name',
            'created_at:datetime',
            'price',
            [                      // name свойство зависимой модели owner
            	'label' => $model->getAttributeLabel('category_id'),
            	'value' => $model->category->name,
        	],     
            [                      // name свойство зависимой модели owner
            	'label' => $model->getAttributeLabel('active'),
            	'value' => $model->itemAlias('active',$model->active),
        	],
        ],
    ]) ?>

</div>
