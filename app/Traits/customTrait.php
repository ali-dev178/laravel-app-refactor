<?php

namespace App\Traits;

trait customTrait
{

    public static function formatInput($data, $attributes = []) {
        $strAttrs = $boolAttrs = [];

        if (isset($attributes)) {
            if (isset($attributes['strAttrs']) || isset($attributes['boolAttrs'])) {
                $strAttrs = isset($attributes['strAttrs']) ? $attributes['strAttrs'] : $strAttrs;
                $boolAttrs = isset($attributes['boolAttrs']) ? $attributes['boolAttrs'] : $boolAttrs;
            }else{
                $strAttrs = $attributes;
            }
        }
        
        foreach ($strAttrs as $attr) {
            $data[$attr] = isset($data[$attr]) && !empty($data[$attr]) ? $data[$attr] : '';
        }

        foreach ($boolAttrs as $attr) {
            $data[$attr] = isset($data[$attr]) && $data[$attr] == 'true' ? 'yes' : 'no';
        }

        return $data;
    }
}
