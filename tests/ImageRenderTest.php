<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Concerns\Mocking;

class ImageRenderTest extends TestCase
{
    use Mocking;

    public function test_can_render_img()
    {
        $prepared = $this->prepareImageRender();

        $output = $prepared->imageRender->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" src="/cached.jpg" data-src="/source.jpg" width="800" height="600">',
            $output
        );
    }

    public function test_can_render_using_aspect_ratio()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $output = $prepared->imageRender->useAspectRatio()->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1.33; object-fit: cover; object-position: center;" src="/cached.jpg" data-src="/source.jpg" width="800" height="600">',
            $output
        );
    }

    public function test_can_render_preserving_existing_styles()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $output = $prepared->imageRender->useAspectRatio()->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
            'style' => 'border: 1px solid red;',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1.33; object-fit: cover; object-position: center; border: 1px solid red;" src="/cached.jpg" data-src="/source.jpg" width="800" height="600">',
            $output
        );
    }

    public function test_can_render_using_wrapper_element()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $output = $prepared->imageRender
            ->useAspectRatio()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1.33; object-fit: cover; object-position: center;" data-src="/source.jpg" width="800" height="600"><img class="lazyload-lqip" src="/cached.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_padding_top_strategy()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $output = $prepared->imageRender
            ->usePaddingTop()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 75.19%;"><img class="lazyload test" alt="This is a test" data-custom="custom" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" data-src="/source.jpg" width="800" height="600"></div><img class="lazyload-lqip" src="/cached.jpg"></div>',
            $output
        );
    }

    public function test_can_render_noscript_variation()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $output = $prepared->imageRender
            ->usePaddingTop()
            ->useWrapper()
            ->noScript([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper-noscript"><div class="lazyload-padding-noscript" style="position: relative; padding-top: 75.19%;"><img class="lazyload-noscript test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" src="/source.jpg" width="800" height="600"></div></div>',
            $output
        );
    }

    public function test_can_render_using_base64_lqip()
    {
        $prepared = $this->prepareImageRender();

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $prepared->cachedMock
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $prepared->imageRender->useBase64Lqip()->img([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" src="_some_base64_encoded_string_" data-src="/source.jpg" width="800" height="600">',
            $output
        );
    }

    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        $this->prepareImageRender(['test' => 'test']);
    }

    public function test_accepts_valid_options()
    {
        $prepared = $this->prepareImageRender([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $this->assertTrue(!!$prepared->imageRender);
    }

    public function test_can_render_using_options_array()
    {
        $prepared = $this->prepareImageRender([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $prepared->sourceMock->shouldReceive('ratio')->andReturn(1.33);

        $prepared->cachedMock
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $prepared->imageRender->useBase64Lqip()->img([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<div class="wrapper"><div class="padding" style="position: relative; padding-top: 56.25%;"><img class="base test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" data-src="/source.jpg" width="800" height="600"></div><img class="lqip" src="_some_base64_encoded_string_"></div>',
            $output
        );
    }

    protected function prepareImageRender($options = []): object
    {
        $prepared = $this->prepareImage();

        $prepared->imageRender = new ImageRender($prepared->image, $options);

        $prepared->sourceMock->shouldReceive('url')->andReturn('/source.jpg');

        $prepared->sourceMock->shouldReceive('width')->andReturn(800);

        $prepared->sourceMock->shouldReceive('height')->andReturn(600);

        $prepared->cachedMock->shouldReceive('url')->andReturn('/cached.jpg');

        return $prepared;
    }
}
