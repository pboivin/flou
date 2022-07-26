<?php

namespace Pboivin\Flou;

trait RendersHtml
{
    protected function htmlTag(
        string $tag,
        array $attributes = [],
        bool $selfClosing = false
    ): string {
        if ($selfClosing === true) {
            return "<{$tag} " . $this->collectHtmlAttributes($attributes) . ' />';
        }

        return "<{$tag} " . $this->collectHtmlAttributes($attributes) . '>';
    }

    protected function htmlWrap($tag, array $attributes = [], string $content = ''): string
    {
        return "<{$tag} " . $this->collectHtmlAttributes($attributes) . ">{$content}</{$tag}>";
    }

    protected function collectHtmlAttributes(array $attributes = [])
    {
        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return implode(' ', $output);
    }

    protected function collectStyles(array $styles = [])
    {
        $output = [];

        foreach ($styles as $key => $value) {
            $output[] = "{$key}: {$value};";
        }

        return implode(' ', $output);
    }
}
