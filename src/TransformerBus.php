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

        while (! $current instanceof $this->targetClass) {
            $transformer = $this->getTransformer($current_class);
            $current = $transformer($current);
            $current_class = get_class($current);
        }

        return $current;
    }

    /**
     * Sets the transformer for a specified type.
     *
     * @param string $class
     *   The class this transformer can handle.
     * @param callable $transformer
     *   A callable that will transform an object of type $class to something else.
     */
    public function setTransformer($class, callable $transformer)
    {
        $this->transformers[$class] = $transformer;
    }

    /**
     * Gets the transformer callable for a given class.
     *
     * @param string $class
     *   The class we want to transform.
     * @return callable|null
     *   Returns the corresponding transformer, or null if there isn't one.
     *
     * @throws NoTransformerFoundException
     *   Throws an exception if we ever hit a class for which no transformer
     *   has been specified.
     */
    protected function getTransformer($class)
    {
        if (!isset($this->transformers[$class])) {
            throw new NoTransformerFoundException(sprintf("No transformer registered for class '%s'", $class));
        }
        return $this->transformers[$class];
    }
}
