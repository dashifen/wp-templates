<?php

namespace Dashifen\WPTemplates;

use SplFileInfo;
use RecursiveIterator;
use RecursiveFilterIterator;

class TemplateFileIterator extends RecursiveFilterIterator
{
    /**
     * @var string
     */
    protected $extension;
    
    /**
     * TemplateFileIterator constructor
     *
     * @link  https://php.net/manual/en/recursivefilteriterator.construct.php
     *
     * @param RecursiveIterator $iterator
     * @param string            $extension
     *
     * @since 5.1
     */
    public function __construct (RecursiveIterator $iterator, string $extension)
    {
        parent::__construct($iterator);
        $this->extension = $extension;
    }
    
    /**
     * accept
     *
     * Check whether the current element of the iterator is acceptable
     *
     * @link  https://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     * @since 5.1
     */
    public function accept (): bool
    {
        /** @var SplFileInfo $current */
        
        $current = parent::current();
        $filename = $current->getFilename();
        
        return $current->isDir()
            ? $this->isAcceptableDirectory($filename)
            : $this->isAcceptableFile($filename);
    }
    
    /**
     * isAcceptableDirectory
     *
     * Returns true if this is an acceptable directory in which to look for
     * templates; false otherwise.
     *
     * @param string $directory
     *
     * @return bool
     */
    private function isAcceptableDirectory (string $directory): bool
    {
        return $directory !== 'vendor' && $directory !== 'node_modules';
    }
    
    /**
     * isAcceptableFile
     *
     * Returns true if this is an acceptable file; false otherwise.
     *
     * @param string $filename
     *
     * @return bool
     */
    private function isAcceptableFile (string $filename): bool
    {
        return pathinfo($filename, PATHINFO_EXTENSION) === $this->extension;
    }
}
