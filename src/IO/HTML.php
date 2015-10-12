<?php

/**
 * Contains the HTML class.
 * @package   DataFrame
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */

namespace Archon\IO;

use Archon\Exceptions\NotYetImplementedException;
use Gajus\Dindent\Indenter;

/**
 * The HTML class contains implementation details for transforming two-dimensional arrays into HTML tables.
 * @package   Archon\IO
 * @author    Howard Gehring <hwgehring@gmail.com>
 * @copyright 2015 Howard Gehring <hwgehring@gmail.com>
 * @license   https://github.com/HWGehring/Archon/blob/master/LICENSE BSD-3-Clause
 * @link      https://github.com/HWGehring/Archon
 * @since     0.1.0
 */
final class HTML
{

    private $defaultOptions = [
        'pretty' => false
    ];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Assembles a two-dimensional array as an HTML table, where row element keys are header/footer columns,
     * and row element values form the individual cells of the table.
     * @param  array $options
     * @return array
     * @throws NotYetImplementedException
     * @throws \Archon\Exceptions\UnknownOptionException
     * @since  0.1.0
     */
    public function assembleTable(array $options)
    {
        $data = $this->data;
        $options = Options::setDefaultOptions($options, $this->defaultOptions);
        $prettyOpt = $options['pretty'];

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

        if ($prettyOpt === true) {
            $indenter = new Indenter();
            $data = $indenter->indent($data);
        }

        return $data;
    }

    /**
     * Returns a function which implodes and wraps an array around the specified HTML tags.
     * @param  $leftTag
     * @param  $implodeTag
     * @param  $rightTag
     * @return \Closure
     * @since  0.1.0
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
     * @param  $leftTag
     * @param  $rightTag
     * @return \Closure
     * @since  0.1.0
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
