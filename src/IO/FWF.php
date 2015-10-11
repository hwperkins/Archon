<?php namespace Archon\IO;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
class FWF {

    private $defaultOptions = [
        'include' => null,
        'exclude' => null
    ];

    public function __construct($fileName) {
        $this->fileName = $fileName;
    }

    public function loadFile(array $colSpecs, array $options = []) {
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

        foreach($fileData as &$line) {
            $line = $this->applyColSpecs($line, $colSpecs);
        }

        $fileData = array_values($fileData);
        return $fileData;
    }

    private function applyColSpecs($data, array $colSpecs) {
        $result = [];
        foreach($colSpecs as $colName => $coords) {
            $result[$colName] = trim(substr($data, $coords[0], $coords[1] - $coords[0]));
        }
        return $result;
    }
}
