<?php

namespace Frgmnt\View;

/**
 * Implements view rendering using the Latte templating engine.
 *
 * Extends the AbstractViewRenderer to provide concrete methods for rendering
 * various views such as list, start, detail, login, and register.
 *
 * @package View
 */
class LatteViewRenderer extends AbstractViewRenderer
{
    /**
     * Render a Latte template and return the output as string.
     *
     * @param string $template Path relative to the templates directory, including subfolder and extension, e.g. 'pages/login.latte'.
     * @param array  $params   Variables to pass into the template.
     * @return string          Rendered HTML.
     */
    public function render(string $template, array $params = []): string
    {
        return $this->latte->renderToString($template, $params);
    }
}
