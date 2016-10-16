<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TovarCosts */

$this->title = 'Update Tovar Costs: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tovar Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tovar-costs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
