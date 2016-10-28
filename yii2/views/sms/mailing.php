<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Смс рассылка';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="sms-mailing">
	
	<h1><?= Html::encode($this->title) ?></h1>
	<p>Клиентам, чьи заявки:</p>
	
	<?php $form = ActiveForm::begin(['layout' => 'horizontal','method' => 'get'])
	//$form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?//= $form->field($model, 'date1')->fileInput(['class'=>'form-control']) ?>
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
	
	<?= $form->field($model, 'category')->dropdownList($model->categoryList,['prompt'=>'']) ?>
	
	<?= $form->field($model, 'status')->dropdownList(['0'=>'Заказы', '1'=>'Все'],['prompt'=>'']) ?>
	
	<?= $form->field($model, 'msg')->textArea(['value' => 'Заберите Ваш подарок нож Columbia! http://lrf24.ru Тел: 88002000748']) ?>
	
	<?= $form->field($model, 'count')->input() ?>
	
	<?= $model->count >0 ? $form->field($model, 'yes')->checkBox() : ''?>
	
	<div class="form-group">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton('Готово', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?//<button type="submit" class="btn btn-primary">Отобрать</button>?>

<?php ActiveForm::end() ?>

<? if($model->count >0) {
	
	\yii\widgets\Pjax::begin();
	
	echo GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
    	['class' => 'yii\grid\SerialColumn'],    	
        [
        	'label' => 'Телефон',
        	'attribute' => 'phone',
        ],
        [
    		'label' => 'Клиент',
    		'attribute' => 'fio',            
        ], 
    ]
	]);
	
	\yii\widgets\Pjax::end();
}
?>
</div>
