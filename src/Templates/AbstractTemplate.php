<?php

namespace Dashifen\WPTemplates\Templates;

use Dashifen\WPTemplates\AbstractPost;

abstract class AbstractTemplate extends AbstractPost implements TemplateInterface {
	/**
	 * @var array
	 */
	protected $context = [];

	public function __construct(array $context = []) {
		$this->setContext($context);
		parent::__construct();
	}

	/**
	 * getContext
	 *
	 * Returns the context property of this object.
	 *
	 * @return array
	 */
	public function getContext(): array {
		return $this->context;
	}

	/**
	 * setContext
	 *
	 * Sets the context property filling it with data pertinent to the
	 * template being displays.  Typically, all the work to do so exists
	 * within this method, but data can be passed to it which should be
	 * merged with the work performed herein.
	 *
	 * @param array $context
	 *
	 * @return void
	 */
	abstract public function setContext(array $context = []): void;

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
	public function getContextValue(string $index) {

		// to drill down into our context property, we start from the
		// assumption that we're returning the whole thing.  then, we
		// explode the $index we're given on spaces and filter out any
		// blanks.  these $indices we use in a loop to dive into our
		// context to find the value that was requested.

		$retValue = $this->context;
		$indices = array_filter(explode(" ", $index));
		foreach ($indices as $index) {

			// this is where we drill down.  we assume each $index can be
			// found in our $retValue.  each iteration then "moves" us
			// through the dimensions of our context property.  if we ever
			// find an $index that is not available, we return null to tell
			// the calling scope that it messed up its request.

			$retValue = $retValue[$index] ?? null;

			if (is_null($retValue)) {
				return null;
			}
		}

		return $retValue;
	}
}