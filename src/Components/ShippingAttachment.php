<?php

namespace Leadvertex\Plugin\Components\Logistic\Components;

use JsonSerializable;
use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingAttachmentException;
use PhpDto\Uri\Exception\UriException;
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
        if (mb_strlen($name) < 1 || mb_strlen($name) > 255) {
            throw new ShippingAttachmentException('Attachment name should be more than 1 chars and less than 255 chars');
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

    /**
     * @param array $data
     * @return ShippingAttachment
     * @throws ShippingAttachmentException
     * @throws UriException
     */
    public static function createFromArray(array $data): self
    {
        return new ShippingAttachment($data['name'], new Uri($data['uri']));
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'uri' => $this->getUri()->get(),
        ];
    }

}