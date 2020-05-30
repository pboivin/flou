<?php

use Flou\DefaultImageRenderer;

class CustomImageRenderer extends DefaultImageRenderer
{
    public function render()
    {
        $html = parent::render();
        return "<div class=\"extra-wrapper\">$html</div>";
    }
}
