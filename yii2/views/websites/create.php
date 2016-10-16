<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Websites */

$this->title = 'Create Websites';
$this->params['breadcrumbs'][] = ['label' => 'Websites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="websites-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
