<?php


namespace Jgrasp\Toolkit\Sql\Factory;


class Field
{
    private $name;

    private $type;

    private $required;

    private $defaultValue;

    public function __construct(string $name, string $type, bool $required = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->defaultValue = null;
    }

    public static function buildFromArray(array $data): self
    {
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            throw new \Exception('Name value is missing');
        }

        if (!array_key_exists('type', $data) || empty($data['type'])) {
            throw new \Exception('Type value is missing');
        }

        $field = new self($data['name'], (string)$data['type']);

        if (array_key_exists('required', $data)) {
            $field->setRequired((bool)$data['required']);
        }

        return $field;
    }

    public function getSql(): string
    {
        return implode(' ', $this->getSqlParts()).',';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): Field
    {
        $this->required = $required;
        return $this;
    }


    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    protected function getNameSql(): string
    {
        return '`'.$this->getName().'`';
    }

    protected function getTypeSql(): string
    {
        return "";
    }

    protected function getRequiredSql(): string
    {
        return $this->required ? 'NOT NULL' : 'NULL';
    }

    protected function getDefaultValueSql(): string
    {
        return !is_null($this->defaultValue) ? $this->defaultValue : "";
    }

    protected function getSqlParts(): array
    {
        return [
            $this->getNameSql(),
            $this->getTypeSql(),
            $this->getRequiredSql(),
            $this->getDefaultValue()
        ];
    }

}