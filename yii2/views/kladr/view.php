<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Kladr */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kladrs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kladr-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->code], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->code], [
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
            'name',
            'socr',
            'code',
            'index',
            'gninmb',
            'uno',
            'ocatd',
            'status',
        ],
    ]) ?>

</div>
