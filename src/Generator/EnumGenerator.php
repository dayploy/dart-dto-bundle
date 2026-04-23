<?php

namespace Dayploy\DartDtoBundle\Generator;

use ReflectionClass;

class EnumGenerator
{
    private static string $classTemplate = '
enum <entityClassName> {
<entityBody>


  final String value;
  const <entityClassName>(this.value);
}
';

    public function generateEntityClass(
        ReflectionClass $reflectionClass,
    ): string {
        $placeHolders = [
            '<entityClassName>',
            '<entityBody>',
        ];

        $bodyReplacement = $this->generateEntityBody($reflectionClass);
        $entityClassName = $reflectionClass->getShortName();

        return str_replace($placeHolders, [
            $entityClassName,
            $bodyReplacement,
        ], static::$classTemplate);
    }

    protected function generateEntityBody(
        ReflectionClass $reflectionClass,
    ): string {
        $code = [];
        $cases = $reflectionClass->getConstants();

        $index = 0;
        foreach ($cases as $case) {
            $value = $case->value;
            if (is_string($value)) {
                $value = '"'.$value.'"';
            }

            $suffix = ($index === count($cases)-1) ? ';' : ',';
            $code[] = '  '.StringCase::snakeToCamel(strtolower($case->name)).'('.$value.')'.$suffix;

            $index++;
        }

        return implode("\n", $code);
    }
}
