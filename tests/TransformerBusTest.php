<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\TransformerBus;


class TransformerBusTest extends \PHPUnit_Framework_TestCase
{

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

}
