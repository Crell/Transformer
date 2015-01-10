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

        $bus = new TransformerBus('Crell\Transformer\Tests\TestB');
        $bus->setTransformer('Crell\Transformer\Tests\TestA', $transformer);

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

        $bus = new TransformerBus('Crell\Transformer\Tests\TestC');
        $bus->setTransformer('Crell\Transformer\Tests\TestA', $ATransformer);
        $bus->setTransformer('Crell\Transformer\Tests\TestB', $BTransformer);

        $result = $bus->transform(new TestA());

        $this->assertInstanceOf('Crell\Transformer\Tests\TestC', $result);
    }

}
