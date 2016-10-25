<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */

$this->title = 'Create Statmetrika';
$this->params['breadcrumbs'][] = ['label' => 'Statmetrikas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statmetrika-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
