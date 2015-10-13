<?php

/**
 * Contains the FWF class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */

namespace Archon\IO;

/**
 * The FWF class contains implementation details for reading and writing files in the fixed-width format.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */
final class FWF
{

    private $defaultOptions = [
        'include' => null,
        'exclude' => null
    ];

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Loads the file which the FWF class was instantiated with.
     * Options include:
     *      include: Whitelist regex to apply to each line of the file (default: null)
     *      exclude: Blacklist regex to apply to each line of the file (default: null)
     * @param  array $colSpecs Associative array mapping column names to start-end column positions.
     * @param  array $options
     * @return array
     * @throws \Archon\Exceptions\UnknownOptionException
     * @since  0.1.0
     */
    public function loadFile(array $colSpecs, array $options = [])
    {
        $fileName = $this->fileName;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);

        $includeRegexOpt = $options['include'];
        $excludeRegexOpt = $options['exclude'];

        $fileData = file_get_contents($fileName);
        $fileData = trim($fileData);
        $fileData = str_replace("\r", '', $fileData);
        $fileData = explode("\n", $fileData);
        $fileData = array_map('rtrim', $fileData);

        $fileData = $includeRegexOpt ? preg_grep($includeRegexOpt, $fileData) : $fileData;
        $fileData = $excludeRegexOpt ? preg_grep($excludeRegexOpt, $fileData, PREG_GREP_INVERT) : $fileData;

        foreach ($fileData as &$line) {
            $line = $this->applyColSpecs($line, $colSpecs);
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    /**
     * Parses a string of data based on the rules defined in user provided colspecs.
     * @param  $data
     * @param  array $colSpecs
     * @return array
     * @since  0.1.0
     */
    private function applyColSpecs($data, array $colSpecs)
    {
        $result = [];
        foreach ($colSpecs as $colName => $coords) {
            $result[$colName] = trim(substr($data, $coords[0], $coords[1] - $coords[0]));
        }
        return $result;
    }
}
