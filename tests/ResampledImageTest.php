<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\ResampledImage;
use Pboivin\Flou\Tests\Concerns\Mocking;
use PHPUnit\Framework\TestCase;

class ResampledImageTest extends TestCase
{
    use Mocking;

    public function test_can_access_source_image_file()
    {
        $resampledImage = new ResampledImage($this->mockImageFile(), $this->mockFactory());

        $this->assertTrue($resampledImage->source() instanceof ImageFile);
    }

    public function test_can_make_resampled_image_variations()
    {
        ($source = $this->mockImageFile())
            ->shouldReceive('fileName')
            ->andReturn('test.jpg');

        ($factory = $this->mockFactory())
            ->shouldReceive('image')
            ->andReturn($this->mockImage());

        $resampledImage = new ResampledImage($source, $factory);

        $this->assertTrue($resampledImage->make(['w' => 500]) instanceof Image);
    }
}
