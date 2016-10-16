<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MoneyBalance */

$this->title = 'Update Money Balance: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Money Balances', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="money-balance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
