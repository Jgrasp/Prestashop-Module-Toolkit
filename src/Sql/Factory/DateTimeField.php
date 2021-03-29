<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class DateTimeField extends Field
{
    public function getType(): string
    {
        return 'DATETIME';
    }
}