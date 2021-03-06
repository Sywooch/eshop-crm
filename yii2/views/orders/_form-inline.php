<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\modules\user\models\User;
use app\models\Client;
use app\models\KladrSearch;
use app\models\Senders;
use app\models\Category;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Pjax;
//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
	.form-group {
		padding: 5px 10px;
	}
	#orders-note, .field-orders-note {
		min-width: 80%;
	}
</style>
<div class="order-form">
   
	<div class="form-horizontal1">
    <?php $form = ActiveForm::begin([
		 'id' => 'order-form',
		 'options' => ['class' => 'form-inline'],
		/* 'fieldConfig' => [		 	
			'template' => '{label}<div class="col-sm-8">{input}</div><div class="col-sm-12">{error}</div>',
			'labelOptions' => ['class' => 'col-sm-4 control-label'],
			 //'template' => '{label}<div class="col-sm-9">{input}</div>',		
		 ], */
 	]); ?>
 	<? if (!$model->isNewRecord) { ?>	
	<div class="row well well-sm">
	
			<div class="form-group">
	    		<label><?=$model->getAttributeLabel('id')?>:</label>
	    		<p class="form-control-static"><?=$model->id?></p>	    		
	    	</div>

	    	<div class="form-group">
	    		<label><?=$model->getAttributeLabel('created_at')?>:</label>
	    		<p class="form-control-static"><?= yii::$app->formatter->asDatetime($model->created_at)?></p>
    		</div>

    		<div class="form-group">
	    		<label><?=$model->getAttributeLabel('url')?>:</label>
	    		<p class="form-control-static"><?=$model->url?></p>	    		
	    	</div>
    
    </div>	
    <? }?>
    <div class="row">    	
		<div class="col-sm-12">
			<?= $form->field($model, 'source')->dropdownList(
    			$model::itemAlias('source')
    			//['prompt'=>'Select Category']
			);?>    
	   	
			<?= $form->field($model, 'status')->dropdownList(
    			$model::itemAlias('status'), ['options'=>$model::itemAlias('status_disable')] 			
			);?>
			
			<?= $form->field($model, 'prich_double')->textarea(['rows' => 2]) ?>
				
			<? if (Yii::$app->user->can('root')) {
				$manager_list = User::getUsersByRole('manager');
				$manager_list = ArrayHelper::map($manager_list, 'id', 'username');
				echo $form->field($model, 'manager_id')->dropdownList($manager_list, ['prompt'=>'-None-']);
			} else { ?>
			
			<div class="form-group">
	    		<label><?=$model->getAttributeLabel('manager_id')?>:</label>
	    		<p class="form-control-static"><?=(!empty($model->manager->fullname) ? $model->manager->fullname : 'нет')?></p>	    		
	    	</div>
				
			<? } ?>			
	
			<? if (Yii::$app->user->can('root')) {
				$packer_list = User::getUsersByRole('packer');
				$packer_list = ArrayHelper::map($packer_list, 'id', 'username');
				echo $form->field($model, 'packer_id')->dropdownList($packer_list, ['prompt'=>'-None-']);
			} else { ?>		    
	
			<div class="form-group">
	    		<label><?=$model->getAttributeLabel('packer_id')?>:</label>
	    		<p class="form-control-static"><?=(!empty($model->packer->fullname) ? $model->packer->fullname : 'нет')?></p>	    		
	    	</div>
	
			<? } ?>			
		</div>
	</div>
	
	<div class="row">
	
	 	<div class="col-sm-12 well well-sm">
		
			<? if (Yii::$app->request->get('client_id')) { 
				$client = Client::findOne(Yii::$app->request->get('client_id'));				
			}
			else $client = $model->client;
			echo Html::hiddenInput('Orders[client_id]', $client->id);
			$c_tmpl=null;
			//$c_tmpl = ['template'=>'{label}<div class="col-sm-5">{input}</div><div class="col-sm-12">{error}</div>', 'labelOptions' => ['class' => 'col-sm-1 control-label']];
			$this->registerjs("
			$('.client-block').click(function(){
				$('#client-block').toggle();
				return false;
			});")
			?>
			
			<div class="form-group">
	    		<label><?=$model->getAttributeLabel('client_id')?>:</label>	    		
	    		<p class="form-control-static"> <?= Html::a('<span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> '.($client->phone.' ('.$client->fio.')'),'#',array('class'=>'column-button btn btn-default client-block')) ?></p>
	    	</div>
	    <? //print_r(KladrSearch::regionList())?>
	    <div id ="client-block" style="display:none">
	    	<div>
	    	<?= $form->field($client, 'fio', $c_tmpl)->textInput(['maxlength' => true]) ?>

		    <?= $form->field($client, 'phone', $c_tmpl)->textInput(['maxlength' => true]) ?>

		    <?= $form->field($client, 'email', $c_tmpl)->textInput(['maxlength' => true]) ?>
		    
		    <?= $form->field($client, 'postcode', $c_tmpl)->textInput(['maxlength' => true]) ?>
		    
		    </div>
		    <div>		    
		    
		    <?= $form->field($client, 'region_id', $c_tmpl)->dropDownList(KladrSearch::regionList(),['prompt'=>'-None-']) ?>
		    
		    <?= $form->field($client, 'area_id', $c_tmpl)->dropDownList(KladrSearch::areaList($client->area_id),['prompt'=>'-None-']) ?>
		    
		    <?= $form->field($client, 'city_id', $c_tmpl)->dropDownList(KladrSearch::cityList($client->city_id),['prompt'=>'-None-']) ?>
		    
		    <?= $form->field($client, 'settlement_id', $c_tmpl)->dropDownList(KladrSearch::settlementList($client->settlement_id),['prompt'=>'-None-']) ?>
			</div>
			<div class="row">
			<div class="col-sm-6">
		    	<?= $form->field($client, 'address')->textarea(['rows' => 2]) ?>				
			</div>
			<div class="col-sm-6">

		    <?//= $form->field($client, 'ident', $c_tmpl)->textarea(['rows' => 2]) ?>

		    <?//= $form->field($client, 'note', $c_tmpl)->textarea(['rows' => 2]) ?>
		    
		    </div>
			</div>
		</div>
						
			
			<? /*} else {
				/*********************
				<div class="form-group">
	    		<label class="col-sm-4 control-label"><?=$model->getAttributeLabel('client_id')?>:</label>
	    		<div class="col-sm-8">
	    			<p class="form-control-static"><?= Html::a(($client->phone.' ('.$client->fio.')'), ['client/view', 'id' => $client->id]) ?></p>
	    		</div>
	    	</div>
				*********************
				
				echo $form->field($model, 'client_id')->widget(\yii\jui\AutoComplete::classname(), [
					'clientOptions' => [
						'source'=>new JsExpression("function(request, response) {
	    					$.getJSON('".Url::toRoute('clientajax')."', {
	        					term: request.term
	    					}, response);
	    				}"),
	    			],
	    			'options' => ['class' => 'form-control'],					
				]);
			}*/?>
	    </div>
	</div>
	
	<div class="row">
		<div class="col-sm-12">
			<?= $form->field($model, 'sender_id')->dropdownList(Senders::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt'=>'']);?>			
		
		    <?= $form->field($model, 'otpravlen')->checkbox() ?>
			
			<?=$form->field($model, 'data_otprav')->widget(yii\jui\DatePicker::classname(), [
                    'model' => $searchModel,
                    'attribute' => 'data_otprav',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ])
            ?>
  		
		    <?= $form->field($model, 'summaotp')->textInput(['maxlength' => true]) ?>
		    
		    <?= $form->field($model, 'fast')->checkbox() ?>
			
			<?= $form->field($model, 'dostavza')->checkbox() ?>
		    
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12 well well-sm">
		
		    <?= $form->field($model, 'dostavlen')->checkbox() ?>
		
			<?=$form->field($model, 'data_dostav')->widget(yii\jui\DatePicker::classname(), [
                    'model' => $searchModel,
                    'attribute' => 'data_dostav',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ])
            ?>
            
            <?= $form->field($model, 'oplachen')->checkbox() ?>	   	   
		    	
			<?=$form->field($model, 'data_oplata')->widget(yii\jui\DatePicker::classname(), [
                    'model' => $searchModel,
                    'attribute' => 'data_oplata',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ])
            ?>

         </div>	
	</div>
	
	<div class="row">
		<div class="col-sm-12">
			<?= $form->field($model, 'vkasse')->checkbox() ?>	
	
			<?=$form->field($model, 'data_vkasse')->widget(yii\jui\DatePicker::classname(), [
                    'model' => $searchModel,
                    'attribute' => 'data_vkasse',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ])
            ?>
            
            <?= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>
			
			<?= $form->field($model, 'identif')->textInput(['maxlength' => true]) ?>
			
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12 well well-sm">
		
 			<?= $form->field($model, 'vozvrat')->checkbox() ?> 			
 
			<?=$form->field($model, 'data_vozvrat')->widget(yii\jui\DatePicker::classname(), [
                    'model' => $searchModel,
                    'attribute' => 'data_vozvrat',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        //'dateFormat' => 'yyyy-MM-dd',
                    ]
                ])
            ?>

		
		    <?= $form->field($model, 'vozvrat_cost')->textInput(['maxlength' => true]) ?>

		    <?= $form->field($model, 'prich_vozvrat')->textarea(['rows' => 3]) ?>
		    	    
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12">			
			
		    <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>	    
  
		</div>
	</div>
	
	<h4>Товары:</h4>
<?
$rashod_list = $model->rashod;
if (count($rashod_list) > 0) {
	//$model->price_old = $model->rashod;	
	//$spec_old = '';
	$n=0;
	foreach ($rashod_list as $rashod) {
?>
		<div class="row tovar-row">
			<div class="col-sm-1"><label for="tovar_list[old][<?=$rashod->id?>]" class="control-label"><?= ++$n ?>.</label></div>
			<div class="col-sm-6">	
				<p class="form-control-static"><?= $rashod->tovar->name?></p>
				<?= Html::hiddenInput("tovar_list[old][{$rashod->id}]", $rashod->id);?>
			</div>
			<div class="col-sm-3">
				<p class="form-control-static"><?= $rashod->amount?></p>
			</div>
			<div class="col-sm-2">
				<a class="btn btn-default" id="delete" title="Удалить">Удалить</a>
			</div>
		</div>
<?	
	}
}

//echo '<pre>';print_r($model->rashod);echo '</pre>';
?>	
	<div id="last_row" class="row">
		<div class="col-sm-6">
			<hr><p><a id="add" class="btn btn-default">Добавить позицию</a></p>
		</div>			
	</div>

	<div class="row">
		<div class="col-sm-3">		  
			<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>		    
		</div>          
	</div>      
	
    <?php ActiveForm::end(); ?>

</div>
</div>
    <?// \yii\helpers\VarDumper::dump($prvTovar) ?>
<? Pjax::begin(); ?>

<?= GridView::widget([
        'dataProvider' => $prvTovar,
        'filterModel' => $mdlTovar,
        'id' => 'tovar-grid',
        'rowOptions' => function ($model, $key, $index, $grid) {
                return ['id' => $model['id'], 'onclick' => 'alert(this.id);'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'artikul',
            'name',           
            'price',
            [
            	'attribute'=>'category_id',
            	'value'=> 'category.name',
            	'filter' => Html::activedropDownList(
	                $mdlTovar,
	                'category_id',
	                Category::find()->select(['name', 'id'])->indexBy('id')->column(),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],
            [
            	'attribute'=>'active',
            	'value'=> function($mdlTovar) {
            		$st=$mdlTovar->itemAlias('active',$mdlTovar->active);
            		return $st;
            	},
            	'filter' => Html::activedropDownList(
	                $mdlTovar,
	                'active',
	                $mdlTovar->itemAlias('active'),
	                ['class' => 'form-control', 'prompt' => '-All-']//'value' => 'OrderSearch[status][]'
	            ),
        	],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>	

<? Pjax::end(); ?>

<?
$balance_source = \app\models\TovarBalance::find()->with('tovar', 'sklad')->all();

foreach($balance_source as $balance) {
	$tmp['id'] = $balance->id;
	$tmp['name'] = $balance->tovar->name. '; ЦЕНА: '.$balance->tovar->price.'; ОСТАТОК: '. $balance->amount . ' шт';
	$tmp['grp'] = $balance->sklad->name;
	$balance_list[]=$tmp;
}
$balance_list = ArrayHelper::map($balance_list, 'id', 'name','grp');
//echo '<pre>';print_r($balance_list);echo '</pre>';die;
//ini_set('display_errors', 1);
$tmp1 = Html::dropDownList("tovar_list[new][x][balance_id]", null,$balance_list,["class"=>"form-control"]);
//$tmp1 .= Html::hiddenInput("tovar_list[new][x][sklad_id]", $tovar->sklad_id);
$tmp1 = str_replace(PHP_EOL," ",$tmp1);
$tmp1 = str_replace('[x]',"[' + len + ']",$tmp1);
$tmp2 = Html::input("text","tovar_list[new][x][amount]", '1',["class"=>"form-control"]);
$tmp2 = str_replace('[x]',"[' + len + ']",$tmp2);
?>
<script type="text/javascript">
	// выбрали регион
	function change_region()
	{
		$.ajax({
			type: "POST",
			dataType: "JSON",
			data: {'code' : $('select[name="Client[region_id]"] option:selected').val()},
			url: "<?php echo Url::to('/kladr/area-list') ?>",
			beforeSend: (function(msg){ $('select[name="Client[area_id]"]').parent('.form-group').children('div.help-block').text('ЖДИТЕ... '); }),
			success: function(msg,stat)
			{
				if ('null' != msg && msg !== null && $.isArray(msg))
				{
					$('select[name="Client[area_id]"]').parent('.form-group').children('div.help-block').text('Готово!');
					$("select[name='Client[area_id]'] option").remove();
					$("select[name='Client[area_id]']").append('<option value="">-Выберите район</option>');
					$.each(eval(msg),function(i,item) {//alert(item);return false;
						$("select[name='Client[area_id]']").append('<option value="'+item.optionKey+'">'+item.optionValue+'</option>');
					});
					//$("select[name='Client[area]']").html(options);
				}
				else
				{
					$("select[name='Client[area_id]'] option").remove();
					$("select[name='Client[area_id]']").append('<option value="">-Нет данных</option>');
					$('select[name="Client[area_id]"]').parent('.form-group').children('div.help-block').text('');
				} 
			},
			error: function(msg,stat){
				$('select[name="Client[area_id]"]').parent('.form-group').children('div.help-block').text('ОШИБКА ('+stat+') '+eval(msg)); }
			//			complete: function(msg){ $("#ajax_mess_area").text('ГОТОВО! '); }
		});
		c_area=$('select[name="Client[region_id]"] option:selected').val();
		//c_area=c_area.substr(0,2)+'000';
		change_area(c_area);
	}
	//выбрали район
	function change_area(data_area)
	{
		if (data_area!=null) data=data_area;
		else data = $('select[name="Client[area_id]"] option:selected').val();
		$.ajax({
			type: "POST",
			dataType: "JSON",
			data: {'code' : data},
			url: "<?php echo Url::to('/kladr/city-list') ?>",
			beforeSend: (function(msg){ $('select[name="Client[city_id]"]').parent('.form-group').children('div.help-block').text('ЖДИТЕ... '); }),
			success: function(msg,stat)
			{//alert(msg);
				if ('null' != msg && msg !== null && $.isArray(msg))
				{
					$('select[name="Client[city_id]"]').parent('.form-group').children('div.help-block').text('Готово!');
					$("select[name='Client[city_id]'] option").remove();
					$("select[name='Client[city_id]']").append('<option value="">-Выберите город</option>');
					$.each(eval(msg),function(i,item)	{
						$("select[name='Client[city_id]']").append('<option value="'+item.optionKey+'">'+item.optionValue+'</option>');
					});
					//$("select[name='Client[city]']").html(options);
				}
				else
				{
					$("select[name='Client[city_id]'] option").remove();
					$("select[name='Client[city_id]']").append('<option value="">-Нет данных</option>');
					$('select[name="Client[city_id]"]').parent('.form-group').children('div.help-block').text(msg);					
				}
			},
			error: function(msg,stat){
				$('select[name="Client[city_id]"]').parent('.form-group').children('div.help-block').text('ОШИБКА ('+stat+'): '+msg);
				//$("select[name='Client[city]'] option").remove();
				//$("select[name='Client[city]']").append('<option value="">-Нет данных</option>');
				//$('select[name="Client[city]"]').parent('.form-group').children('div.help-block').text('');	
				//alert(data_area);
				//change_region(data_area);
			}
			//complete: function(msg){ $("#ajax_mess_area").text('ГОТОВО! '); }
		});
		c_city=$('select[name="Client[area_id]"] option:selected').val();
		//c_city=c_city.substr(0,5)+'000';
		change_city(c_city);
	}
	//выбрали город
	function change_city(data_city)
	{//alert(data_city);
		if (data_city!=null) data=data_city;
		else data=$('select[name="Client[city_id]"] option:selected').val();
		$.ajax({
			type: "POST",
			dataType: "JSON",
			data: {'code' : data},
			url: "<?php echo Url::to('/kladr/settlement-list') ?>",
			beforeSend: (function(msg){ $('select[name="Client[settlement_id]"]').parent('.form-group').children('div.help-block').text('ЖДИТЕ... '); }),
			success: function(msg,stat)
			{//alert(msg);
				if ('null' != msg && msg !== null && $.isArray(msg))
				{
					$('select[name="Client[settlement_id]"]').parent('.form-group').children('div.help-block').text('Готово!');
					$("select[name='Client[settlement_id]'] option").remove();
					$("select[name='Client[settlement_id]']").append('<option value="">-Выберите нас.пункт</option>');
					$.each(eval(msg),function(i,item)	{
						$("select[name='Client[settlement_id]']").append('<option value="'+item.optionKey+'">'+item.optionValue+'</option>');
					});
					//$("select[name='Client[settlement]']").html(options);
				}
				else
				{
					$("select[name='Client[settlement_id]'] option").remove();
					$("select[name='Client[settlement_id]']").append('<option value="">-Нет данных</option>');
					$('select[name="Client[settlement_id]"]').parent('.form-group').children('div.help-block').text('');
				}
			},
			error: function(msg,stat){
				$('select[name="Client[settlement_id]"]').parent('.form-group').children('div.help-block').text('ОШИБКА ('+stat+'): '+msg);
				//alert(data);change_city(data);
			}
		});
	}
		
	function add_row() {
	var lr = $("#last_row");
	var len = $('label[for*=tovar_list]').length;
	len = len+1;	
	var s='<div class="row tovar-row">';	
	s+='<div class="col-sm-1"><label for="tovar_list[new][' + len + '][balance_id]" class="control-label">' + len + '.</label></div>';
	s+='<div class="col-sm-6">';
	s+='<?=$tmp1?>';
	s+='</div>';
	s+='<div class="col-sm-3">';
	s+='<?=$tmp2?>';	
	s+='</div>';
	s+='<div class="col-sm-2"><a class="btn btn-default" id="delete">Удалить</a></div>';
	s+='</div>';
	var new_row = $(s);
	lr.before(new_row);
}
function delete_row() {	
	var len = $('label[for*=tovar_list]').length;
	if (len<=1) {alert('Должен быть хотя бы один товар');return false;}
	$(this).parents('div.tovar-row').remove();
	renum();
}
function renum() {
	var totals = 0;
	$('label[for*=tovar_list]').each(
	function() {
		totals++;
		$(this).text(totals);
	})
}

$('select[name="Client[region_id]"]').change(change_region);
$('select[name="Client[area_id]"]').bind('change',function(){change_area(null)});
$('select[name="Client[city_id]"]').bind('change',function(){change_city(null)});
$('body').on('click', 'a#delete', delete_row);
$('a#add').on('click', add_row);	
</script>