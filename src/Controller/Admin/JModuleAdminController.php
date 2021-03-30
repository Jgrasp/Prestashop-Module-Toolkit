<?php


namespace Jgrasp\Toolkit\Controller\Admin;


use ModuleAdminController;
use ObjectModel;
use ReflectionClass;

class JModuleAdminController extends ModuleAdminController
{
    private $autoBuild = false;

    public function __construct()
    {
        parent::__construct();

        if ($this->autoBuild && $this->className) {

            $class = new ReflectionClass($this->className);

            if (!$class->isSubclassOf(ObjectModel::class)) {
                throw new \Exception("Class ".$this->className." should be an instance of ObjectModel");
            }

            $definition = $class->getStaticPropertyValue('definition');

            $this->bootstrap = true;
            $this->lang = true;
            $this->table = $definition['table'];
            $this->default_form_language = $this->context->language->id;
            $this->multishop_context = true;
            $this->position_identifier = $definition['primary'];
            $this->identifier = $definition['primary'];
        }
    }

    public function setAutoBuild($auto = false): self
    {
        $this->autoBuild = $auto;

        return $this;
    }
}