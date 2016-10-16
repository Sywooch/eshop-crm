<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TovarCancelling */

$this->title = 'Update Tovar Cancelling: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tovar Cancellings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tovar-cancelling-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sklad_list' => $sklad_list,
        'tovar_list' => $tovar_list,
    ]) ?>

</div>
