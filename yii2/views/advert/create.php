<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Statcompany */

$this->title = 'Create Statcompany';
$this->params['breadcrumbs'][] = ['label' => 'Statcompanies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statcompany-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
