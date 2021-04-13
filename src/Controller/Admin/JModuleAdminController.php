<?php


namespace Jgrasp\Toolkit\Controller\Admin;


use Configuration;
use Jgrasp\Toolkit\JObjectModel;
use ModuleAdminController;
use ObjectModel;
use ReflectionClass;
use Shop;
use Tools;
use Validate;

class JModuleAdminController extends ModuleAdminController
{
    private $autoBuild = false;

    public function __construct()
    {
        parent::__construct();

        $this->loadEntity();
    }

    public function setAutoBuild($auto = false): self
    {
        $this->autoBuild = $auto;

        return $this;
    }

    public function ajaxProcessUpdatePositions()
    {
        $tableId = substr($this->identifier, 3, strlen($this->identifier));

        $positions = Tools::getValue($tableId);
        $langId = $this->context->language->id;

        if (is_array($positions)) {
            foreach ($positions as $position => $value) {
                $values = explode('_', $value);
                $objectId = (int)$values[2];

                $object = new $this->className($objectId, $langId, Shop::getContextShopID());

                if (!Validate::isLoadedObject($object)) {
                    continue;
                }

                $object->position = $position;
                $object->save();
            }
        }

        die(true);
    }

    protected function loadEntity(): void
    {
        if ($this->autoBuild && $this->className) {

            $class = new ReflectionClass($this->className);

            if (!$class->isSubclassOf(JObjectModel::class)) {
                throw new \Exception("Class ".$this->className." should be an instance of JObjectModel");
            }

            $definition = $class->getStaticPropertyValue('definition');

            $this->bootstrap = true;
            $this->lang = true;
            $this->table = $definition['table'];
            $this->default_form_language = $this->context->language->id;
            $this->multishop_context = true;
            $this->identifier = $definition['primary'];
            $this->position_identifier = $definition['primary'];
            $this->_orderBy = 'a!'.$this->identifier;
            $this->_orderWay = 'ASC';

            $selectFields = [];

            foreach (call_user_func([$this->className, 'getRegularFields']) as $fieldName => $field) {
                $selectFields[] = 'a.'.$fieldName;
            }

            foreach (call_user_func([$this->className, 'getMultiLangFields']) as $fieldName => $field) {
                $selectFields[] = 'b.'.$fieldName;
            }

            foreach (call_user_func([$this->className, 'getMultiShopFields']) as $fieldName => $field) {
                $selectFields[] = 'sa.'.$fieldName;
            }

            $this->_select = implode(',', $selectFields);

            if (call_user_func([$this->className, 'isMultiShopStatic'])) {
                $this->_join = 'LEFT JOIN '._DB_PREFIX_.$this->table.'_shop sa
                        ON (b.`id_shop` = sa.`id_shop` AND b.`'.$this->identifier.'` = sa.`'.$this->identifier.'`)';
            }

            if (!$this->module->isShopContext()) {
                $this->_where = ' AND b.`id_shop` = '.Configuration::get('PS_SHOP_DEFAULT');
                $this->_group = 'GROUP BY a.`'.$this->identifier.'`';
                $this->_defaultOrderBy = $this->identifier;
                $this->_orderBy = $this->identifier;
            } else {
                $this->_where = ' AND b.id_shop='.Shop::getContextShopID();
            }
        }
    }
}