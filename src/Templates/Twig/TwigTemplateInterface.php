<?php

namespace Dashifen\WPTemplates\Templates\Twig;

use Dashifen\WPTemplates\PostException;
use Twig_Environment;

interface TwigTemplateInterface {
	/**
	 * setTemplateLocation
	 *
	 * Given a path to a directory, stores it as the filesystem location
	 * where we can find our templates.
	 *
	 * @param string $pathToTemplates
	 *
	 * @return void
	 * @throws PostException
	 */
	public function setTemplateLocation(string $pathToTemplates);

	/**
	 * setEnvironment
	 *
	 * Given a Twig_Environment, use it as the renderer for our templates.
	 *
	 * @param Twig_Environment|null $environment
	 *
	 * @return void
	 * @throws PostException
	 */
	public function setEnvironment(Twig_Environment $environment = null);
}