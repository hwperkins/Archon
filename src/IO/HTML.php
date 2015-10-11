<?php namespace Archon\IO;

use Archon\Exceptions\NotYetImplementedException;

/**
 * @link https://github.com/HWGehring/Archon for the canonical source repository
 * @license https://github.com/HWGehring/Archon/blob/master/LICENSE BSD 3-Clause
 */
final class HTML
{

    private $defaultOptions = [
        'readable' => false
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function render(array $options)
    {
        $data = $this->data;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $readableOpt = $options['readable'];

        if ($readableOpt === true) {
            throw new NotYetImplementedException('Pretty HTML not yet implemented');
        }

        $columns = current($data);
        $columns = array_keys($columns);

        $fnTable = $this->fnWrapText('<table>', '</table>');
        $fnTHead = $this->fnWrapText('<thead>', '</thead>');
        $fnTFoot = $this->fnWrapText('<tfoot>', '</tfoot>');
        $fnTBody = $this->fnWrapText('<tbody>', '</tbody>');

        $fnTRTH = $this->fnWrapArray('<tr><th>', '</th><th>', '</th></tr>');

        $columns = $fnTRTH($columns);

        foreach ($data as &$row) {
            $row = $fnTRTH($row);
        }

        $data = $fnTable(
            $fnTHead($columns).
            $fnTFoot($columns).
            $fnTBody($data)
        );

        return $data;
    }

    /**
     * Returns a function which implodes and wraps an array around the specified HTML tags.
     * @param $leftTag
     * @param $implodeTag
     * @param $rightTag
     * @return \Closure
     */
    private function fnWrapArray($leftTag, $implodeTag, $rightTag)
    {
        return function (array $data) use ($leftTag, $implodeTag, $rightTag) {
            $wrap = $this->fnWrapText($leftTag, $rightTag);
            return $wrap(implode($implodeTag, $data));
        };
    }

    /**
     * Returns a function which wraps a string or an array around the specified HTML tags.
     * @param $leftTag
     * @param $rightTag
     * @return \Closure
     */
    private function fnWrapText($leftTag, $rightTag)
    {
        return function ($data) use ($leftTag, $rightTag) {
            if (is_array($data) === true) {
                $data = implode('', $data);
            }

            return $leftTag.$data.$rightTag;
        };
    }
}
