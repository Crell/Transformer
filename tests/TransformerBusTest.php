<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\TransformerBus;

class A {}
class B {}
class C {}

class TransformerBusTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMap()
    {
        $transformer = function(A $a) {
            return new B();
        };

        $bus = new TransformerBus('Crell\Transformer\Tests\B');
        $bus->setTransformer('Crell\Transformer\Tests\A', $transformer);

        $result = $bus->transform(new A());

        $this->assertInstanceOf('Crell\Transformer\Tests\B', $result);
    }

    public function testMultistepMap()
    {
        $ATransformer = function(A $a) {
            return new B();
        };
        $BTransformer = function(B $a) {
            return new C();
        };

        $bus = new TransformerBus('Crell\Transformer\Tests\C');
        $bus->setTransformer('Crell\Transformer\Tests\A', $ATransformer);
        $bus->setTransformer('Crell\Transformer\Tests\B', $BTransformer);

        $result = $bus->transform(new A());

        $this->assertInstanceOf('Crell\Transformer\Tests\C', $result);
    }

}
