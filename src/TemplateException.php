<?php

namespace Dashifen\WPTemplates;

use Dashifen\Exception\Exception;

class TemplateException extends Exception
{
    public const FILE_NOT_FOUND = 1;
    public const UNABLE_TO_FIND_FILE = 2;
}
