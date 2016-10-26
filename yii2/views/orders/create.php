<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = 'Создать заявку';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if ($model->isNewRecord and !Yii::$app->request->get('client_id')) { ?>

<p>Для начала выберите клиента (или создайте) <?= Html::a('по этой ссылке', '/client/index',['class'=>'btn btn-primary'])?></p>
	
<?} else { ?>

<div class="orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<? } ?>
