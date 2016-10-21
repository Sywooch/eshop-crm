<?php
//use yii\helpers\Html;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
use app\modules\user\models\User;
use app\models\Client;
use app\models\KladrSearch;
use app\models\Senders;
use app\models\Category;
use yii\web\View;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
//use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
//<!-- Add fancyBox main JS and CSS files -->
$this->registerJsFile(\Yii::$app->request->baseUrl.'/lib/fancybox/jquery.fancybox.pack.js?v=2.1.5', ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile(\Yii::$app->request->baseUrl.'/lib/fancybox/jquery.fancybox.css?v=2.1.5', ['media' => 'screen']);
$this->registerJsFile(\Yii::$app->request->baseUrl.'/lib/inventory.js', ['depends' => 'yii\web\JqueryAsset']);
?>
<style>
#accordion .panel-heading { padding: 0;}
#accordion .panel-title > a {
	display: block;
	padding: 0.4em 0.6em;
    outline: none;
    font-weight:bold;
    text-decoration: none;
}

#accordion .panel-title > a.accordion-toggle::before, #accordion a[data-toggle="collapse"]::before  {
    content:'\2212';/*"\e113";*/
    float: left;
    font-family: 'Glyphicons Halflings';
	margin-right :1em;
}
#accordion .panel-title > a.accordion-toggle.collapsed::before, #accordion a.collapsed[data-toggle="collapse"]::before  {
    content:'\002b';/*"\e114";*/
}
#accordion table#tovar-list td, #accordion table#tovar-list th {
	vertical-align: middle;
}
</style>


