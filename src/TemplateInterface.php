<?php

namespace Dashifen\WPTemplates;

interface TemplateInterface
{
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
     * @return mixed
     */
    public function render(bool $debug = false, ?string $file = null, ?array $context = null);
    
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
}
