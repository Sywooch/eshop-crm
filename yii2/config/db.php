<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=db_erp',
    'username' => 'db_erp',
    'password' => 'psswd123',
    'charset' => 'utf8',
    /*'on afterOpen' => function($event) {
     	$event->sender->createCommand("SET time_zone = '+5:00'")->execute();
	}*/
];