<div class="order-form">
	
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
 	<? if (!$model->isNewRecord) { ?>
 		<p><span class="label label-default" style="font-size:90%"><strong><?=$model->getAttributeLabel('date_at')?>:</strong> <?= yii::$app->formatter->asDatetime($model->date_at)?></span><?if(!empty($model->url)) {echo ' <strong>'.$model->getAttributeLabel('url').'</strong>: '.$model->url;} ?><?if(!empty($model->ip_address)) {echo ' <strong>'.$model->getAttributeLabel('ip_address').'</strong>: <a target="_blank" href="http://ipgeobase.ru/?address='.$model->ip_address.'">'.$model->ip_address.'</a>';} ?></p>
    <? }?>
    
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    
    	<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-order-block">
      			<h4 class="panel-title">
        			<a class="accordion-toggle" role="button" data-toggle="collapse" data-parent="#accordion" href="#order-block" aria-expanded="true" aria-controls="order-block">Общее</a>
        		</h4>
        	</div>
    
	 		<div id="order-block" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="head-order-block">
		 		<div class="panel-body">
    
			    <div class="row">    	
					<div class="col-md-6">
						<?= $form->field($model, 'source')->dropdownList(
			    			$model->itemAlias('source'),
			    			['prompt'=>'--Выбрать источник--']
						);?>
						
						<?//= $form->field($model, 'url', ['inputOptions' => ['readonly' => true]])->textInput(['maxlength' => true]) ?>	    		
				   	
						<?= $form->field($model, 'status')->dropdownList(
			    			$model->itemAlias('status'), ['options'=>$model->itemAlias('status_disable')] 			
						);?>
						
						<?= $form->field($model, 'prich_double')->dropdownList(
			    			$model->itemAlias('prich_double'),['prompt'=>'']
						);?>
						
						<?//= $form->field($model, 'prich_double')->textarea(['rows' => 2]) ?>
					</div>
					<div class="col-md-6">
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
						
						<?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>		
					</div>
				</div>
	
				</div>
			</div>
		</div>
	
	    <div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-client-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#client-block" aria-expanded="false" aria-controls="client-block">Клиент <?=!empty($model->client_id) ? '[#'.$model->client_id.']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="client-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-client-block">
		 		<div class="panel-body">

				<div class="row">
				
				 	<div class="col-md-4">
					
						<? if (Yii::$app->request->get('client_id')) { 
							$client = Client::findOne(Yii::$app->request->get('client_id'));				
						}
						else $client = $model->client;
						echo Html::hiddenInput('Orders[client_id]', $client->id);
						$c_tmpl=[];
						//$c_tmpl = ['template'=>'{label}<div class="col-sm-5">{input}</div><div class="col-sm-12">{error}</div>', 'labelOptions' => ['class' => 'col-sm-1 control-label']];?>
										    	
				    	<?= $form->field($client, 'fio', $c_tmpl)->textInput(['maxlength' => true]) ?>

					    <?= $form->field($client, 'phone', $c_tmpl)->textInput(['maxlength' => true]) ?>

					    <?= $form->field($client, 'email', $c_tmpl)->textInput(['maxlength' => true]) ?>
					    
					    <?= $form->field($client, 'postcode', $c_tmpl)->textInput(['maxlength' => true]) ?>
					    
					</div>
					<div class="col-md-4">
						<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
							<li role="presentation" class="active"><a href="#tab-auto" aria-controls="tab-auto" role="tab" data-toggle="tab">Подобрать адрес автоматом</a></li>
							<li role="presentation"><a href="#tab-hand" aria-controls="tab-hand" role="tab" data-toggle="tab">Вручную</a></li>
						</ul>
						
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="tab-auto">
					   			 <div class="form-group">									
									<div class="col-sm-12">
									<?= Html::input('text', 'find_addr',null,['id'=>'find_addr','class'=>'form-control'])?>
									</div>
								</div>
								
								<?= $form->field($client, 'address')->textarea(['rows' => 3]) ?>
								
							</div>
							
							<div role="tabpanel" class="tab-pane" id="tab-hand">
							
							<?= $form->field($client, 'region_id', $c_tmpl)->dropDownList(KladrSearch::regionList(),['prompt'=>'-None-']) ?>
												    
					    	<?= $form->field($client, 'area_id', $c_tmpl)->dropDownList(KladrSearch::areaList($client->area_id),['prompt'=>'-None-']) ?>
					    
						    <?= $form->field($client, 'city_id', $c_tmpl)->dropDownList(KladrSearch::cityList($client->city_id),['prompt'=>'-None-']) ?>
					    
						    <?= $form->field($client, 'settlement_id', $c_tmpl)->dropDownList(KladrSearch::settlementList($client->settlement_id),['prompt'=>'-None-']) ?>
					    
						    <?= $form->field($client, 'flat', $c_tmpl)->textInput(['maxlength' => true]) ?>
			
								
							</div>							
						
						</div>	
								    
					    <script src="https://dadata.ru/static/js/lib/jquery.suggestions-16.2.min.js"></script>
						<link href="https://dadata.ru/static/css/lib/suggestions-16.2.css" rel="stylesheet">
						<script>
						 $("#find_addr").suggestions({
						  serviceUrl: "https://dadata.ru/api/v2",
						  token: "<?php echo Yii::$app->params['dadata.token']?>",
						  type: "ADDRESS",
						  onSelect: showSelected
						});

						function join(arr /*, separator */) {
						  var separator = arguments.length > 1 ? arguments[1] : ", ";
						  return arr.filter(function(n){return n}).join(separator);
						}

						function showSelected(suggestion) {
						  var address = suggestion.data;
						  var address_full = suggestion.unrestricted_value;
						  console.log(address_full);
						  //console.log(address);
						  //alert(address.region_kladr_id);
						  $("#client-postcode").val(address.postal_code);
						  $("#client-address").val(address_full);
						 /* $("#region").val(join([
						    join([address.region_type, address.region], " "),
						    join([address.area_type, address.area], " ")
						  ]));
						  $("#city").val(join([
						    join([address.city_type, address.city], " "),
						    join([address.settlement_type, address.settlement], " ")
						  ]));
						  $("#street").val(
						    join([address.street_type, address.street], " ")
						  );
						  $("#house").val(join([
						    join([address.house_type, address.house], " "),
						    join([address.block_type, address.block], " ")
						  ]));
						  $("#flat").val(
						    join([address.flat_type, address.flat], " ")
						  );*/
						}
						</script>
					</div>
										
				</div>				
				</div>
			</div>
		</div>

		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-sender-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#sender-block" aria-expanded="false" aria-controls="sender-block">Отправка <?=(!empty($model->data_otprav) and $model->otpravlen=='1') ? '['.Yii::$app->formatter->asDate($model->data_otprav).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="sender-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-sender-block">
		 		<div class="panel-body">
		 		
				<div class="row">
					<div class="col-md-6">
						<?= $form->field($model, 'sender_id')->dropdownList(Senders::find()->select(['name', 'id'])->indexBy('id')->column(), ['prompt'=>'']);?>
							
					    <?= $form->field($model, 'summaotp')->textInput(['maxlength' => true]) ?>
						
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
			            
			            <?= $form->field($model, 'identif')->textInput(['maxlength' => true]) ?>
			  		</div>
			  		<div class="col-md-6">
					    <?= $form->field($model, 'otpravlen')->checkbox() ?>
					    
					    <?= $form->field($model, 'fast')->checkbox() ?>
						
						<?= $form->field($model, 'dostavza')->checkbox() ?>
						
						<?= $form->field($model, 'send_moskva')->checkbox() ?>
					    
					</div>
				</div>
	
				</div>
			</div>
		</div>
	
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-dostavlen-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#dostavlen-block" aria-expanded="false" aria-controls="dostavlen-block">Доставка <?=(!empty($model->data_dostav) and $model->dostavlen=='1') ? '['.Yii::$app->formatter->asDate($model->data_dostav).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="dostavlen-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-dostavlen-block">
		 		<div class="panel-body">
	
				<div class="row">
					<div class="col-md-6">		
					    <?= $form->field($model, 'dostavlen')->checkbox() ?>
					</div>
					<div class="col-md-6">		
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
			      </div>
			    </div>
			    
				</div>
			</div>
		</div>
    
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-oplachen-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#oplachen-block" aria-expanded="false" aria-controls="oplachen-block">Оплата <?=(!empty($model->data_oplata) and $model->oplachen=='1') ? '['.Yii::$app->formatter->asDate($model->data_oplata).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="oplachen-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-oplachen-block">
		 		<div class="panel-body">
	
				<div class="row">
					<div class="col-md-6">
						<?= $form->field($model, 'type_oplata')->dropdownList($model->itemAlias('type_oplata'));?>
						      
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
			        <div class="col-md-6">
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

			         </div>	
				</div>
				
				</div>
			</div>
		</div>

		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-vozvrat-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#vozvrat-block" aria-expanded="false" aria-controls="vozvrat-block">Возврат <?=(!empty($model->data_vozvrat) and $model->vozvrat =='1') ? '['.Yii::$app->formatter->asDate($model->data_vozvrat).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="vozvrat-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-vozvrat-block">
		 		<div class="panel-body">

				<div class="row">
					<div class="col-md-6">		
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
			        </div>
			        
			        <div class="col-md-6">		
					    <?= $form->field($model, 'vozvrat_cost')->textInput(['maxlength' => true]) ?>

					    <?= $form->field($model, 'prich_vozvrat')->textarea(['rows' => 3]) ?>					    	    
					</div>
				</div>	
					
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-sms-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#sms-block" aria-expanded="false" aria-controls="sms-block">Смс <?=(count($model->sms)>0) ? '['.count($model->sms).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="sms-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-sms-block">
		 		<div class="panel-body">

				<div class="row">
					<div class="col-md-12">
					<? Pjax::begin(['enablePushState' => false]); ?>
		 			<?= GridView::widget([		 				
				        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getSms()]),
				        //'filterModel' => $searchModel,
				        'columns' => [
				            ['class' => 'yii\grid\SerialColumn'],

				            //'id',
				            'sms_id',
				            'order_id',
				            [
				            	'attribute'=>'event',
				            	'value'=> function($model) {
				            		$st=$model->itemAlias('event',$model->event);
				            		return $st;
				            	},				            	
				        	],
				            //'event',
				            'status',
				            'cost',
				            // 'msg:ntext',
				            // 'note:ntext',

				            //['class' => 'yii\grid\ActionColumn'],
				        ],
				    ]); ?>
				    <? Pjax::end(); ?>
			        </div>			  
				</div>	
					
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-call-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#call-block" aria-expanded="false" aria-controls="call-block">Звонки <?=(count($call) >0 ) ? '['.count($call).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="call-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-call-block">
		 		<div class="panel-body">
		 		<? //\yii\helpers\VarDumper::dump($call,true,10); ?>
				<div class="row">
					<div class="col-md-12">
					<? Pjax::begin(['enablePushState' => false]); ?>
		 			<?= GridView::widget([		 				
				        'dataProvider' => new \yii\data\ArrayDataProvider(['allModels' => $call]),
				        //'filterModel' => $searchModel,
				        'columns' => [
				            ['class' => 'yii\grid\SerialColumn'],

				            [
				            	'attribute' => 'call_date',
				            	'label' => 'Дата звонка',
				            	'format' => 'datetime',
				            ],
				            [
				            	'attribute' => 'duration',
				            	'label' => 'Длительность',
				            	//'format' => 'time',
				            ],
				            [
				            	'attribute' => 'status',
				            	'label' => 'Статус',
				            ],
				            [
				            	'attribute' => 'direction',
				            	'label' => 'Направление',
				            ],
				            [
				            	'attribute' => 'operator_name',
				            	'label' => 'Оператор',
				            ],
				            [
				            	'attribute' => 'file_link',
				            	'format' => 'raw',
				            	'label' => 'Запись',
				            	'value' => function($model) {
				            		$f = $model['file_link'];
				            		//return print_r($model['file_link'], true);
				            		if(!empty($f)) {
										return '<audio src="'.$f[0].'" controls></audio>';
									}
				            		//else return '-';
				            	},
				            ]
				        ],
				    ]); ?>
				    <? Pjax::end(); ?>
					    
					</div>
				</div>
	
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-utm-block">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#utm-block" aria-expanded="false" aria-controls="utm-block">UTM метки <?=(count($model->utmLabel)>0) ? '['.count($model->utmLabel).']' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="utm-block" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-utm-block">
		 		<div class="panel-body">

				<div class="row">
					<div class="col-md-12">
					<? Pjax::begin(['enablePushState' => false]); ?>
		 			<?= GridView::widget([		 				
				        'dataProvider' => new \yii\data\ActiveDataProvider(['query' => $model->getUtmLabel()]),
				        //'filterModel' => $searchModel,
				        'columns' => [
				            ['class' => 'yii\grid\SerialColumn'],

				            'id',
				            'order_id',
				            'utm_campaign',
				            'utm_content:ntext',
				            'utm_source',
				            'utm_medium',
				            'utm_term',
				            'source_type',
				            'source',
				            //'group_id',
				            'banner_id',
				            'position',
				            'position_type',
				            'region_name',
				            'device',
				        ],
				    ]); ?>
				    <? Pjax::end(); ?>
			        </div>			  
				</div>	
					
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
    		<div class="panel-heading" role="tab" id="head-products">
      			<h4 class="panel-title">
        			<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#products" aria-expanded="false" aria-controls="products">Товары <?=(count($model->rashod)>0) ? '['.count($model->rashod).'/'.$model->tovarSumma.'р]' : '' ?></a>
        		</h4>
        	</div>
    
	 		<div id="products" class="panel-collapse collapse" role="tabpanel" aria-labelledby="head-products">
		 		<div class="panel-body">

	
				
	<div class="table-responsive">
	<table id="tovar-list" class="table table-striped table-bordered">
	<thead>
		<th>#</th>
		<th>Наименование</th>
		<th>Склад</th>
		<th>Цена 1 ед.</th>
		<th>Кол-во</th>
		<th>Сумма</th>
		<th></th>
	</thead>
	<tbody>
