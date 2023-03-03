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
        $prepared = $this->prepareForImg();

        $output = $prepared->imageSetRender->img([
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
        $prepared = $this->prepareForImg();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender->useAspectRatio()->img([
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
        $prepared = $this->prepareForImg();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender
            ->useAspectRatio()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><img class="lazyload test" alt="This is a test" data-custom="custom" style="aspect-ratio: 1; object-fit: cover; object-position: center;" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_padding_top_strategy()
    {
        $prepared = $this->prepareForImg();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender
            ->usePaddingTop()
            ->useWrapper()
            ->img([
                'class' => 'test',
                'alt' => 'This is a test',
                'data-custom' => 'custom',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 100%;"><img class="lazyload test" alt="This is a test" data-custom="custom" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" width="1000" height="1000" data-src="cached2.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw"></div><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_can_render_using_noscript_variation()
    {
        $prepared = $this->prepareForImg();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender
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
        $prepared = $this->prepareForImg();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $prepared->_lqip->cachedMock
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $prepared->imageSetRender->useBase64Lqip()->img([
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
        $prepared = $this->prepareForPicture();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender->picture([
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
        $prepared = $this->prepareForPictureWidths();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender->picture([
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
        $prepared = $this->prepareForPictureFormats();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender->picture([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<picture>' .
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
        $prepared = $this->prepareForPicture();

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $output = $prepared->imageSetRender
            ->useAspectRatio()
            ->usePaddingTop()
            ->useWrapper()
            ->picture([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="lazyload-wrapper"><div class="lazyload-padding" style="position: relative; padding-top: 100%;"><picture><source media="(max-width: 500px)" data-srcset="cached1.jpg"><source media="(min-width: 501px)" data-srcset="cached2.jpg"><img class="lazyload test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" data-src="cached2.jpg" width="1000" height="1000"></picture></div><img class="lazyload-lqip" src="lqip.jpg"></div>',
            $output
        );
    }

    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        $this->prepareForPicture(['test' => 'test']);
    }

    public function test_accepts_valid_options()
    {
        $prepared = $this->prepareForPicture([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $this->assertTrue($prepared->imageSetRender instanceof ImageSetRender);
    }

    public function test_can_render_picture_using_options_array()
    {
        $prepared = $this->prepareForPicture([
            'baseClass' => 'base',
            'wrapperClass' => 'wrapper',
            'lqipClass' => 'lqip',
            'paddingClass' => 'padding',
            'aspectRatio' => 16 / 9,
            'paddingTop' => 16 / 9,
            'wrapper' => true,
            'base64Lqip' => true,
        ]);

        $prepared->_src->cachedMock->shouldReceive('ratio')->andReturn(1);

        $prepared->_lqip->cachedMock
            ->shouldReceive('toBase64String')
            ->andReturn('_some_base64_encoded_string_');

        $output = $prepared->imageSetRender
            ->useAspectRatio()
            ->usePaddingTop()
            ->useWrapper()
            ->picture([
                'class' => 'test',
                'alt' => 'This is a test',
            ]);

        $this->assertEquals(
            '<div class="wrapper"><div class="padding" style="position: relative; padding-top: 100%;"><picture><source media="(max-width: 500px)" data-srcset="cached1.jpg"><source media="(min-width: 501px)" data-srcset="cached2.jpg"><img class="base test" alt="This is a test" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;" data-src="cached2.jpg" width="1000" height="1000"></picture></div><img class="lqip" src="_some_base64_encoded_string_"></div>',
            $output
        );
    }

    public function test_can_override_picture_attributes()
    {
        $prepared = $this->prepareForImg();

        $output = $prepared->imageSetRender->picture([
            'alt' => 'test',
            '!data-src' => '/test.jpg',
            '!src' => true,
            '!width' => false,
            '!height' => false,
        ]);

        $this->assertEquals(
            '<picture><source data-sizes="50vw" data-srcset="cached1.jpg 500w, cached2.jpg 1000w"><img alt="test" class="lazyload" src="" data-src="/test.jpg"></picture>',
            $output
        );
    }

    public function test_can_override_img_attributes()
    {
        $prepared = $this->prepareForImg();

        $output = $prepared->imageSetRender->img([
            'alt' => 'test',
            '!data-src' => '/test.jpg',
            '!src' => true,
            '!width' => false,
            '!height' => false,
        ]);

        $this->assertEquals(
            '<img alt="test" class="lazyload" src="" data-src="/test.jpg" data-srcset="cached1.jpg 500w, cached2.jpg 1000w" data-sizes="50vw">',
            $output
        );
    }

    public function test_can_override_noscript_attributes()
    {
        $prepared = $this->prepareForImg();

        $output = $prepared->imageSetRender->noscript([
            'alt' => 'test',
            '!src' => '/test.jpg',
            '!width' => false,
            '!height' => false,
        ]);

        $this->assertEquals(
            '<img alt="test" class="lazyload-noscript" src="/test.jpg" srcset="cached1.jpg 500w, cached2.jpg 1000w" sizes="50vw">',
            $output
        );
    }

    public function test_can_initialize_from_array()
    {
        $imageSetArray = [
            'sources' => [
                [
                    'srcset' => [
                        [
                            'image' => ['cached' => ['url' => '/images/cache/01.jpg/one.jpg']],
                            'width' => 500,
                        ],
                        [
                            'image' => ['cached' => ['url' => '/images/cache/01.jpg/two.jpg']],
                            'width' => 900,
                        ],
                        [
                            'image' => ['cached' => ['url' => '/images/cache/01.jpg/three.jpg']],
                            'width' => 1300,
                        ],
                        [
                            'image' => ['cached' => ['url' => '/images/cache/01.jpg/four.jpg', 'width' => 1700, 'height' => 1160]],
                            'width' => 1700,
                        ],
                    ],
                ],
            ],
            'lqip' => ['cached' => ['url' => '/images/cache/01.jpg/lqip.gif']],
        ];

        $imageRender = ImageSetRender::fromArray($imageSetArray);

        $output = $imageRender->img([
            'class' => 'test',
            'alt' => 'This is a test',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" src="/images/cache/01.jpg/lqip.gif" width="1700" height="1160" data-src="/images/cache/01.jpg/four.jpg" data-srcset="/images/cache/01.jpg/one.jpg 500w, /images/cache/01.jpg/two.jpg 900w, /images/cache/01.jpg/three.jpg 1300w, /images/cache/01.jpg/four.jpg 1700w" data-sizes="100vw">',
            $output
        );
    }

    protected function prepareAllImages(): array
    {
        $image1 = $this->prepareImage();
        $image1->cachedMock->shouldReceive('url')->andReturn('cached1.jpg');

        $image2 = $this->prepareImage();
        $image2->cachedMock->shouldReceive('url')->andReturn('cached2.jpg');
        $image2->cachedMock
            ->shouldReceive('width')
            ->andReturn(1000)
            ->shouldReceive('height')
            ->andReturn(1000);

        $lqip = $this->prepareImage();
        $lqip->cachedMock->shouldReceive('url')->andReturn('lqip.jpg');

        return [$image1, $image2, $lqip];
    }

    protected function prepareForImg(): object
    {
        [$image1, $image2, $lqip] = $this->prepareAllImages();

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => 'source.jpg',
                    'widths' => [500, 1000],
                    'sizes' => '50vw',
                    'srcset' => [
                        ['image' => $image1->image, 'width' => 500],
                        ['image' => $image2->image, 'width' => 1000],
                    ],
                ],
            ],
            'lqip' => $lqip->image,
        ]);

        $imageSetRender = new ImageSetRender($set);

        return (object) [
            'imageSetRender' => $imageSetRender,
            '_image1' => $image1,
            '_src' => $image2,
            '_lqip' => $lqip,
        ];
    }

    protected function prepareForPicture($options = []): object
    {
        [$image1, $image2, $lqip] = $this->prepareAllImages();

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [500],
                    'media' => '(max-width: 500px)',
                    'srcset' => [['image' => $image1->image, 'width' => 500]],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1000],
                    'media' => '(min-width: 501px)',
                    'srcset' => [['image' => $image2->image, 'width' => 1000]],
                ],
            ],
            'lqip' => $lqip->image,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return (object) [
            'imageSetRender' => $imageSetRender,
            '_image1' => $image1,
            '_src' => $image2,
            '_lqip' => $lqip,
        ];
    }

    protected function prepareForPictureWidths($options = []): object
    {
        [$image1, $image2, $lqip] = $this->prepareAllImages();

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'srcset' => [
                        ['image' => $image1->image, 'width' => 400],
                        ['image' => $image1->image, 'width' => 800],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'srcset' => [
                        ['image' => $image2->image, 'width' => 1200],
                        ['image' => $image2->image, 'width' => 1600],
                    ],
                ],
            ],
            'lqip' => $lqip->image,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return (object) [
            'imageSetRender' => $imageSetRender,
            '_image1' => $image1,
            '_src' => $image2,
            '_lqip' => $lqip,
        ];
    }

    protected function prepareForPictureFormats($options = []): object
    {
        [$image1, $image2, $lqip] = $this->prepareAllImages();

        ($set = $this->mockImageSet())->shouldReceive('data')->andReturn([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'format' => 'webp',
                    'srcset' => [
                        ['image' => $image1->image, 'width' => 400],
                        ['image' => $image1->image, 'width' => 800],
                    ],
                ],
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'sizes' => '66vw',
                    'format' => 'jpg',
                    'srcset' => [
                        ['image' => $image1->image, 'width' => 400],
                        ['image' => $image1->image, 'width' => 800],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'format' => 'webp',
                    'srcset' => [
                        ['image' => $image2->image, 'width' => 1200],
                        ['image' => $image2->image, 'width' => 1600],
                    ],
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'sizes' => '66vw',
                    'format' => 'jpg',
                    'srcset' => [
                        ['image' => $image2->image, 'width' => 1200],
                        ['image' => $image2->image, 'width' => 1600],
                    ],
                ],
            ],
            'lqip' => $lqip->image,
        ]);

        $imageSetRender = new ImageSetRender($set, $options);

        return (object) [
            'imageSetRender' => $imageSetRender,
            '_src' => $image2,
            '_lqip' => $lqip,
        ];
    }
}
