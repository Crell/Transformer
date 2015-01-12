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

namespace Crell\Transformer;

/**
 * Transformation Bus that can derive the receiving type of a transformer on the fly.
 */
class ReflectiveTransformerBus extends TransformerBus
{
    /**
     * Sets a transformer, deriving its received type with reflection.
     *
     * @param callable $transformer
     *   The callable to register.
     */
    public function setAutomaticTransformer(callable $transformer)
    {
        $this->setTransformer($this->deriveReceivedClass($transformer), $transformer);
    }

    /**
     * Derives the receivable class type for the provided transformer.
     *
     * That is, determine the type hint of the one and only parameter.
     *
     * @param callable $transformer
     *   The transformer to examine.
     * @return string
     *   The class name the provided transformer can receive.
     *
     * @throws InvalidTransformerException
     *   Thrown if the provided transformer has the wrong number of parameters
     *   or its parameter is not type hinted.
     */
    protected function deriveReceivedClass(callable $transformer)
    {
        if (is_array($transformer)) {
            $r = new \ReflectionMethod($transformer[0], $transformer[1]);
        } elseif (is_object($transformer) && !$transformer instanceof \Closure) {
            $r = new \ReflectionObject($transformer);
            $r = $r->getMethod('__invoke');
        } else {
            $r = new \ReflectionFunction($transformer);
        }

        $parameters = $r->getParameters();
        if (count($parameters) != 1) {
            throw new InvalidTransformerException('A transformer must have one parameter and it must be typed to the input class type.');
        }

        $class = $parameters[0]->getClass();

        if (!$class) {
            throw new InvalidTransformerException();
        }

        return $class->name;
    }
}
