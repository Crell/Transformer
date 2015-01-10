<?php

namespace Crell\Transformer;

/**
 * Interface for TransformerBus implementations.
 */
interface TransformerBusInterface
{
    /**
     * Continually transforms a source object until a destination type is reached.
     *
     * @param mixed $source
     *   An object of whatever type that we want converted.
     * @return mixed
     *   An object of the destination type.
     *
     * @throws NoTransformerFoundException
     *   Throws an exception if we ever hit a class for which no transformer
     *   has been specified.
     */
    public function transform($source);
}