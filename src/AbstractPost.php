<?php

namespace Dashifen\WPTemplates;

abstract class AbstractPost implements PostInterface {
	/**
	 * @var int
	 */
	protected $postId = 0;

	public function __construct() {
		$this->postId = is_singular() ? get_the_ID() : 0;
	}

	/**
	 * show
	 *
	 * Given a template, this method displays it.
	 *
	 * @param string $template
	 * @param bool   $debug
	 *
	 * @return void
	 */
	abstract public function show(string $template, bool $debug = false);
}