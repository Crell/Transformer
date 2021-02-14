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
 * Interface for TransformerBus implementations.
 */
interface TransformerBusInterface
{
    /**
     * Continually transforms a source object until a destination type is reached.
     *
     * @param object $source
     *   An object of whatever type that we want converted.
     * @return object
     *   An object of the destination type.
     *
     * @throws NoTransformerFoundException
     *   Throws an exception if we ever hit a class for which no transformer
     *   has been specified.
     */
    public function transform(object $source): object;
}