<?
$rashod_list = $model->rashod;
if (count($rashod_list) > 0) {
	//$model->price_old = $model->rashod;	
	//$spec_old = '';
	$n=0;
	$total_sum = $total_qnt = 0;
	foreach ($rashod_list as $rashod) {
		$input_id = time();
?>
		<tr class="tovar-row">
			<td class="num"><?= ++$n ?></td>
			<td class="name"><?= $rashod->tovar->name ?><?= Html::hiddenInput("tovar_list[{$input_id}][rashod_id]", $rashod->id);?><?= Html::hiddenInput("tovar_list[{$input_id}][tovar_id]", $rashod->tovar->id, ['class'=>'tovar_id']);?></td>
			<td class="sklad_id"><?= $rashod->sklad->name ?><?= Html::hiddenInput("tovar_list[{$input_id}][sklad_id]", $rashod->sklad->id);?></td>
			<td class="price"><?= $rashod->price ?></td>
			<td class="amount"><?= Html::input('text',"tovar_list[{$input_id}][amount]",$rashod->amount,["class"=>"form-control amount"]); $total_qnt = $total_qnt + $rashod->amount; ?></td>
			<td class="sum"><?= $rashod->price * $rashod->amount; $total_sum = $total_sum + ($rashod->price * $rashod->amount) ?></td>
			<td><button type="button" class="btn btn-default btn-sm" aria-label="Удалить"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>
		</tr>
<?	
	}
}	
//echo '<pre>';print_r($model->rashod);echo '</pre>';
?>	
		<tr id="last_row">
			<th colspan="3" class="text-right"><?= Html::activeLabel($model,'discount')?>: <?= Html::activeInput('text',$model, 'discount',['class'=>'form-control','style'=>'width:auto; display:inline-block']) ?></th>
			<th colspan="" class="text-right">Итого</th>
			<th id="total_qnt"><?=$total_qnt?></th>
			<th id="total_sum"><?=$total_sum?></th>
			<td></td>
		</tr>
	</tbody>
	</table>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<a class="btn btn-default various fancybox.iframe" data-fancybox-type="iframe" href="<?php echo Url::toRoute("/tovar/popup") ?>">Добавить товар</a>
		</div>			
	</div>
				
	<?//= $form->field($model, 'discount')->textInput(['maxlength' => true]) ?>			
					
				</div>
			</div>
		</div>

	</div>	
	
	<div class="row">
		<div class="col-sm-3">		  
			<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>		    
		</div>          
	</div>	
	
    <?php ActiveForm::end(); ?>
    	
</div>
<script>
$(document).ready(function() {
	set_regions();
	// выбрали регион
	function change_region()
	{
		$.ajax({
			type: "POST",
			dataType: "JSON",
			data: {'code' : $('select[name="Client[region_id]"] option:selected').val()},
			url: "<?php echo Url::toRoute('/kladr/area-list') ?>",
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
			url: "<?php echo Url::toRoute('/kladr/city-list') ?>",
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
			url: "<?php echo Url::toRoute('/kladr/settlement-list') ?>",
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
	function set_regions() {				
		$('select[name="Client[region_id]"]').change(change_region);
		$('select[name="Client[area_id]"]').bind('change',function(){change_area(null)});
		$('select[name="Client[city_id]"]').bind('change',function(){change_city(null)});			
	}
})
</script>