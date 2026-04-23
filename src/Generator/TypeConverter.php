<?php

namespace Dayploy\DartDtoBundle\Generator;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BackedEnumType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\EnumType;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\Uid\Uuid;

class TypeConverter
{
    public function __construct(
        private FilenameService $filenameService,
    ) {
    }

    public function convertType(
        Type $type,
    ): string {
        switch ($type::class) {
            case ObjectType::class:
                /** @var ObjectType $type */
                if ($type->getClassName() === Uuid::class) {
                    $this->filenameService->addUuidImport();
                    return 'UuidValue';
                }
                if ($type->getClassName() === Collection::class) {
                    return 'List';
                }
                if ($type->getClassName() === DateTimeImmutable::class) {
                    return 'DateTime';
                }

                if ($type->getClassName() === UploadedFile::class) {
                    return 'String';
                }
                if ($type->getClassName() === File::class) {
                    return 'String';
                }

                return $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );
            case BuiltinType::class:
                /** @var BuiltinType $type */
                if ($type->getTypeIdentifier()->value === 'int') {
                    return 'int';
                }
                if ($type->getTypeIdentifier()->value === 'float') {
                    return 'double';
                }
                if ($type->getTypeIdentifier()->value === 'array') {
                    return 'List';
                }
                if ($type->getTypeIdentifier()->value === 'bool') {
                    return 'bool';
                }
                if ($type->getTypeIdentifier()->value === 'string') {
                    return 'String';
                }

                return $type->__toString();
            case UnionType::class:
                /** @var UnionType $type */
                $types = $type->getTypes();
                $str = '';
                foreach ($types as $index => $subType) {
                    $str .= $this->convertType($subType);
                    if (($index + 1) < count($types)) {
                        $str .= ' | ';
                    }
                }

                return $str;
            case BackedEnumType::class:
                /** @var BackedEnumType $type */
                return $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );
            case EnumType::class:
                /** @var EnumType $type */
                return '\\'.$type->getClassName();
            case CollectionType::class:
                /** @var CollectionType $type */
                if ($type->isList()) {
                    return $this->convertType($type->getWrappedType());
                }

                return $this->convertType($type->getWrappedType());
            case GenericType::class:
                /** @var GenericType $type */
                $variableType = $type->getVariableTypes() ? $type->getVariableTypes()[1] : null;
                $variableTypePrefix = '';
                if ($variableType) {
                    $variableTypePrefix =  '<'.$this->convertType($variableType).'>';
                }

                return $this->convertType($type->getWrappedType()).$variableTypePrefix;
            case NullableType::class:
                /** @var NullableType $type */
                return $this->convertType($type->getWrappedType()).'?';
        }

        throw new \LogicException('Class '.$type::class.' not handled');
    }
}
