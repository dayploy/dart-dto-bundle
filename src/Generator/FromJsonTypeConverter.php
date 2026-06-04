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

class FromJsonTypeConverter
{
    public function __construct(
        private FilenameService $filenameService,
    ) {}

    public function convertType(
        string $fieldName,
        Type $type,
    ): string {
        return $this->parseExpression(sprintf('json[\'%s\']', $fieldName), $type);
    }

    private function parseExpression(
        string $expression,
        Type $type,
    ): string {
        switch ($type::class) {
            case ObjectType::class:
                /** @var ObjectType $type */
                if ($type->getClassName() === Uuid::class) {
                    $this->filenameService->addUuidImport();
                    return sprintf('UuidValue.fromString(%s as String)', $expression);
                }
                if ($type->getClassName() === Collection::class) {
                    throw new \LogicException('The Collection property is missing a specific type. Please add a PHPDoc to specify its elements (e.g., /** @var MyDto[] */).');
                }
                if ($type->getClassName() === DateTimeImmutable::class) {
                    $this->filenameService->addApiDateServiceImport();
                    return sprintf('DateTime.parse(%s as String)', $expression);
                }

                if ($type->getClassName() === UploadedFile::class) {
                    return 'String';
                }
                if ($type->getClassName() === File::class) {
                    return 'String';
                }

                $classname = $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );

                return sprintf('%s.fromJson(%s as Map<String, dynamic>)', $classname, $expression);
            case BuiltinType::class:
                /** @var BuiltinType $type */
                if ($type->getTypeIdentifier()->value === 'int') {
                    return sprintf('%s as int', $expression);
                }
                if ($type->getTypeIdentifier()->value === 'float') {
                    return sprintf('%s as double', $expression);
                }
                if ($type->getTypeIdentifier()->value === 'array') {
                    throw new \LogicException('The array property is missing a specific type. Please add a PHPDoc to specify its elements (e.g., /** @var MyDto[] */).');
                }
                if ($type->getTypeIdentifier()->value === 'bool') {
                    return sprintf('%s as bool', $expression);
                }
                if ($type->getTypeIdentifier()->value === 'string') {
                    return sprintf('%s as String', $expression);
                }

                return $type->__toString();
            case UnionType::class:
                /** @var UnionType $type */
                $types = $type->getTypes();
                $str = '';
                foreach ($types as $index => $subType) {
                    $str .= $this->parseExpression($expression, $subType);
                    if (($index + 1) < count($types)) {
                        $str .= ' | ';
                    }
                }

                return $str;
            case BackedEnumType::class:
                /** @var BackedEnumType $type */
                $classname =  $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );

                return sprintf('%s.fromValue(%s)', $classname, $expression);
            case EnumType::class:
                /** @var EnumType $type */
                return '\\' . $type->getClassName();
            case CollectionType::class:
                /** @var CollectionType $type */
                $valueType = $type->getCollectionValueType();
                
                if ($valueType && !($valueType instanceof BuiltinType && $valueType->getTypeIdentifier()->value === 'mixed')) {
                    $mappedE = $this->parseExpression('e', $valueType);
                    return sprintf('(%s as List<dynamic>).map((e) => %s).toList()', $expression, $mappedE);
                }

                throw new \LogicException('The collection/array property is missing a specific type. Please add a PHPDoc to specify its elements (e.g., /** @var MyDto[] */).');
            case GenericType::class:
                /** @var GenericType $type */
                return $this->parseExpression($expression, $type->getWrappedType());
            case NullableType::class:
                /** @var NullableType $type */
                $wrappedType = $type->getWrappedType();

                if ($wrappedType instanceof ObjectType && $wrappedType->getClassName() === DateTimeImmutable::class) {
                    return sprintf(
                        '%s != null ? DateTime.tryParse(%s as String) : null',
                        $expression,
                        $expression
                    );
                }

                return sprintf(
                    '%s != null ? %s : null',
                    $expression,
                    $this->parseExpression($expression, $type->getWrappedType()),
                );
        }

        throw new \LogicException('Class ' . $type::class . ' not handled');
    }
}
