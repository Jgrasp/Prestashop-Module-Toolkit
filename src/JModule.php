<?php

namespace Jgrasp\Toolkit;

use Context;
use Jgrasp\Toolkit\Manager\ModelManager;
use Module;

class JModule extends Module
{

    private $entityManager;

    public function __construct($name = null, Context $context = null)
    {
        $this->entityManager = new ModelManager();

        parent::__construct($name, $context);
    }

    public function install()
    {
        return parent::install() && $this->entityManager->install();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->entityManager->uninstall();
    }

    protected function getEntityManager(): ModelManager
    {
        return $this->entityManager;
    }

}