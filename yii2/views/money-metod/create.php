<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MoneyMethod */

$this->title = 'Создать способ прихода/расхода';
$this->params['breadcrumbs'][] = ['label' => 'Money Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-method-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
