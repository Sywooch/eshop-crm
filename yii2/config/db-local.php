<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=linerfmail_crm',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    /*'on afterOpen' => function($event) {
     	$event->sender->createCommand("SET time_zone = '+5:00'")->execute();
	}*/
];
