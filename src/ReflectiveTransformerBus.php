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
    public function setAutomaticTransformer(callable $transformer): void
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
    protected function deriveReceivedClass(callable $transformer): string
    {
        $r = (new \ReflectionObject(\Closure::fromCallable($transformer)))->getMethod('__invoke');

        $parameters = $r->getParameters();
        if (count($parameters) !== 1) {
            throw new InvalidTransformerException('A transformer must have one parameter and it must be typed to the input class type.');
        }

        // Apparently this is deprecated in 8.0.0 but it's not clear what to replace it with.
        // @todo replace this with something not deprecated if the docs ever get updated to
        // say what that is.
        $class = $parameters[0]->getClass();

        if (!$class) {
            throw new InvalidTransformerException();
        }

        return $class->name;
    }
}
