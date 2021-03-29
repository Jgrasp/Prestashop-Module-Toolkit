<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class IntegerField extends NumericField
{
    public function __construct(string $name, bool $required = false)
    {
        parent::__construct($name, $this->getType(), $required);
    }

    public function getType(): string
    {
        return 'INT';
    }
}