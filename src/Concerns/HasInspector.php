<?php

namespace Pboivin\Flou\Concerns;

use Pboivin\Flou\ImageFileInspector;

trait HasInspector
{
    protected $inspector;

    public function inspector(): ImageFileInspector
    {
        if (!$this->inspector) {
            $this->inspector = new ImageFileInspector();
        }

        return $this->inspector;
    }

    public function setInspector(ImageFileInspector $inspector): self
    {
        $this->inspector = $inspector;

        return $this;
    }
}
