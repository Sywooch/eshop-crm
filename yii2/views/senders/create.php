<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Senders */

$this->title = 'Create Senders';
$this->params['breadcrumbs'][] = ['label' => 'Senders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="senders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
