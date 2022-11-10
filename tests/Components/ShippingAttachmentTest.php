<?php

namespace Leadvertex\Plugin\Components\Logistic\Components;

use Leadvertex\Plugin\Components\Logistic\Exceptions\ShippingAttachmentException;
use PhpDto\Uri\Uri;
use PHPUnit\Framework\TestCase;

class ShippingAttachmentTest extends TestCase
{
    private ShippingAttachment $shippingAttachment;
    private Uri $uri;

    protected function setUp(): void
    {
        $this->uri = new Uri('https://test.dev');
        $this->shippingAttachment = new ShippingAttachment('my_attach_name', $this->uri);
    }

    public function testGetName(): void
    {
        $this->assertSame('my_attach_name', $this->shippingAttachment->getName());
    }

    public function testGetUri(): void
    {
        $this->assertSame($this->uri, $this->shippingAttachment->getUri());
    }

    public function invalidNames(): array
    {
        return [
            [''],
            ['    '],
            [str_repeat('name', 500)],
        ];
    }

    /**
     * @dataProvider invalidNames
     *
     * @param string $name
     * @return void
     * @throws ShippingAttachmentException
     */
    public function testFailedCreate(string $name): void
    {
        $this->expectException(ShippingAttachmentException::class);
        $this->shippingAttachment = new ShippingAttachment($name, $this->uri);
    }

    public function testJsonSerialize(): void
    {
        $this->assertSame([
            'name' => 'my_attach_name',
            'uri' => $this->uri->get(),
        ], $this->shippingAttachment->jsonSerialize());
    }

}