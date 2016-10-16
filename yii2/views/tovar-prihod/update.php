<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TovarPrihod */

$this->title = 'Update Tovar Prihod: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tovar Prihods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tovar-prihod-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
