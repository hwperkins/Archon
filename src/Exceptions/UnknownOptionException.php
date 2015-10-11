<?php

/**
 * Contains the UnknownOptionException.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */

namespace Archon\Exceptions;

/**
 * Exception thrown when an options array is passed to a function and contains an optional element which that
 * function does not know how to process.
 * The purpose of this is to prevent a situation where the user may be passing an option that he or she believes has an
 * effect on their data, when in actuality it is doing nothing.
 * @package   Archon\Exceptions
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */
class UnknownOptionException extends DataFrameException
{

}
