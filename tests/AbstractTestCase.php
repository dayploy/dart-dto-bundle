<?php

namespace Dayploy\DartDtoBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractTestCase extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(\Dayploy\DartDtoBundle\DartDtoBundle::class);
        $kernel->handleOptions($options);
        $kernel->addTestConfig(__DIR__.'/config.yml');

        return $kernel;
    }
}
