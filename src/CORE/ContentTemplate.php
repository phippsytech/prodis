<?php

namespace NDISmate\CORE;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class ContentTemplate
{
    var $twig;

    function __construct()
    {
        $loader = new FilesystemLoader(BASE_PATH . '/templates');
        $this->twig = new Environment($loader, [
            // 'cache' => '/path/to/compilation_cache',
        ]);
    }

    public function render($data)
    {
        $body = $this->twig->render($data['template'], $data['template_variables']);
        return $body;
    }
}
