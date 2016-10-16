<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TovarRashod */

$this->title = 'Create Tovar Rashod';
$this->params['breadcrumbs'][] = ['label' => 'Tovar Rashods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-rashod-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
