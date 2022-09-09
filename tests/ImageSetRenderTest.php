<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\Tests\Concerns\Mocking;

class ImageSetRenderTest extends TestCase
{
    use Mocking;

    public function test_can_render_img()
    {
        [$imageSetRender] = $this->prepareForImg();

        $output = $imageSetRender->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" src="lqip.jpg" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_render_using_aspect_ratio()
    {
        [$imageSetRender, $src] = $this->prepareForImg();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender->useAspectRatio()->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1; object-fit: cover; object-position: center;" src="lqip.jpg" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_render_using_wrapper_element()
    {
        [$imageSetRender, $src] = $this->prepareForImg();

        $src->cached()
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
            '<div class="lazyload-wrapper"><img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1; object-fit: cover; object-position: center;" src="lqip.jpg" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_padding_top_strategy()
    {
        [$imageSetRender, $src] = $this->prepareForImg();

        $src->cached()
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
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 100%;"><img class="lazyload test" alt="This is a test" data-custom="custom" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" src="lqip.jpg" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"></div><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_noscript_variation()
    {
        [$imageSetRender, $src] = $this->prepareForImg();

        $src->cached()
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
            '<div class="lazyload-wrapper-noscript"><div class="lazyload-padding-noscript" style="position: relative; padding-top: 100%;"><img class="lazyload-noscript test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" width="1000" height="1000" src="cached2.jpg" srcset="cached1.jpg 500w, cached2.jpg 1000w" sizes="50vw"></div></div>',
            $output
        );
    }

    public function test_can_render_using_base64_lqip()
    {
        [$imageSetRender, $src, $lqip] = $this->prepareForImg();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $lqip
            ->cached()
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $imageSetRender->useBase64Lqip()->img([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" src="_some_base64_encoded_string_" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_render_picture()
    {
        [$imageSetRender, $src] = $this->prepareForPicture();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender->picture([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<picture><source media="(max-width: 500px)" data-srcset="cached1.jpg"><source media="(min-width: 501px)" data-srcset="cached2.jpg"><img class="lazyload test" alt="This is a test" src="lqip.jpg" data-src="cached2.jpg" width="1000" height="1000"></picture>',
            $output
        );
    }

    public function test_can_render_picture_widths()
    {
        [$imageSetRender, $src] = $this->prepareForPictureWidths();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender->picture([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<picture><source media="(max-width: 1023px)" data-sizes="66vw" data-srcset="cached1.jpg 400w, cached1.jpg 800w"><source media="(min-width: 1024px)" data-sizes="66vw" data-srcset="cached2.jpg 1200w, cached2.jpg 1600w"><img class="lazyload test" alt="This is a test" src="lqip.jpg" data-src="cached2.jpg" width="1000" height="1000"></picture>',
            $output
        );
    }

    public function test_can_render_picture_formats()
    {
        [$imageSetRender, $src] = $this->prepareForPictureFormats();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender->picture([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<picture >' .
                '<source type="image/webp" media="(max-width: 1023px)" data-sizes="66vw" data-srcset="cached1.jpg 400w, cached1.jpg 800w">' .
                '<source type="image/jpeg" media="(max-width: 1023px)" data-sizes="66vw" data-srcset="cached1.jpg 400w, cached1.jpg 800w">' .
                '<source type="image/webp" media="(min-width: 1024px)" data-sizes="66vw" data-srcset="cached2.jpg 1200w, cached2.jpg 1600w">' .
                '<source type="image/jpeg" media="(min-width: 1024px)" data-sizes="66vw" data-srcset="cached2.jpg 1200w, cached2.jpg 1600w">' .
                '<img class="lazyload test" alt="This is a test" src="lqip.jpg" data-src="cached2.jpg" width="1000" height="1000">' .
                '</picture>',
            $output
        );
    }

    public function test_can_render_picture_with_options()
    {
        [$imageSetRender, $src] = $this->prepareForPicture();

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $output = $imageSetRender
            ->useAspectRatio()
            ->usePaddingTop()
            ->useWrapper()
            ->picture([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 100%;"><picture><source media="(max-width: 500px)" data-srcset="cached1.jpg"><source media="(min-width: 501px)" data-srcset="cached2.jpg"><img class="lazyload test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" src="lqip.jpg" data-src="cached2.jpg" width="1000" height="1000"></picture></div><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        [$imageSetRender] = $this->prepareForPicture(['test' => 'test']);
    }

    public function test_accepts_valid_options()
    {
        [$imageSetRender] = $this->prepareForPicture([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $this->assertTrue(!!$imageSetRender);
    }

    public function test_can_render_picture_using_options_array()
    {
        [$imageSetRender, $src, $lqip] = $this->prepareForPicture([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $src->cached()
            ->shouldReceive('ratio')
            ->andReturn(1);

        $lqip
            ->cached()
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $imageSetRender
            ->useAspectRatio()
            ->usePaddingTop()
            ->useWrapper()
            ->picture([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="wrapper"><div class="padding" style="position: relative; padding-top: 100%;"><picture><source media="(max-width: 500px)" data-srcset="cached1.jpg"><source media="(min-width: 501px)" data-srcset="cached2.jpg"><img class="base test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" src="_some_base64_encoded_string_" data-src="cached2.jpg" width="1000" height="1000"></picture></div><img class="lqip" src="_some_base64_encoded_string_"></div>',
            $output
        );
    }

    protected function prepareForImg()
    {
        ($image1 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg');

        ($image2 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached2.jpg');

        $image2
            ->cached()
            ->shouldReceive('width')
            ->andReturn(1000)
            ->shouldReceive('height')
            ->andReturn(1000);

        ($lqip = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('lqip.jpg');

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => 'source.jpg',
                    'widths' => [500, 1000],
                    'sizes' => '50vw',
                    'srcset' => [
                        ['image' => $image1, 'width' => 500],
                        ['image' => $image2, 'width' => 1000],
                    ],
                ],
            ],
            'lqip' => $lqip,
        ]);

        $imageSetRender = new ImageSetRender($set);

        return [$imageSetRender, $image2, $lqip];
    }

    protected function prepareForPicture($options = [])
    {
        ($image1 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg');

        ($image2 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached2.jpg');

        $image2
            ->cached()
            ->shouldReceive('width')
            ->andReturn(1000)
            ->shouldReceive('height')
            ->andReturn(1000);

        ($lqip = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('lqip.jpg');

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [500],
                    'media' => '(max-width: 500px)',
                    'srcset' => [['image' => $image1, 'width' => 500]],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1000],
                    'media' => '(min-width: 501px)',
                    'srcset' => [['image' => $image2, 'width' => 1000]],
                ],
            ],
            'lqip' => $lqip,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return [$imageSetRender, $image2, $lqip];
    }

    protected function prepareForPictureWidths($options = [])
    {
        ($image1 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg');

        ($image2 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached2.jpg');

        $image2
            ->cached()
            ->shouldReceive('width')
            ->andReturn(1000)
            ->shouldReceive('height')
            ->andReturn(1000);

        ($lqip = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('lqip.jpg');

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'srcset' => [
                        ['image' => $image1, 'width' => 400],
                        ['image' => $image1, 'width' => 800],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'srcset' => [
                        ['image' => $image2, 'width' => 1200],
                        ['image' => $image2, 'width' => 1600],
                    ],
                ],
            ],
            'lqip' => $lqip,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return [$imageSetRender, $image2, $lqip];
    }

    protected function prepareForPictureFormats($options = [])
    {
        ($image1 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg');

        ($image2 = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached2.jpg');

        $image2
            ->cached()
            ->shouldReceive('width')
            ->andReturn(1000)
            ->shouldReceive('height')
            ->andReturn(1000);

        ($lqip = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('lqip.jpg');

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'format' => 'webp',
                    'srcset' => [
                        ['image' => $image1, 'width' => 400],
                        ['image' => $image1, 'width' => 800],
                    ],
                ],
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'format' => 'jpg',
                    'srcset' => [
                        ['image' => $image1, 'width' => 400],
                        ['image' => $image1, 'width' => 800],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'format' => 'webp',
                    'srcset' => [
                        ['image' => $image2, 'width' => 1200],
                        ['image' => $image2, 'width' => 1600],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'format' => 'jpg',
                    'srcset' => [
                        ['image' => $image2, 'width' => 1200],
                        ['image' => $image2, 'width' => 1600],
                    ],
                ],
            ],
            'lqip' => $lqip,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return [$imageSetRender, $image2, $lqip];
    }
}
