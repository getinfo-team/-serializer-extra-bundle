<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Mapping;

class AttributeMetadata implements AttributeMetadataInterface
{
    /** @var string */
    private $name;

    /** @var bool */
    private $exclude;

    /** @var bool */
    private $expose;

    /** @var string|null */
    private $getter;

    /** @var string|null */
    private $setter;

    /** @var string|null */
    private $converter;

    /** @var array */
    private $options;

    public function __construct(
        string $name,
        bool $exclude = false,
        bool $expose = false,
        ?string $getter = null,
        ?string $setter = null,
        ?string $converter = null,
        array $options = []
    )
    {
        $this->name = $name;
        $this->exclude = $exclude;
        $this->expose = $expose;
        $this->getter = $getter;
        $this->setter = $setter;
        $this->converter = $converter;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isExclude(): bool
    {
        return $this->exclude;
    }

    public function isExpose(): bool
    {
        return $this->expose;
    }

    public function getter(): ?string
    {
        return $this->getter;
    }

    public function setter(): ?string
    {
        return $this->setter;
    }

    public function getConverter(): ?string
    {
        return $this->converter;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}