<?php

namespace Jgrasp\Toolkit;

use Shop;

class JObjectModel extends \ObjectModel
{
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (isset(static::$definition['multilang_shop']) && static::$definition['multilang_shop'] === true && Shop::isTableAssociated($this->def['table'])) {
            Shop::addTableAssociation(static::$definition['table'], array('type' => 'shop'));
        }

        $tableLang = $this->def['table'].'_lang';

        if (isset(static::$definition['multilang']) && static::$definition['multilang'] === true && Shop::isTableAssociated($tableLang)) {
            Shop::addTableAssociation($tableLang, array('type' => 'fk_shop'));
        }

        parent::__construct($id, $id_lang, $id_shop);
    }
}