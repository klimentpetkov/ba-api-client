<?php


namespace App;

use Twig\Loader\FilesystemLoader as TwigFsLoader;
use Twig\Environment as TwigEnv;

class TemplateRenderer
{
    protected $loader;
    protected $twig;

    /**
     * TemplateRenderer constructor.
     */
    public function __construct()
    {
        $this->loader = new TwigFsLoader('../views');

        // with template caching
        $this->twig = new TwigEnv($this->loader, [
            'cache' => '../cache/templates',
        ]);

        // without cache
//        $this->twig = new TwigEnv($this->loader);
    }

    /**
     * @param string $template
     * @param array $data
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderData($template, $data)
    {
        $templateError = "Template error caught: ";

        try {
            echo $this->twig->render($template, $data);
        }
        catch (\Twig\Error\LoaderError $loaderError) {
            echo $templateError . $loaderError->getMessage();
        }
        catch (\Twig\Error\RuntimeError $runtimeError) {
            echo $templateError . $runtimeError->getMessage();
        }
        catch (\Twig\Error\SyntaxError $syntaxError) {
            echo $templateError . $syntaxError->getMessage();
        }
    }
}