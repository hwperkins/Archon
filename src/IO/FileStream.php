<?php

namespace Archon\IO;

use Closure;
use SplFileInfo;

class FileStream
{

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function apply(Closure ...$callbacks)
    {
        foreach ($this->generator() as $i => $el) {
            foreach ($callbacks as $callback) {
                $el = $callback($el, $i);
            }
            yield $el;
        }
    }

    public function generator()
    {
        $handle = fopen($this->file, 'r');

        while (!feof($handle)) {
            yield fgets($handle);
        }

        fclose($handle);
    }

}