<?php
/**
 * Created by PhpStorm.
 * User: crell
 * Date: 1/10/15
 * Time: 12:11 AM
 */

namespace Crell\TransformerBundle\Tests;

use Crell\Transformer\TransformerBus;

class A {}
class B {}
class C {}

class Transformer
{
    public function transform($source) {}
}

class TransformerBusTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleMap()
    {
        $transformer = function(A $a) {
            return new B();
        };

        $bus = new TransformerBus('Crell\TransformerBundle\Tests\B');
        $bus->setTransformer('Crell\TransformerBundle\Tests\A', $transformer);

        $result = $bus->transform(new A());

        $this->assertInstanceOf('Crell\TransformerBundle\Tests\B', $result);
    }

    public function testMultistepMap()
    {
        $ATransformer = function(A $a) {
            return new B();
        };
        $BTransformer = function(B $a) {
            return new C();
        };

        $bus = new TransformerBus('Crell\TransformerBundle\Tests\C');
        $bus->setTransformer('Crell\TransformerBundle\Tests\A', $ATransformer);
        $bus->setTransformer('Crell\TransformerBundle\Tests\B', $BTransformer);

        $result = $bus->transform(new A());

        $this->assertInstanceOf('Crell\TransformerBundle\Tests\C', $result);
    }

}
