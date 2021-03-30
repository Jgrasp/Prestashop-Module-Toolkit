<?php


namespace Jgrasp\Toolkit\Controller\Admin;


use Configuration;
use Jgrasp\Toolkit\JObjectModel;
use ModuleAdminController;
use ObjectModel;
use ReflectionClass;
use Shop;

class JModuleAdminController extends ModuleAdminController
{
    private $autoBuild = false;

    public function __construct()
    {
        parent::__construct();

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

            if (call_user_func([$this->className, 'isMultishopStatic'])) {
                $this->_join = 'LEFT JOIN '._DB_PREFIX_.$this->table.'_shop c 
                        ON (b.`id_shop` = c.`id_shop` AND b.`'.$this->identifier.'` = bs.`'.$this->identifier.'`)';
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

    public function setAutoBuild($auto = false): self
    {
        $this->autoBuild = $auto;

        return $this;
    }
}