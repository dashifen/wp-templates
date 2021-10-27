<?php

namespace Dashifen\WPTemplates;

use Throwable;

interface TemplateInterface
{
  /**
   * render
   *
   * Renders either a previously set template file and context or can use the
   * optional parameters here to specify the a file and context at the time
   * of the call and displays it on screen.
   *
   * @param bool        $debug
   * @param string|null $file
   * @param array|null  $context
   *
   * @return void
   */
  public function render(bool $debug = false, ?string $file = null, ?array $context = null): void;
  
  /**
   * compile
   *
   * Compiles either a previously set template file and context or can use
   * the optional parameters here to specify the file and context at the time
   * of the call and returns it to the calling scope.
   *
   *
   * @param bool        $debug
   * @param string|null $file
   * @param array|null  $context
   *
   * @return string
   */
  public function compile(bool $debug = false, ?string $file = null, ?array $context = null): string;
  
  /**
   * setContext
   *
   * Typically, a setter isn't a part of our interface, but this one has an
   * additional parameter:  the replace flag.  When set, the array parameter
   * replaces the current value of our context property.  Otherwise, the
   * parameter is merged into it.
   *
   * @param array|null $context
   * @param bool       $replace
   *
   * @return mixed
   */
  public function setContext(?array $context, bool $replace = false): void;
  
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
   * @return mixed
   */
  public function getContextValue(string $index);
  
  /**
   * isDebug
   *
   * Returns true when WP_DEBUG exists and is set.
   *
   * @return bool
   */
  public static function isDebug(): bool;
  
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
  public static function debug($stuff, bool $die = false, bool $force = false): void;
  
  /**
   * writeLog
   *
   * Calling this method should write $data to the WordPress debug.log file.
   *
   * @param mixed $data
   *
   * @return void
   */
  public static function writeLog($data): void;
  
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
  public static function catcher(Throwable $thrown): void;
}
