<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class HtmlField extends Field
{
    public function getType(): string
    {
        return 'TEXT';
    }
}