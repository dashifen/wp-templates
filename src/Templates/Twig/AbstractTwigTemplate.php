<?php

namespace Dashifen\WPTemplates\Templates\Twig;

use Dashifen\WPTemplates\Templates\AbstractTemplate;
use Dashifen\WPTemplates\PostException;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Error;

abstract class AbstractTwigTemplate extends AbstractTemplate implements TwigTemplateInterface {
	/**
	 * @var string
	 */
	protected $templatePath;

	/**
	 * @var Twig_Environment
	 */
	protected $environment;

	/**
	 * AbstractTimberPage constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->setContext();
	}

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
	public function setTemplateLocation(string $pathToTemplates) {
		if (!is_dir($pathToTemplates)) {
			throw new PostException("Templates not found: $pathToTemplates.",
				PostException::TEMPLATE_LOCATION_NOT_FOUND);
		}

		$this->templatePath = $pathToTemplates;
	}

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
	public function setEnvironment(Twig_Environment $environment = null) {
		if (!is_null($environment)) {

			// if we were sent an environment, we use it.

			$this->environment = $environment;
		} else {

			// otherwise, we construct an environment from the template path.
			// which, if we don't have that either, then we'll only be able to
			// throw a tantrum.

			if (empty($this->templatePath)) {
				throw new PostException("Template path empty.",
					PostException::TEMPLATE_LOCATION_NOT_FOUND);
			}

			$loader = new Twig_Loader_Filesystem($this->templatePath);
			$this->environment = new Twig_Environment($loader);
		}
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
	public function show(string $template, bool $debug = false) {
		if ($debug) {
			echo "<!-- " . print_r($this->context, true) . " -->";
		}

		if (empty($template)) {
			throw new PostException("Cannot render without template",
				PostException::CANNOT_RENDER_TEMPLATE);
		}

		if (is_null($this->environment)) {
			$this->setEnvironment();
		}

		try {
			echo $this->environment->render($template, $this->context);
		} catch (Twig_Error $e) {
			throw new PostException("Unable to render template $template.",
				PostException::CANNOT_RENDER_TEMPLATE);
		}
	}
}