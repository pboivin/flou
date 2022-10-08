<?php

namespace Pboivin\Flou;

trait RendersHtml
{
    protected function htmlTag(
        string $tag,
        array $attributes = [],
        bool $selfClosing = false
    ): string {
        $finalAttributes = $this->collectHtmlAttributes($attributes);
        $finalAttributes = $finalAttributes ? " $finalAttributes" : '';

        $end = $selfClosing ? ' /' : '';

        return "<{$tag}{$finalAttributes}{$end}>";
    }

    protected function htmlWrap($tag, array $attributes = [], string $content = ''): string
    {
        $finalAttributes = $this->collectHtmlAttributes($attributes);
        $finalAttributes = $finalAttributes ? " $finalAttributes" : '';

        return "<{$tag}{$finalAttributes}>{$content}</{$tag}>";
    }

    protected function collectHtmlAttributes(array $attributes = []): string
    {
        $output = [];

        foreach ($attributes as $key => $value) {
            $output[] = $key . '="' . $value . '"';
        }

        return implode(' ', array_filter($output));
    }

    protected function collectStyles(array $styles = []): string
    {
        $output = [];

        foreach ($styles as $key => $value) {
            $output[] = "{$key}: {$value};";
        }

        return implode(' ', array_filter($output));
    }

    protected function collectClasses(array $classes = []): string
    {
        return implode(' ', array_filter($classes));
    }
}
