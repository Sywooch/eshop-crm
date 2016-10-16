<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Senders */

$this->title = 'Update Senders: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Senders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="senders-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
