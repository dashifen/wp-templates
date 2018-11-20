<?php

namespace Dashifen\WPTemplates\Templates;

use Dashifen\WPTemplates\PostException;
use Timber\Timber;

abstract class AbstractTimberTemplate extends AbstractTemplate {
	public function __construct(bool $getTimberContext = false) {
		parent::__construct();

		$timberContext = $getTimberContext
			? Timber::get_context()
			: [];

		$this->setContext($timberContext);
	}

	/**
	 * show
	 *
	 * Given a template, displays it using the $context property for this
	 * page.  If $debug is set, then it also prints the $context property in
	 * a large comment at the top of the page.
	 *
	 * @param string $template
	 * @param bool   $debug
	 *
	 * @return void
	 * @throws PostException
	 */
	public function show(string $template, bool $debug = false): void {
		if ($debug) {
			echo "<!-- " . print_r($this->context, true) . " -->";
		}

		if (empty($template)) {
			throw new PostException("Cannot render without template",
				PostException::CANNOT_RENDER_TEMPLATE);
		}

		Timber::render($template, $this->context);
	}
}