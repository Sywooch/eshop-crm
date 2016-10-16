<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Money */

$this->title = 'Создать поток';
$this->params['breadcrumbs'][] = ['label' => 'Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
