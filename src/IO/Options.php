<?php namespace Archon\IO;

use Archon\Exceptions\UnknownOptionException;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
class Options
{

    public static function setDefaultOptions(array $userProvidedOptions, array $defaultOptions)
    {
        /*
         * First, override all default options with whatever ones have been user-specified.
         */
        foreach ($userProvidedOptions as $optionName => $optionValue) {
            // Check if user provided any invalid options
            if (array_key_exists($optionName, $defaultOptions) === false) {
                throw new UnknownOptionException('Unknown option: ' . $optionName);
            }

            // Otherwise override the default value for that option.
            $defaultOptions[$optionName] = $optionValue;
        }

        /*
         * Then once the default options have been overridden, populate the
         * user-provided options array with them and pass it back to be used.
         */
        foreach ($defaultOptions as $optionName => $optionValue) {
            // This will add all our default option values to the user provided array.
            if (array_key_exists($optionName, $userProvidedOptions) === false) {
                $userProvidedOptions[$optionName] = $optionValue;
            }
        }

        return $userProvidedOptions;
    }
}
