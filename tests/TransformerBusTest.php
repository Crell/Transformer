<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\TransformerBus;
use Crell\Transformer\TransformerBusInterface;


class TransformerBusTest extends \PHPUnit_Framework_TestCase
{

    protected $classToTest = 'Crell\Transformer\TransformerBus';

    public function testSimpleMap()
    {
        $transformer = function(TestA $a) {
            return new TestB();
        };

        $bus = new TransformerBus(TestB::CLASSNAME);
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

        $bus = new TransformerBus(TestC::CLASSNAME);
        $bus->setTransformer(TestA::CLASSNAME, $ATransformer);
        $bus->setTransformer(TestB::CLASSNAME, $BTransformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestC::CLASSNAME, $result);
    }

    /**
     * @param string $from
     *
     * @param string $to
     * @param mixed $transformer
     *   A transformer definition (aka callable definition).
     *
     * @dataProvider transformerDefinitionProvider
     */
    public function testCallableOptions($from, $to, $transformer)
    {
        $classToTest = $this->classToTest;

        /** @var TransformerBusInterface $bus */
        $bus = new $classToTest($to);
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

        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, function(TestA $a) { return new TestB(); }];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, __NAMESPACE__ . '\function_converter_test'];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [MethodConverterTest::CLASSNAME, 'staticTransform']];
        $defs[] = [TestA::CLASSNAME, TestB::CLASSNAME, [new MethodConverterTest(), 'transform']];

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
