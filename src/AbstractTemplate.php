<?php

namespace Dashifen\WPTemplates;

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
  protected array $context = [];
  
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
        TemplateException::UNABLE_TO_CONSTRUCT,
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
   */
  public function setFile(?string $file): void
  {
    $this->file = $file;
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
      
      // if we receive null we do nothing.  we have to check this first because
      // accidentally trying to merge a null into existing context would cause
      // an error.
      
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
