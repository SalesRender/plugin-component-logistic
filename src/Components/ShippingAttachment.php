<?php

namespace Leadvertex\Plugin\Components\Logistic\Components;

use JsonSerializable;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingAttachmentException;
use PhpDto\Uri\Uri;

class ShippingAttachment implements JsonSerializable
{
    private string $name;

    private Uri $uri;

    /**
     * @throws ShippingAttachmentException
     */
    public function __construct(string $name, Uri $uri)
    {
        $name = trim($name);
        if (mb_strlen($name) <= 5 || mb_strlen($name) >= 255) {
            throw new ShippingAttachmentException('Attachment name should be more than 5 chars and less than 255 chars');
        }
        $this->name = $name;

        $this->uri = $uri;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'uri' => $this->getUri()->get(),
        ];
    }

}