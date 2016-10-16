<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

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
			'api_id' => '470c1f43-9745-b1f4-75be-8ceaaf51594e'
		],
	],
	'params' => $params,
];
