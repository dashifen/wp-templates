<?php

namespace Dashifen\WPTemplates;

use Dashifen\Exception\Exception;

class PostException extends Exception {
	const CANNOT_RENDER_TEMPLATE = 1;
	const TEMPLATE_LOCATION_NOT_FOUND = 2;
}