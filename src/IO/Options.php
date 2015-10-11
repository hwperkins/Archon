<?php

/**
 * Contains the Options class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */

namespace Archon\IO;

use Archon\Exceptions\UnknownOptionException;

/**
 * Options class provides basic common handling for array options which are passed to various data format
 * implementations in the Archon/IO package.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 */
class Options
{


    /**
     * Will apply all default options to an associative array of user-provided options.
     * @param array $userOptions    User-provided options.
     * @param array $defaultOptions Class-provided options.
     * @return array
     * @throws UnknownOptionException Exception when an option is unknown.
     */
    public static function setDefaultOptions(array $userOptions, array $defaultOptions)
    {
        /*
            * First, override all default options with whatever ones have been
            * user-specified.
        */

        foreach ($userOptions as $optionName => $optionValue) {
            // Check if user provided any invalid options.
            if (array_key_exists($optionName, $defaultOptions) === false) {
                throw new UnknownOptionException('Unknown option: '.$optionName);
            }

            // Otherwise override the default value for that option.
            $defaultOptions[$optionName] = $optionValue;
        }

        /*
         * Then once the default options have been overridden, populate the
         * user-provided options array with them and pass it back to be used.
         */
        foreach ($defaultOptions as $optionName => $optionValue) {
            /* This will add all our default option values to the user provided
             * array.
             */
            if (array_key_exists($optionName, $userOptions) === false) {
                $userOptions[$optionName] = $optionValue;
            }
        }

        return $userOptions;
    }
}
