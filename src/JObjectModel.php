<?php

namespace Jgrasp\Toolkit;

use Shop;

class JObjectModel extends \ObjectModel
{
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (self::isMultiShopStatic() && !Shop::isTableAssociated($this->def['table'])) {
            Shop::addTableAssociation(static::$definition['table'], array('type' => 'shop'));
        }

        $tableLang = $this->def['table'].'_lang';

        if (self::isMultiLangStatic() && !Shop::isTableAssociated($tableLang)) {
            Shop::addTableAssociation($tableLang, array('type' => 'fk_shop'));
        }

        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        $object = parent::add($autodate, $null_values);

        return $object;
    }

    public static function getRegularFields(): array
    {
        return array_filter(static::$definition['fields'], function ($field) {
            return (!isset($field['lang']) || $field['lang'] !== true) && (!isset($field['shop']) || $field['shop'] !== true);
        });
    }

    public static function getMultiLangFields(): array
    {
        return array_filter(static::$definition['fields'], function ($field) {
            return isset($field['lang']) && $field['lang'] === true;
        });
    }

    public static function getMultiShopFields(): array
    {
        return array_filter(static::$definition['fields'], function ($field) {
            return isset($field['shop']) && $field['shop'] === true;
        });
    }

    public static function isMultiShopStatic(): bool
    {
        return isset(static::$definition['multilang_shop']) && static::$definition['multilang_shop'] === true;
    }

    public static function isMultiLangStatic(): bool
    {
        return isset(static::$definition['multilang']) && static::$definition['multilang'] === true;
    }
}