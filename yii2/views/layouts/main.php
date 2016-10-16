<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;
use mdm\admin\classes\MenuHelper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody();?>

<div class="wrap1">
 <?php
    NavBar::begin([
        'brandLabel' => 'LRF CRM',
        'brandUrl' => Yii::$app->homeUrl,        
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'innerContainerOptions' =>['class'=>'container-fluid']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav','activateParents' =>TRUE],
        'activateParents' => true,
        'items' => MenuHelper::getAssignedMenu(Yii::$app->user->id),//$root = null, $callback = null, $refresh = true),
    ]);
    /*echo Nav::widget([
        'options' => ['class' => 'navbar-nav','activateParents' =>TRUE],
        'activateParents' => true,
        'items' => [
			['label' => 'Заявки', 'url' => ['/orders'], 'active' => Yii::$app->controller->id == 'orders', 'visible' => Yii::$app->user->can('orders'),],
			['label' => 'Клиенты', 'url' => ['/client'], 'active' => Yii::$app->controller->id == 'client', 'visible' => Yii::$app->user->can('client')],
			['label' => 'Товары',
				'items' => [
					['label' => 'Товар', 'url' => ['/tovar'], 'active' => Yii::$app->controller->id == 'tovar', 'visible' => Yii::$app->user->can('tovar')],
					['label' => 'Приход', 'url' => ['/tovar-prihod'], 'active' => Yii::$app->controller->id == 'tovar-prihod', 'visible' => Yii::$app->user->can('tovar-prihod')],
					['label' => 'Расход', 'url' => ['/tovar-rashod'], 'active' => Yii::$app->controller->id == 'tovar-rashod', 'visible' => Yii::$app->user->can('tovar-rashod')],
					['label' => 'Остатки', 'url' => ['/tovar-balance'], 'active' => Yii::$app->controller->id == 'tovar-balance', 'visible' => Yii::$app->user->can('tovar-balance')],
					['label' => 'Цены', 'url' => ['/tovar-costs'], 'active' => Yii::$app->controller->id == 'tovar-costs', 'visible' => Yii::$app->user->can('tovar-costs')],
				],
				'visible' => (Yii::$app->user->can('tovar') or Yii::$app->user->can('tovar-rashod') or Yii::$app->user->can('tovar-prihod') or Yii::$app->user->can('tovar-balance') or Yii::$app->user->can('tovar-costs')),
			],			
			['label' => 'Деньги',
				'items' => [
					['label' => 'Приход/расход', 'url' => ['/money'], 'active' => Yii::$app->controller->id == 'money', 'visible' => Yii::$app->user->can('money')],
					['label' => 'Баланс', 'url' => ['/money-balance'], 'active' => Yii::$app->controller->id == 'money-balance', 'visible' => Yii::$app->user->can('money-balance')],
					['label' => 'Статьи расход/прихода', 'url' => ['/money-item'], 'active' => Yii::$app->controller->id == 'money-item', 'visible' => Yii::$app->user->can('money-item')],
					['label' => 'Средства приход/расхода', 'url' => ['/money-metod'], 'active' => Yii::$app->controller->id == 'money-metod', 'visible' => Yii::$app->user->can('money-metod')],
				],
				'visible' => (Yii::$app->user->can('money') or Yii::$app->user->can('money-item') or Yii::$app->user->can('money-metod')),
			],
			['label' => 'Отчеты',
				'items' => [
					['label' => 'По хостам', 'url' => ['/report/hosts'], 'active' => Yii::$app->controller->id == 'report/hosts', 'visible' => Yii::$app->user->can('report')],
					['label' => 'По заявкам', 'url' => ['/report/orders'], 'active' => Yii::$app->controller->id == 'report/orders', 'visible' => Yii::$app->user->can('report')],
					['label' => 'По менеджерам', 'url' => ['/report/managers'], 'active' => Yii::$app->controller->id == 'report/managers', 'visible' => Yii::$app->user->can('report')],
					['label' => 'По товарам', 'url' => ['/report/tovar'], 'active' => Yii::$app->controller->id == 'report/tovar', 'visible' => Yii::$app->user->can('report')],
					['label' => 'По приходу/расходу', 'url' => ['/report/inout'], 'active' => Yii::$app->controller->id == 'report/inout', 'visible' => Yii::$app->user->can('report')]
				],
				'visible' => Yii::$app->user->can('report'),
			],
			['label' => 'Утилиты',
				'items' => [
					['label' => 'Загрузка ЯД', 'url' => ['/utils/importstat'], 'active' => Yii::$app->controller->id == 'utils/importstat', 'visible' => (Yii::$app->user->can('utils/importstat') or Yii::$app->user->can('utils'))],
					['label' => 'Загрузка отправок', 'url' => ['/utils/importrpo'], 'active' => Yii::$app->controller->id == 'utils/importrpo', 'visible' => (Yii::$app->user->can('utils/importrpo') or Yii::$app->user->can('utils'))],
					['label' => 'Экспорт xls в Москву', 'url' => ['/utils/exportmsk'], 'active' => Yii::$app->controller->id == 'utils/exportmsk', 'visible' => (Yii::$app->user->can('utils/exportmsk') or Yii::$app->user->can('utils'))],
				],
				'visible' => (Yii::$app->user->can('utils/importstat') or Yii::$app->user->can('utils/exportmsk') or Yii::$app->user->can('utils/importrpo') or Yii::$app->user->can('utils')),
			],
			['label' => 'Доступ',
	            'items' => [
	                 ['label' => 'Роли', 'url' => '/permit/access/role','visible' => Yii::$app->user->can('permit/access'),],	                 
	                 ['label' => 'Разрешения', 'url' => '/permit/access/permission','visible' => Yii::$app->user->can('permit/access'),],
	                 ['label' => 'Пользователи', 'url' => '/user/admin','visible' => Yii::$app->user->can('user'),],
	            ],
	            'visible' => (Yii::$app->user->can('permit/access') or Yii::$app->user->can('user')),
        	],
		]
	]);*/
	?>
	
    <?php echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
		Yii::$app->user->isGuest ?
                ['label' => 'Войти', 'url' => ['/user/default/login']] :
                [
                    'label' => 'Выйти (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/user/default/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ],
        ],
    ]);?>
    
    <?php if(! Yii::$app->user->isGuest) {	?>
    
    <?= Html::beginForm(['/user/default/saveshops'], 'post', ['enctype' => 'multipart/form-data', 'class' => "navbar-form navbar-right"]) ?>
    <div class="form-group">
    	<?= Html::dropDownList('select_user_shop_menu',
    		(isset(Yii::$app->request->cookies['select_user_shop_menu']) ? Yii::$app->request->cookies['select_user_shop_menu'] : null),
    		\yii\helpers\ArrayHelper::map(Yii::$app->user->identity->shops, 'id', 'name'),['prompt'=>'--Выбрать магазин--','class'=>'form-control'])?>    	
    </div>
    <?= Html::submitButton('GO', ['class' => 'btn btn-default']) ?>
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
    <?= Html::endForm() ?>
    
    <?php } ?>
    
	<?php NavBar::end(); ?>

    <div class="container-fluid">
<!--    	<div class="row">
    		<div class="col-sm-12">
    			<?= Breadcrumbs::widget([
		            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
		        ]) ?>
		        
    		</div>
    	</div>-->
        <div class="row">
    		<div class="col-sm-12">
    			<?= Alert::widget(); ?>
        		<?= $content ?>
        	</div>
    	</div>
    </div>
</div>

<footer class="footer">
    <div class="container-fluid">
        <p class="pull-left">&copy; LRF Inc, <?= date('Y') ?></p>

        <p class="pull-right">Разработка <a href="http://leadrf.ru">LeadRF Studio</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
