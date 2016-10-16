<?php
//ini_set('display_error', 1);
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StatcompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Анализ рекламы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advert-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']],'method'=>'get'])
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
    <?= $form->field($model, 'campaign') ?> 
    
    <?= $form->field($model, 'host')->checkbox() ?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

	<?php ActiveForm::end() ?>
	
	<p>&nbsp;</p>
	
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?= GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
    	['class' => 'yii\grid\SerialColumn'],
    	/*[
    		'label' => 'Наименование',
    		'attribute' => 'tovar_name',           
            'value' => function ($data) {
            	return '['.$data['tovar_id'].'] '.$data['tovar_name'];            	
            },
        ],*/
        [
        	'label' => 'ID',
        	'attribute' => 'id_company',
        	'format' => 'raw',
        	'value' => function ($mdl, $key, $index, $column) {        		
        		return Html::a($mdl['id_company'], ['campaign', 'idc' => $mdl['id_company'], 'date1'=>$mdl['date1'], 'date2'=>$mdl['date2']], ['data-pjax' => 0]);//
        	}
        ],
        [
    		'label' => 'Кампания',
    		'attribute' => 'name',            
        ],
        [
    		'label' => 'Показы',
    		'attribute' => 'shows',            
        ],
        [
    		'label' => 'Клики',
    		'attribute' => 'clicks',            
        ],
        [
    		'label' => 'Расход',
    		'attribute' => 'costs',            
        ],
        [
    		'label' => 'Сайт',
    		'attribute' => 'host',
        ],
        [
    		'label' => 'Источник',
    		'attribute' => 'source',
        ],
        [
    		'label' => 'Заявки',
    		'attribute' => 'cnt_za',
    		'format' => 'raw',
    		'value' => function ($model, $key, $index, $column) {
    			$p = 0;
    			$p = ($model['costs'] >0 and $model['cnt_za'] >0) ? round($model['costs']/$model['cnt_za'],2) : $p;
	            return $model['cnt_za'].' <span style="color: #aaa"> /'.$p.'</span>';
	        },
        ],
		[
    		'label' => 'Заказы',
    		'attribute' => 'cnt_zz',
    		'format' => 'raw',
    		'value' => function ($model, $key, $index, $column) {
    			$p = 0;
    			$p = ($model['costs'] >0 and $model['cnt_zz'] >0) ? round($model['costs']/$model['cnt_zz'],2) : $p;
	            return $model['cnt_zz'].' <span style="color: #aaa"> /'.$p.'</span>';
	        },
        ],
        //'after_sum_rashod',
    ]
]);?>

</div>
