<?php

namespace Dayploy\DartDtoBundle\Generator;

use Symfony\Component\TypeInfo\Type;

class ToJsonTypeGenerator
{
    private static string $template = '      "<fieldName>": <fieldName><type>,';

    public function __construct(
        private ToJsonTypeConverter $typeConverter,
    ) {
    }

    public function generate(
        string $fieldName,
        Type $type,
    ): string {
        $replacements = [
            '<type>' => $this->typeConverter->convertType($type),
            '<fieldName>' => $fieldName,
        ];

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            static::$template
        );

        return $method;
    }
}
