<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\MoneyBalance */

$this->title = 'Create Money Balance';
$this->params['breadcrumbs'][] = ['label' => 'Money Balances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-balance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
