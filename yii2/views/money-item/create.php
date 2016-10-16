<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MoneyItem */

$this->title = 'Создать статью прихода/расхода';
$this->params['breadcrumbs'][] = ['label' => 'Money Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
