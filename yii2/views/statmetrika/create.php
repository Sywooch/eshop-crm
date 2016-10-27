<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Statmetrika */

$this->title = 'Получить данные метрики';
$this->params['breadcrumbs'][] = ['label' => 'Список данных метрики', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="statmetrika-create">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="StatmetrikaForm">

    <?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]]) ?>
    
    <?= $form->field($model, 'date1')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?>  
        
    <?= $form->field($model, 'date2')->widget(\yii\jui\DatePicker::classname(), [
    	'language' => 'ru',
    	'dateFormat' => 'yyyy-MM-dd',
    	'options'=>['class'=>'form-control']
	]) ?> 

    <button type="submit" class="btn btn-primary">Получить данные</button>

    <?php ActiveForm::end() ?>

    </div><!-- StatmetrikaForm -->

    <?/*= $this->render('_form', [
        'model' => $model,
    ]) */?>

</div>
