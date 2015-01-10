<?php

namespace Crell\Transformer\Tests;

use Crell\Transformer\ReflectiveTransformerBus;
use Crell\Transformer\TransformerBus;

class D {}
class E {}
class F {}

class ReflectiveTransformerBusTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMap()
    {
        $transformer = function(D $a) {
            return new E();
        };

        $bus = new ReflectiveTransformerBus('Crell\Transformer\Tests\E');
        $bus->setAutomaticTransformer($transformer);

        $result = $bus->transform(new D());

        $this->assertInstanceOf('Crell\Transformer\Tests\E', $result);
    }

    public function testMultistepMap()
    {
        $DTransformer = function(D $a) {
            return new E();
        };
        $ETransformer = function(E $a) {
            return new F();
        };

        $bus = new ReflectiveTransformerBus('Crell\Transformer\Tests\F');
        $bus->setAutomaticTransformer($DTransformer);
        $bus->setAutomaticTransformer($ETransformer);

        $result = $bus->transform(new D());

        $this->assertInstanceOf('Crell\Transformer\Tests\F', $result);
    }

}
