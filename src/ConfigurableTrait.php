<?php
namespace Flou;

/**
 * Flou\ConfigurableTrait is used to add global configuration to base classes.
 */
trait ConfigurableTrait
{
    private static $configuration = [];


    public static function configure($options)
    {
        if (is_array($options)) {
            self::$configuration = array_merge(self::$configuration, $options);
        }
    }

    public static function getConfig($name)
    {
        return self::$configuration[$name] ?? null;
    }
}
