<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSetConfig
{
    protected $preparedConfig = [];

    public function __construct(protected array $config)
    {
        if (isset($config['sources'])) {
            $this->preparedConfig = $this->acceptLegacyConfig($config);
        } else {
            $this->preparedConfig = $this->acceptConfig($config);
        }
    }

    protected function acceptLegacyConfig(array $config): array
    {
        if (isset($config['image'])) {
            return $this->acceptConfig([
                'image' => $config['image'],
                'widths' => array_map(fn ($i) => (int) $i['width'], $config['sources']),
            ]);
        }

        return $this->acceptConfig(
            array_map(function ($i) {
                $i['widths'] = [$i['width']];
                unset($i['width']);
                return $i;
            }, $config['sources'])
        );
    }

    protected function acceptConfig(array $config): array
    {
        if (isset($config['image'])) {
            return $this->validateItems([$config]);
        }

        $first = array_values($config)[0] ?? null;

        if (is_array($first)) {
            return $this->validateItems($config);
        }

        throw new InvalidArgumentException('Invalid ImageSet configuration — missing image.');
    }

    protected function validateItems(array $items): array
    {
        $validItems = [];

        foreach ($items as $item) {
            if (!isset($item['widths'])) {
                throw new InvalidArgumentException(
                    "Missing required 'widths' argument for single image."
                );
            }

            if (count($items) > 1 && !isset($item['media'])) {
                throw new InvalidArgumentException(
                    "Missing required 'media' argument for multiple images."
                );
            }

            $this->handleFormats($item, $validItems);
        }

        return $validItems;
    }

    protected function handleFormats(array $item, array &$validItems): void
    {
        if (isset($item['formats'])) {
            foreach ($item['formats'] as $format) {
                $i = $item;
                $i['format'] = $format;
                unset($i['formats']);

                $validItems[] = $i;
            }
        } else {
            $validItems[] = $item;
        }
    }

    public function get(): array
    {
        return $this->preparedConfig;
    }
}
