<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = array_merge(
	require(__DIR__ . '/db.php'),
	require(__DIR__ . '/db-local.php')
);

return [
	'id' => 'basic-console',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log', 'gii'],
	'controllerNamespace' => 'app\commands',
	'modules' => [
		'gii' => 'yii\gii\Module',
	],
	'components' => [
		'cache' => [
			'class' => 'yii\caching\FileCache',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'db' => $db,
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
			'api_id' => $params['sms.api_id']
		],
	],
	'params' => $params,
];
