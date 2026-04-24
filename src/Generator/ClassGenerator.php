<?php

namespace Dayploy\DartDtoBundle\Generator;

use Dayploy\DartDtoBundle\Attributes\DartDtoIgnore;
use Psr\Log\LoggerInterface;
use ReflectionClass;

class ClassGenerator
{
    private static string $classTemplate = '<imports>

class <entityClassName> {
<entityBody>


  <entityClassName>({
<constructorParameters>
  });
<generateFromJson><generateToJson>
}
';

    private static string $toJsonTemplate ='

  Map<String, dynamic> toJson() {
    return {
<parameters>
    };
  }
';

    private static string $fromJsonTemplate = '

  factory <entityClassName>.fromJson(Map<String, dynamic> json) {
    return <entityClassName>(
<generateFromJsonParameters>
    );
  }
';

    public function __construct(
        private LoggerInterface $logger,
        private Extractor $extractor,
        private TypeGenerator $typeGenerator,
        private FilenameService $filenameService,
        private ConstructorTypeGenerator $constructorTypeGenerator,
        private ToJsonTypeGenerator $toJsonTypeGenerator,
        private FromJsonTypeGenerator $fromJsonTypeGenerator,
    ) {}

    public function generateEntityClass(
        ReflectionClass $reflectionClass,
        bool $generateToJson,
        bool $generateFromJson,
    ): string {
        $placeHolders = [
            '<imports>',
            '<namespace>',
            '<entityClassName>',
            '<entityBody>',
            '<constructorParameters>',
            '<generateToJson>',
            '<generateFromJson>',
        ];
        $entityClassName = $reflectionClass->getShortName();
        $bodyReplacement = $this->generateEntityBody($reflectionClass);
        $constructorParametersReplacement = $this->generateEntityConstructorParameters($reflectionClass);

        $generateToJsonString = '';
        if ($generateToJson) {
            $toJsonParametersReplacement = $this->generateToJsonParameters($reflectionClass);
            $generateToJsonString = str_replace(['<parameters>'], [
                $toJsonParametersReplacement,
            ], static::$toJsonTemplate);
        }

        $generateFromJsonString = '';
        if ($generateFromJson) {
            $fromJsonParametersReplacement = $this->generateFromJsonParameters($reflectionClass);
            $generateFromJsonString = str_replace(['<entityClassName>','<generateFromJsonParameters>'], [
                $entityClassName,
                $fromJsonParametersReplacement,
            ], static::$fromJsonTemplate);
        }

        $importStrings = '';

        foreach ($this->filenameService->getImports() as $classname => $path) {
            // self referenced class does not add import
            if ($entityClassName === $classname) {
                continue;
            }

            $importStrings .= "import '$path';\n";
        }

        $this->filenameService->clearImports();

        return str_replace($placeHolders, [
            $importStrings,
            $reflectionClass->getNamespaceName(),
            $entityClassName,
            $bodyReplacement,
            $constructorParametersReplacement,
            $generateToJsonString,
            $generateFromJsonString,
        ], static::$classTemplate);
    }

    protected function generateEntityProperties(
        ReflectionClass $reflectionClass,
    ): string {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $this->logger->info('PROPERTY: ' . $propertyName);

            $attributes = $property->getAttributes();
            if (!$this->isPropertyIncluded($attributes)) {
                $this->logger->info('IGNORED');
                continue;
            }

            $type = $this->extractor->getType($reflectionClass->getName(), $propertyName);

            // the mixed type gives a null value
            if (null === $type) {
                continue;
            }
            $properties[] = $this->typeGenerator->generate($propertyName, $type);
        }

        return implode("\n\n", array_filter($properties));
    }

    protected function generateConstructorProperty(
        ReflectionClass $reflectionClass,
    ): string {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $this->logger->info('PROPERTY: ' . $propertyName);

            $attributes = $property->getAttributes();
            if (!$this->isPropertyIncluded($attributes)) {
                $this->logger->info('IGNORED');
                continue;
            }

            $type = $this->extractor->getType($reflectionClass->getName(), $propertyName);

            // the mixed type gives a null value
            if (null === $type) {
                continue;
            }
            $properties[] = $this->constructorTypeGenerator->generate($propertyName, $type);
        }

        return implode("\n\n", array_filter($properties));
    }

    protected function generateToJsonProperty(
        ReflectionClass $reflectionClass,
    ): string {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $this->logger->info('PROPERTY: ' . $propertyName);

            $attributes = $property->getAttributes();
            if (!$this->isPropertyIncluded($attributes)) {
                $this->logger->info('IGNORED');
                continue;
            }

            $type = $this->extractor->getType($reflectionClass->getName(), $propertyName);

            // the mixed type gives a null value
            if (null === $type) {
                continue;
            }
            $properties[] = $this->toJsonTypeGenerator->generate($propertyName, $type);
        }

        return implode("\n\n", array_filter($properties));
    }

     protected function generateFromJsonProperty(
        ReflectionClass $reflectionClass,
    ): string {
        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName = $property->getName();
            $this->logger->info('PROPERTY: ' . $propertyName);

            $attributes = $property->getAttributes();
            if (!$this->isPropertyIncluded($attributes)) {
                $this->logger->info('IGNORED');
                continue;
            }

            $type = $this->extractor->getType($reflectionClass->getName(), $propertyName);

            // the mixed type gives a null value
            if (null === $type) {
                continue;
            }
            $properties[] = $this->fromJsonTypeGenerator->generate($propertyName, $type);
        }

        return implode("\n\n", array_filter($properties));
    }

    protected function generateEntityBody(
        ReflectionClass $reflectionClass,
    ): string {
        $code = [];

        // EnumType
        $stubMethods = $this->generateEntityProperties($reflectionClass);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        return implode("\n", $code);
    }

    protected function generateEntityConstructorParameters(
        ReflectionClass $reflectionClass,
    ): string {
        $code = [];

        // EnumType
        $stubMethods = $this->generateConstructorProperty($reflectionClass);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        return implode("\n", $code);
    }

    protected function generateToJsonParameters(
        ReflectionClass $reflectionClass,
    ): string {
        $code = [];

        // EnumType
        $stubMethods = $this->generateToJsonProperty($reflectionClass);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        return implode("\n", $code);
    }

    protected function generateFromJsonParameters(
        ReflectionClass $reflectionClass,
    ): string {
        $code = [];

        // EnumType
        $stubMethods = $this->generateFromJsonProperty($reflectionClass);

        if ($stubMethods) {
            $code[] = $stubMethods;
        }

        return implode("\n", $code);
    }

    private function isPropertyIncluded(
        array $attributes,
    ): bool {
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === DartDtoIgnore::class) {
                return false;
            }
        }

        return true;
    }
}
