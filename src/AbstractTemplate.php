<?php

namespace Dashifen\WPTemplates;

use SplFileInfo;
use Dashifen\Repository\Repository;
use Dashifen\WPDebugging\WPDebuggingTrait;
use Dashifen\Repository\RepositoryException;

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
  use WPDebuggingTrait;
  
  protected ?string $file = null;
  protected ?array $context = null;
  
  /**
   * AbstractTemplate constructor.
   *
   * @param string|null $file
   * @param array|null  $context
   *
   * @throws TemplateException
   */
  public function __construct(?string $file = null, ?array $context = null)
  {
    try {
      parent::__construct(["file" => $file, "context" => $context]);
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
   * @return void
   */
  public function render(bool $debug = false, ?string $file = null, ?array $context = null): void
  {
    echo $this->compile($debug, $file, $context);
  }
  
  /**
   * compile
   *
   * Compiles either a previously set template file and context or can use
   * the optional parameters here to specify the file and context at the time
   * of the call and returns it to the calling scope.     *
   *
   * @param bool        $debug
   * @param string|null $file
   * @param array|null  $context
   *
   * @return string
   */
  abstract public function compile(bool $debug = false, ?string $file = null, ?array $context = null): string;
  
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
  public function setFile(?string $file): void
  {
    if ($file === null) {
      
      // if we receive null we do nothing.  this is mostly useful to
      // allow implementations of the render method to pass a null here
      // without consequences.
      
      return;
    }
    
    // if we received a file, then we want to confirm that it exists
    // somewhere in our theme directory.  to do that, we get our stylesheet
    // directory and the extension of the file.  then, the glob function
    // can get us a list of those files in that directory so we can check
    // for it.
    
    $directory = get_stylesheet_directory();
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $filePaths = $this->getThemeFilesOfType($directory, $extension);
    
    // now we have an array of absolute filenames for the files in our
    // stylesheet directory that have the specified extension.  if we can
    // find the requested one in that list, we're good to go.
    
    $fileLength = strlen($file);
    foreach ($filePaths as $filepath) {
      if (substr($filepath, -$fileLength) === $file) {
        
        // the if condition makes sure that the last X characters,
        // where X is the string length of our $file parameter, matches
        // the $file parameter itself.  if that's the case, then we've
        // found our template.  we'll set the property and return to
        // avoid throwing the exception below.
        
        $this->file = $file;
        return;
      }
    }
    
    throw new TemplateException(
      'File not found: ' . $file,
      TemplateException::FILE_NOT_FOUND
    );
  }
  
  /**
   * getThemeFilesOfType
   *
   * Given a directory and file extension, return all files of that type in
   * the directory and it's subdirectories.
   *
   * @param string $directory
   * @param string $extension
   *
   * @return SplFileInfo[]
   */
  protected function getThemeFilesOfType(string $directory, string $extension): array
  {
    return $this->rGlob($directory . '/*/*.' . $extension);
  }
  
  /**
   * rGlob
   *
   * Recursively calls the glob function to look for files that match the
   * pattern within this folder and its subdirectories.
   *
   * @link https://stackoverflow.com/a/17161106/360838 (accessed: 2019-12-18)
   *
   * @param string $pattern
   *
   * @return array
   */
  private function rGlob(string $pattern): array
  {
    $files = glob($pattern);
    foreach (glob(dirname($pattern) . '/*', GLOB_NOSORT | GLOB_ONLYDIR) as $subdirectory) {
      if ($this->isAppropriateDirectory($subdirectory)) {
        $subdirectoryPattern = $subdirectory . '/' . basename($pattern);
        $subdirectoryFiles = $this->rGlob($subdirectoryPattern);
        $files = array_merge($files, $subdirectoryFiles);
      }
    }
    
    return $files;
  }
  
  /**
   * isAppropriateDirectory
   *
   * Returns true if our parameter is neither the vendor nor the node_modules
   * folder and if it's not within them; otherwise, false.
   *
   * @param string $directory
   *
   * @return bool
   */
  private function isAppropriateDirectory(string $directory): bool
  {
    return strpos($directory, '/node_modules/') === false
      && strpos($directory, '/vendor/') === false;
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
  public function setContext(?array $context, bool $replace = false): void
  {
    if ($context === null) {
      
      // if we receive null we do nothing.  this is mostly useful to
      // allow implementations of the render method to pass a null here
      // without consequences.
      
      return;
    }
    
    $this->context = !$replace
      ? array_merge_recursive($this->context, $context)
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
  public function getContextValue(string $index)
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
}
