<?php

namespace Dashifen\WPTemplates;

use Throwable;
use SplFileInfo;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Dashifen\Repository\Repository;
use Dashifen\Repository\RepositoryException;

use function Dashifen\WPHandler\Handlers\write_log;

/**
 * Class AbstractTemplate
 *
 * @property-read string $file
 * @property-read array  $context
 *
 * @package Dashifen\WPTemplates
 */
abstract class AbstractTemplate extends Repository implements TemplateInterface
{
    /**
     * @var string
     */
    protected $file;
    
    /**
     * @var array
     */
    protected $context;
    
    /**
     * AbstractTemplate constructor.
     *
     * @param string $file
     * @param array  $context
     *
     * @throws TemplateException
     */
    public function __construct (string $file = "", array $context = [])
    {
        try {
            parent::__construct([
                "file"    => $file,
                "context" => $context,
            ]);
        } catch (RepositoryException $exception) {
            
            // we don't want the scope using this object to have to worry about
            // repository exceptions.  therefore, we switch it for a more
            // appropriate, contextual one.
            
            throw new TemplateException(
                "Unable to construct " . static::class,
                $exception->getCode(),
                $exception
            );
        }
    }
    
    /**
     * render
     *
     * Renders either a previously set template file and context or can use
     * the optional parameters here to specify what a file and context at the
     * time of the call.
     *
     * @param bool        $debug
     * @param string|null $file
     * @param array|null  $context
     *
     * @return string
     */
    abstract public function render (bool $debug = false, ?string $file = null, ?array $context = null): string;
    
    /**
     * setFile
     *
     * Sets the file property.
     *
     * @param string|null $file
     *
     * @return void
     * @throws TemplateException
     */
    public function setFile (?string $file): void
    {
        if ($file === null) {
            
            // if we receive null we do nothing.  this is mostly useful to
            // allow implementations of the render method to pass a null here
            // without consequences.
            
            return;
        }
        
        // $file should be an absolute path to a the template file that we're
        // going to render.  if all we get is a filename without a path, then
        // we'll try to find it.  we can tell if it's a filename because it and
        // its basename will be the same.  notice that we switch the name from
        // $file to $path so that we can still use the original parameter when
        // we need to herein.
        
        $path = $file;
        if ($path === basename($path)) {
            $path = $this->locateFile($path);
            
            if ($path === null) {
                throw new TemplateException(
                    'Unable to find ' . $file,
                    TemplateException::UNABLE_TO_FIND_FILE
                );
            }
        }
        
        if (!is_file($path)) {
            throw new TemplateException(
                'File not found: ' . basename($path),
                TemplateException::FILE_NOT_FOUND
            );
        }
        
        $this->file = $path;
    }
    
    /**
     * locateFile
     *
     * Given a file, looks for it within the stylesheet directory and its
     * sub-directories.  If found, returns the path to it; otherwise returns
     * null.
     *
     * @param string $filename
     *
     * @return string|null
     */
    private function locateFile (string $filename): ?string
    {
        $dir = get_stylesheet_directory();
        $dir_iterator = new RecursiveDirectoryIterator($dir);
        $iterator = new RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */
            
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPath();
            }
        }
        
        return null;
    }
    
    /**
     * setContext
     *
     * Merges the parameter into the context property unless the replace flag
     * is set.  In that case, we simply replace the property with our
     * parameter.
     *
     * @param array|null $context
     * @param bool       $replace
     *
     * @return void
     */
    public function setContext (?array $context, bool $replace = false): void
    {
        if ($context === null) {
            
            // if we receive null we do nothing.  this is mostly useful to
            // allow implementations of the render method to pass a null here
            // without consequences.
            
            return;
        }
        
        $this->context = !$replace
            ? array_merge($this->context, $context)
            : $context;
    }
    
    /**
     * getContextValue
     *
     * Given a space separated series of indices, returns the value matching
     * those indices from within this Template's context.  For example, if you
     * wanted to retrieve $this->context['foo']['bar], you could call this
     * method as follows:  getContextValue('foo bar').
     *
     * @param string $index
     *
     * @return mixed|null
     */
    public function getContextValue (string $index)
    {
        $context = $this->context;
        $indices = array_filter(explode(' ', $index));
        foreach ($indices as $index) {
            
            // here's where the magic happens.  our explode() call will have
            // split our space-separated string into an array, an array over
            // which we are now iterating.  if $context has an index that
            // matches $index, we continue to loop until either (a) we don't
            // have $index or (b) we run out of $indices.
            
            $context = $context[$index] ?? null;
            if ($context === null) {
                return null;
            }
        }
        
        return $context;
    }
    
    /**
     * isDebug
     *
     * Returns true when WP_DEBUG exists and is set.
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }
    
    /**
     * debug
     *
     * Given stuff, print information about it and then die() if the $die flag is
     * set.  Typically, this only works when the isDebug() method returns true,
     * but the $force parameter will override this behavior.
     *
     * @param mixed $stuff
     * @param bool  $die
     * @param bool  $force
     *
     * @return void
     */
    public static function debug($stuff, bool $die = false, bool $force = false): void
    {
        if (self::isDebug() || $force) {
            $message = '<pre>' . print_r($stuff, true) . '</pre>';
            
            if (!$die) {
                echo $message;
                return;
            }
            
            die($message);
        }
    }
    
    /**
     * writeLog
     *
     * Calling this method should write $data to the WordPress debug.log file.
     *
     * @param mixed $data
     *
     * @return void
     */
    public static function writeLog($data): void
    {
        // source:  https://www.elegantthemes.com/blog/tips-tricks/using-the-wordpress-debug-log
        // accessed:  2018-07-09
        
        if (!function_exists('write_log')) {
            function write_log($log)
            {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }
        
        write_log($data);
    }
    
    /**
     * catcher
     *
     * This serves as a general-purpose Exception handler which displays
     * the caught object when we're debugging and writes it to the log when
     * we're not.
     *
     * @param Throwable $thrown
     *
     * @return void
     */
    public static function catcher(Throwable $thrown): void
    {
        self::isDebug() ? self::debug($thrown, true) : self::writeLog($thrown);
    }
}
