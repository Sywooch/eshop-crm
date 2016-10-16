<?php
namespace app\components;
class TotalCount {
    public static function pageTotal($provider, $fieldName)
    {
        $total=0;
        foreach($provider as $item){
            $total+= abs($item[$fieldName]);
        }
        return $total;
    }
}