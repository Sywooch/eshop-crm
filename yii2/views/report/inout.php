<?php
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
?>
<h1>Анализ по приходу/расходу товара</h1>
<?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
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
    <?//= $form->field($model, 'date_column')->dropdownList($model->itemAlias('date_column')) ?> 

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p>&nbsp;</p>

<?= GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
    	['class' => 'yii\grid\SerialColumn'],
    	[
    		'label' => 'Наименование',
    		'attribute' => 'tovar_name',           
            'value' => function ($data) {
            	return '['.$data['tovar_id'].'] '.$data['tovar_name'];            	
            },
        ],
        [
        	'label' => 'Cклад',
        	'attribute' => 'sklad_name',
        ],
        [
    		'label' => 'Остаток на начало периода, кол-во / руб',
    		'attribute' => 'before_ostatok',            
        ],
        [
    		'label' => 'Приход, кол-во',
    		'attribute' => 'cnt_prihod',            
        ],
        [
    		'label' => 'Приход, cумма',
    		'attribute' => 'sum_prihod',            
        ],
        [
    		'label' => 'Расход, кол-во',
    		'attribute' => 'cnt_rashod',            
        ],
        [
    		'label' => 'Расход, cумма',
    		'attribute' => 'sum_rashod',            
        ],
        [
    		'label' => 'Остаток на конец периода, кол-во / руб',
    		'attribute' => 'after_ostatok',            
        ],
        //'after_sum_rashod',
    ]
]);