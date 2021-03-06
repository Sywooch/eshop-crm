<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Kladr */

$this->title = 'Update Kladr: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Kladrs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->code]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kladr-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
