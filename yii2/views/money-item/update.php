<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MoneyItem */

$this->title = 'Изменить статью прихода/расхода: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Money Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="money-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
