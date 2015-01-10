<?php

namespace Crell\Transformer;

/**
 * Default implementation of a Transformation bus.
 */
class TransformerBus implements TransformerBusInterface
{
    /**
     * We want an object of this class at the end of the day.
     *
     * @var string
     */
    protected $targetClass;

    /**
     * An associative array of callable transformers.
     *
     * The key is the class name the transformer will handle, and the value
     * is a PHP callable that can convert objects of that class to something
     * else.
     *
     * @var array
     */
    protected $transformers = [];

    /**
     * Constructs a new TransformerBus.
     *
     * @param string $targetClass
     *   The name of the class we are after.
     */
    public function __construct($targetClass)
    {
        $this->targetClass = $targetClass;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($source)
    {
        $current = $source;
        $current_class = get_class($current);

        while ($current_class != $this->targetClass) {
            if (empty($this->transformers[$current_class])) {
                throw new NoTransformerFoundException(sprintf("No transformer registered for class '%s'", $current_class));
            }
            $current = $this->transformers[$current_class]($current);
            $current_class = get_class($current);
        }

        return $current;
    }

    public function setTransformer($class, callable $transformer)
    {
        $this->transformers[$class] = $transformer;
    }
}
