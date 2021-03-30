<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class PrimaryField extends IntegerField
{
    public function __construct(string $name)
    {
        parent::__construct($name, true);

        $this
            ->setSize(10)
            ->setUnsigned(true);
    }

    public function getTypeSql(): string
    {
        return parent::getTypeSql().' AUTO_INCREMENT';
    }
}