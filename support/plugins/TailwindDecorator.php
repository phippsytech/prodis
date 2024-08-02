<?php

class TailwindDecorator extends AbstractPicoPlugin
{
    protected $enabled = true;

    public function onContentParsed(&$content)
    {
        // Use a simple regex to add Tailwind classes to h1 tags
        $content = preg_replace('/<h1>/', '<h1 class="text-3xl leading-6 font-fredoka-one-regular mb-2" style="color:#220055;">', $content);
        $content = preg_replace('/<h2>/', '<h2 class="text-2xl leading-6 font-fredoka-one-regular mb-3 " style="color:#220055;">', $content);
        $content = preg_replace('/<p>/', '<p class="mb-4">', $content);
    }
}
