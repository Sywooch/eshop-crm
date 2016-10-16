<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TovarCancelling */

$this->title = 'Cписать товар';
$this->params['breadcrumbs'][] = ['label' => 'Tovar Cancellings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tovar-cancelling-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sklad_list' => $sklad_list,
        'tovar_list' => $tovar_list,
    ]) ?>

</div>
