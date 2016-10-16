<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TovarCosts */

$this->title = 'Create Tovar Costs';
$this->params['breadcrumbs'][] = ['label' => 'Tovar Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-costs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
