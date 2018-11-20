<?php

namespace Dashifen\WPTemplates\Templates;

interface TemplateInterface {
	/**
	 * getContext
	 *
	 * Returns the context property of this object.
	 *
	 * @return array
	 */
	public function getContext(): array;

	/**
	 * setContext
	 *
	 * Sets the context property filling it with data pertinent to the
	 * template being displays.  Typically, all the work to do so exists
	 * within this method, but data can be passed to it which will be
	 * merged with the work performed herein.
	 *
	 * @param array $context
	 *
	 * @return void
	 */
	public function setContext(array $context = []): void;

	/**
	 * getContextValue
	 *
	 * Uses the $index parameter to drill down into the context property
	 * and returns a specific value within it.  $index should be a space
	 * separated "path" to the index we need.  so, if we wanted to return
	 * $this->context["foo"]["bar"], $index should be "foo bar."
	 *
	 * @param string $index
	 *
	 * @return mixed|null
	 */
	public function getContextValue(string $index);
}