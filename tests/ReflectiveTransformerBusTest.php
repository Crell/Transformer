<?php

/**
 * This file is part of the Transformer library.
 *
 * (c) Larry Garfield <larry@garfieldtech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Crell\Transformer
 */

namespace Crell\Transformer\Tests;

use Crell\Transformer\ReflectiveTransformerBus;
use Crell\Transformer\TransformerBus;

class ReflectiveTransformerBusTest extends TransformerBusTest
{

    /**
     * {@inheritdoc}
     */
    protected function createTransformerBus($target): TransformerBus {
        return new ReflectiveTransformerBus($target);
    }

    public function testSimpleAutomaticMap(): void
    {
        $transformer = function(TestA $a) {
            return new TestB();
        };

        $bus = $this->createTransformerBus(TestB::CLASSNAME);
        $bus->setAutomaticTransformer($transformer);

        $result = $bus->transform(new TestA());

        static::assertInstanceOf(TestB::CLASSNAME, $result);
    }

    public function testMultistepAutomaticMap(): void
    {
        $ATransformer = function(TestA $a) {
            return new TestB();
        };
        $BTransformer = function(TestB $a) {
            return new TestC();
        };

        $bus = $this->createTransformerBus(TestC::CLASSNAME);
        $bus->setAutomaticTransformer($ATransformer);
        $bus->setAutomaticTransformer($BTransformer);

        $result = $bus->transform(new TestA());

        static::assertInstanceOf(TestC::CLASSNAME, $result);
    }

    /**
     * @param string $from
     *
     * @param string $to
     * @param mixed $transformer
     *   A transformer definition (aka callable definition).
     * @param string $exception
     *   The type of an Exception that should be thrown in case of an unsuccessful
     *   transformation. Leave null for test sets that should succeed.
     *
     * @dataProvider transformerDefinitionProvider
     */
    public function testCallableOptions($from, $to, $transformer, $exception = null): void
    {
        if ($exception) {
            $this->expectException($exception);
        }

        $bus = $this->createTransformerBus($to);
        $bus->setAutomaticTransformer($transformer);

        $result = $bus->transform(new $from());
        static::assertInstanceOf($to, $result);
    }

    /**
     * Defines an array of transformers that convert from TestA to TestB.
     */
    public function transformerDefinitionProvider(): iterable
    {
        $defs = parent::transformerDefinitionProvider();

        // Invalid data that should fail.
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, function() { return new TestB(); }, 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, function($a) { return new TestB(); }, 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, function(TestC $c) { return new TestB(); }, 'Crell\Transformer\NoTransformerFoundException'];

        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [InvalidTransformers::CLASSNAME, 'staticNoParam'], 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [InvalidTransformers::CLASSNAME, 'staticNoType'], 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [InvalidTransformers::CLASSNAME, 'staticWrongType'], 'Crell\Transformer\NoTransformerFoundException'];

        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [new InvalidTransformers(), 'noParam'], 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [new InvalidTransformers(), 'noType'], 'Crell\Transformer\InvalidTransformerException'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [new InvalidTransformers(), 'wrongType'], 'Crell\Transformer\NoTransformerFoundException'];

        return $defs;
    }

}

class InvalidTransformers
{
    const CLASSNAME = __CLASS__;

    public function noParam()
    {
        return new TestB();
    }

    public function noType($a)
    {
        return new TestB();
    }

    public function wrongType(TestC $a)
    {
        return new TestB();
    }

    public static function staticNoParam()
    {
        return new TestB();
    }

    public static function staticNoType($a)
    {
        return new TestB();
    }

    public static function staticWrongType(TestC $a)
    {
        return new TestB();
    }
}
