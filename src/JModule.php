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

    public function install(): bool
    {
        return parent::install() && $this->entityManager->install();
    }

    public function uninstall(): bool
    {
        return parent::uninstall() && $this->entityManager->uninstall();
    }

    public function isShopContext(): bool
    {
        return !is_null(Shop::getContextShopID());
    }

    protected function getEntityManager(): ModelManager
    {
        return $this->entityManager;
    }

}