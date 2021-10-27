<?php

namespace Dashifen\WPTemplates;

use Dashifen\Exception\Exception;

class TemplateException extends Exception
{
  // we expect that the projects which use this object will have their own
  // version of template exceptions and they might want to extend this object
  // when they make theirs.  so that they can begin their constants at one, we
  // put our lonely constant at negative one here.
  
  public const UNABLE_TO_CONSTRUCT = -1;
}
