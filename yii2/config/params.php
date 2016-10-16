<?php
//if(isset($_COOKIE['select_user_shop_menu'])) $usershop = print_r(unserialize($_COOKIE['select_user_shop_menu']),true);	
//if(isset(Yii::$app->request->cookies['select_user_shop_menu'])) $usershop = (Yii::$app->request->cookies['select_user_shop_menu']);	
//else $usershop = 0;
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'admin@example.com',
    'user.passwordResetTokenExpire' => 3600, 
    'user.current_shop' => 0,
    'salt' => 'lrf%salt',
];
