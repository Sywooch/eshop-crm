<?php

$params = require(__DIR__ . '/params.php');

$config = [
	'id' => 'esshop-crm',
	'name' => 'ESHOP-CRM',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log','admin'],
	'language' => 'ru-RU',
	'timeZone' => 'Asia/Yekaterinburg',//'Europe/Samara',
	'components' => [
		'request' => [
			// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
			'cookieValidationKey' => 'vPiR42tchDpUQsTJwXs-TumuliBJyFHQ',
		],
		/*'cache' => [
		'class' => 'yii\caching\FileCache',
		],*/
		'user' => [
			'identityClass' => 'app\modules\user\models\User',
			'enableAutoLogin' => true,
			'loginUrl' => ['user/default/login'],
		],
		'errorHandler' => [
			'errorAction' => 'site/error',
		],
		'assetManager' => [
			'linkAssets' => true,
		],
		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => true,
		],
		'db' => require(__DIR__ . '/db.php'),
		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error'],
					'logFile' => '@app/runtime/logs/web-error.log'
				],
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['warning'],
					'logFile' => '@app/runtime/logs/web-warning.log'
				],
				[
					'class' => 'yii\log\DbTarget',
					'exportInterval' => 1,
					'categories' => ['logged','order_list', 'order_status'],
					//'categories' => ['modules/user/default/*'],
					'levels' => ['info'],
					'logVars' => [],
				],  
			],
		],		
		'urlManager' => [
			'enablePrettyUrl' => true,
			//'enableStrictParsing' => true,
			'showScriptName' => false,
			//'suffix' => ' / '
			/*'rules' => [
			'<module:\w+>/<controller:\w+>/<action:(\w|-)+>' => '<module>/<controller>/<action>',
			'<module:\w+>/<controller:\w+>/<action:(\w|-)+>/<id:\d+>' => '<module>/<controller>/<action>',
			'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			]*/
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
		],
		'formatter' => [
			'dateFormat' => 'Y-MM-dd',
			'timeFormat' => 'H:mm:ss',
			'datetimeFormat' => 'Y-MM-dd H:mm:ss',
			'defaultTimeZone' => 'Europe/Moscow',
		],
		'sms' => [
			'class' => 'Zelenin\yii\extensions\Sms',
			'api_id' => ''
		],
	],
	'params' => $params,
	'modules' => [
		'admin' => [
			'class' => 'mdm\admin\Module',
			/*'menus' => [
			'assignment' => [
			'label' => 'Grand Access' // change label
			],
			'route' => null, // disable menu
			],*/
		],
		'user' => [
			'class' => 'app\modules\user\Module',
		],

	],
	'as AccessBehavior' => [
		'class' => 'mdm\admin\classes\AccessControl',
		//'class' => AccessBehavior::className(),
		'allowActions' => [
			'site/index',
			'site/error',
			'user/default/*',
			'exchange/*'			
		],		
	]
];

if(YII_ENV_DEV)
{
	// configuration adjustments for 'dev' environment
	$config['bootstrap'][] = 'debug';
	$config['modules']['debug'] = [
		'class' => 'yii\debug\Module',
		//'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*']
	];

	$config['bootstrap'][] = 'gii';
	$config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		//'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*']
	];
}

return $config;
