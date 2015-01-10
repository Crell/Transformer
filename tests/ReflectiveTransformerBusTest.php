<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\ReflectiveTransformerBus;

class ReflectiveTransformerBusTest extends TransformerBusTest
{

    public function testSimpleAutomaticMap()
    {
        $transformer = function(TestA $a) {
            return new TestB();
        };

        $bus = new ReflectiveTransformerBus(TestB::CLASSNAME);
        $bus->setAutomaticTransformer($transformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestB::CLASSNAME, $result);
    }

    public function testMultistepAutomaticMap()
    {
        $ATransformer = function(TestA $a) {
            return new TestB();
        };
        $BTransformer = function(TestB $a) {
            return new TestC();
        };

        $bus = new ReflectiveTransformerBus(TestC::CLASSNAME);
        $bus->setAutomaticTransformer($ATransformer);
        $bus->setAutomaticTransformer($BTransformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf(TestC::CLASSNAME, $result);
    }

}
