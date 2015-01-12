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
 * Exception thrown when a transformer is registered that does not have a single typed argument.
 */
class InvalidTransformerException extends \InvalidArgumentException {

}
