<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TovarPrihod */

$this->title = 'Добавить приход товара';
$this->params['breadcrumbs'][] = ['label' => 'Tovar Prihods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-prihod-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
