<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\Tests\Helpers\Mocking;
use PHPUnit\Framework\TestCase;

class ImageSetRenderTest extends TestCase
{
    use Mocking;

    public function test_can_render_img()
    {
        [$imageSetRender] = $this->prepareImageSetRender();

        $output = $imageSetRender->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" src="lqip.jpg" width="4000" height="3000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_render_using_aspect_ratio()
    {
        [$imageSetRender, $lqip] = $this->prepareImageSetRender();

        $lqip
            ->source()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender->useAspectRatio()->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1;" src="lqip.jpg" width="4000" height="3000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_render_using_wrapper_element()
    {
        [$imageSetRender, $lqip] = $this->prepareImageSetRender();

        $lqip
            ->source()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender
            ->useAspectRatio()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1;" src="lqip.jpg" width="4000" height="3000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_padding_top_strategy()
    {
        [$imageSetRender, $lqip] = $this->prepareImageSetRender();

        $lqip
            ->source()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender
            ->usePaddingTop()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 100%;"><img class="lazyload test" alt="This is a test" data-custom="custom" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" src="lqip.jpg" width="4000" height="3000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"></div><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_noscript_variation()
    {
        [$imageSetRender, $lqip] = $this->prepareImageSetRender();

        $lqip
            ->source()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender
            ->usePaddingTop()
            ->useWrapper()
            ->noScript([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper-noscript"><div class="lazyload-padding-noscript" style="position: relative; padding-top: 100%;"><img class="lazyload-noscript test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" width="4000" height="3000" src="cached2.jpg" srcset="cached1.jpg 500w, cached2.jpg 1000w" sizes="50vw"></div></div>',
            $output
        );
    }

    protected function prepareImageSetRender()
    {
        ($image1 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg');

        ($image2 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached2.jpg');

        ($lqip = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('lqip.jpg');

        $lqip
            ->source()
            ->shouldReceive('width')
            ->andReturn(4000)
            ->shouldReceive('height')
            ->andReturn(3000);

        ($set = $this->mockImageSet())->shouldReceive('toArray')->andReturn([
            'sizes' => '50vw',
            'srcset' => [
                [
                    'image' => $image1,
                    'width' => 500,
                ],
                [
                    'image' => $image2,
                    'width' => 1000,
                ],
            ],
            'lqip' => $lqip,
        ]);

        $imageSetRender = new ImageSetRender($set);

        return [$imageSetRender, $lqip];
    }
}
