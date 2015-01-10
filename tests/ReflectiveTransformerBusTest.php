<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\ReflectiveTransformerBus;

class ReflectiveTransformerBusTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMap()
    {
        $transformer = function(TestA $a) {
            return new TestB();
        };

        $bus = new ReflectiveTransformerBus('Crell\Transformer\Tests\TestB');
        $bus->setAutomaticTransformer($transformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf('Crell\Transformer\Tests\TestB', $result);
    }

    public function testMultistepMap()
    {
        $ATransformer = function(TestA $a) {
            return new TestB();
        };
        $BTransformer = function(TestB $a) {
            return new TestC();
        };

        $bus = new ReflectiveTransformerBus('Crell\Transformer\Tests\TestC');
        $bus->setAutomaticTransformer($ATransformer);
        $bus->setAutomaticTransformer($BTransformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf('Crell\Transformer\Tests\TestC', $result);
    }

}
