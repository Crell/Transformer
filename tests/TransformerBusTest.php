<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\TransformerBus;
use Crell\Transformer\TransformerBusInterface;


class TransformerBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a transformer bus to be tested.
     *
     * @param $target
     *   The target class the transformer under test should produce.
     * @return TransformerBusInterface
     *   The transformer bus implementation being tested.
     */
    protected function createTransformerBus($target) {
        $bus = new TransformerBus($target);
        return $bus;
    }

    public function testSimpleMap()
    {
        $transformer = function(TestA $a) {
            return new TestB();
        };

        $bus = $this->createTransformerBus(TestB::CLASSNAME);
        $bus->setTransformer(TestA::CLASSNAME, $transformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestB::CLASSNAME, $result);
    }

    public function testMultistepMap()
    {
        $ATransformer = function(TestA $a) {
            return new TestB();
        };
        $BTransformer = function(TestB $a) {
            return new TestC();
        };

        $bus = $this->createTransformerBus(TestC::CLASSNAME);
        $bus->setTransformer(TestA::CLASSNAME, $ATransformer);
        $bus->setTransformer(TestB::CLASSNAME, $BTransformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestC::CLASSNAME, $result);
    }

    public function testExtendedClassMap()
    {
        $transformer = function(TestA $a) {
            return new TestB2();
        };

        $bus = $this->createTransformerBus(TestB::CLASSNAME);
        $bus->setTransformer(TestA::CLASSNAME, $transformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestB::CLASSNAME, $result);
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
    public function testCallableOptions($from, $to, $transformer, $exception = null)
    {
        if ($exception) {
            $this->setExpectedException($exception);
        }

        $bus = $this->createTransformerBus($to);
        $bus->setTransformer($from, $transformer);

        $result = $bus->transform(new $from());
        $this->assertInstanceOf($to, $result);
    }

    /**
     * Defines an array of transformers that convert from TestA to TestB.
     */
    public function transformerDefinitionProvider()
    {
        $defs = [];

        // Successful tests.
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, function(TestA $a) { return new TestB(); }];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, __NAMESPACE__ . '\function_converter_test'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [MethodConverterTest::CLASSNAME, 'staticTransform']];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [new MethodConverterTest(), 'transform']];

        // Invalid data that should fail.
        $defs[] = [TestA::CLASSNAME, TestC::CLASSNAME, function(TestA $a) { return new TestB(); }, 'Crell\Transformer\NoTransformerFoundException'];

        return $defs;
    }
}

function function_converter_test(TestA $a)
{
    return new TestB();
}

class MethodConverterTest
{
    const CLASSNAME = __CLASS__;

    public static function staticTransform(TestA $a)
    {
        return new TestB();
    }

    public function transform(TestA $a)
    {
        return new TestB();
    }
}
