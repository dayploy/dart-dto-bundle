<?php

namespace Dayploy\DartDtoBundle\Generator;

class FilenameService
{
    public function __construct(
        private readonly string $modelPath,
    ) {}

    /** store converted object to add to the top import */
    private $imports = [];

    public function clearImports()
    {
        $this->imports = [];
    }

    public function getImports(): array
    {
        return $this->imports;
    }

    // public function getObjectFromClassname(
    //     string $classname,
    // ): string {
    //     $elements = explode('\\', $classname);

    //     $objectName = end($elements);

    //     $this->imports[$objectName] = $this->getPathFromClassname(
    //         classname: $classname,
    //         prefixToRemove: 'App\\',
    //     );

    //     return $objectName;
    // }

    public function addUuidImport(): void
    {
        $this->imports['uuid'] = 'package:uuid/uuid_value.dart';
    }

    public function addApiDateServiceImport(): void
    {
        $this->imports['api_date_service'] = '/services/api_date_service.dart';
    }

    public function getObjectFromClassname(
        string $classname,
    ): string {
        $elements = explode('\\', $classname);

        $objectName = end($elements);

        $this->imports[$objectName] = $this->getPathFromClassname(
            classname: $classname,
            prefixToRemove: 'App\\',
        );

        return $objectName;
    }

    private function getPathFromClassname(
        string $classname,
        string $prefixToRemove,
    ): string {
        $classname = str_replace(
            $prefixToRemove,
            '',
            $classname,
        );

        $classname = str_replace(
            '\\',
            '/',
            $classname,
        );
        $classname = $this->modelPath . '/' . $classname;

        return '/' . StringCase::camelToSnake(str_replace(
            '//',
            '/',
            $classname,)
        ) . '.dart';
    }
}
