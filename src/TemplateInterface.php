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
     * @return string
     */
    public function render (bool $debug = false, ?string $file = null, ?array $context = null): string;
    
    /**
     * setContext
     *
     * Typically, a setter isn't a part of our interface, but this one has an
     * additional parameter:  the render flag.  When set, the array parameter
     * replaces the current value of our context property.  Otherwise, the
     * parameter is merged into it.
     *
     * @param array|null $context
     * @param bool       $replace
     *
     * @return mixed
     */
    public function setContext (?array $context, bool $replace = false): void;
    
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
    public function getContextValue (string $index);
}
