<?php

namespace SSF\ORM\Util;

/**
 * @method static string camel(string $value, bool $callIsCached = true)
 * @method static string class(string $value, bool $callIsCached = true)
 * @method static string kebab(string $value, bool $callIsCached = true)
 * @method static string snake(string $value, string $delimiter = '_', bool $callIsCached = true)
 * @method static string studly(string $value, bool $callIsCached = true)
 * @method static string plural(string $value, int $count = null, bool $callIsCached = true)
 * @method static string singular(string $value, bool $callIsCached = true)
 */
class Str
{
    /**
     * @var array
     */
    private static array $cache = [];

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|string
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (static::callIsCached($name, $arguments)) {
            $key = static::cacheKey($name, $arguments);
            return ! isset(static::$cache[$key])
                ? static::$cache[$key] = static::invokeMethod($name, $arguments)
                : static::$cache[$key];
        }

        return static::invokeMethod($name, $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    private static function callIsCached(string $name, array $arguments): bool
    {
        return match($name) {
            'camel', 'plural' => $arguments[2] ?? true,
            default => $arguments[1] ?? true
        };
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    private static function cacheKey(string $name, array $arguments): string
    {
        return sha1($name . '-' . implode('-', $arguments));
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return string
     */
    private static function invokeMethod(string $name, array $arguments): string
    {
        return static::getMethod($name)(...$arguments);
    }

    /**
     * @param string $name
     * @return callable
     */
    private static function getMethod(string $name): callable
    {
        return match($name) {
            'class' => fn(string $value) => substr($value,strrpos($value, '\\') + 1),
            'camel' => fn(string $value) => lcfirst(static::studly($value, false)),
            'kebab' => fn(string $value) => static::snake($value, '_', false),
            'snake' => function(string $value, string $delimiter = '_') {
                if (!ctype_lower($value)) {
                    $value = preg_replace('/[\s_-]+/', $delimiter, $value);
                    $value = strtolower(preg_replace('/([a-z])([A-Z])/', "$1{$delimiter}$2", $value));
                }
                return $value;
            },
            'studly' => fn(string $value) => str_replace(' ', '', ucwords(preg_replace('/[_\-\s]+/', ' ', $value))),
            'plural' => function(string $value, int $count = null) {
                if ($count === 1) {
                    return $value;
                }
                foreach([
                    '/(quiz)$/i'               => '\1zes',
                    '/^(ox)$/i'                => '\1en',
                    '/([m|l])ouse$/i'          => '\1ice',
                    '/(matr|vert|ind)ix|ex$/i' => '\1ices',
                    '/(x|ch|ss|sh)$/i'         => '\1es',
                    '/([^aeiouy]|qu)y$/i'      => '\1ies',
                    '/(hive)$/i'               => '\1s',
                    '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
                    '/sis$/i'                  => 'ses',
                    '/([ti])um$/i'             => '\1a',
                    '/(buffal|tomat)o$/i'      => '\1oes',
                    '/(bu)s$/i'                => '\1ses',
                    '/(alias|status)$/i'       => '\1es',
                    '/(octop|vir)us$/i'        => '\1i',
                    '/(ax|tests)is$/i'          => '\1es',
                    '/s$/i'                    => 's',
                    '/$/'                      => 's'
                ] as $pattern => $replacement) {
                    if (preg_match($pattern, $value)) {
                        return preg_replace($pattern, $replacement, $value);
                    }
                }
                return $value;
            },
            'singular' => function(string $value) {
                foreach([
                    '/(quiz)zes$/i'             => '\1',
                    '/(matr)ices$/i'            => '\1ix',
                    '/(vert|ind)ices$/i'        => '\1ex',
                    '/^(ox)en$/i'               => '\1',
                    '/(alias|status)es$/i'      => '\1',
                    '/([octop|vir])i$/i'        => '\1us',
                    '/(cris|ax|test)es$/i'      => '\1is',
                    '/(shoe)s$/i'               => '\1',
                    '/(o)es$/i'                 => '\1',
                    '/(bus)es$/i'               => '\1',
                    '/([m|l])ice$/i'            => '\1ouse',
                    '/(x|ch|ss|sh)es$/i'        => '\1',
                    '/(m)ovies$/i'              => '\1ovie',
                    '/(s)eries$/i'              => '\1eries',
                    '/([^aeiouy]|qu)ies$/i'     => '\1y',
                    '/([lr])ves$/i'             => '\1f',
                    '/(tive)s$/i'               => '\1',
                    '/(hive)s$/i'               => '\1',
                    '/([^f])ves$/i'             => '\1fe',
                    '/(^analy)ses$/i'           => '\1sis',
                    '/([ti])a$/i'               => '\1um',
                    '/(buffal|tomat)oes$/i'     => '\1o',
                    '/(bu)s$/i'                 => '\1',
                    '/(octop|vir)i$/i'          => '\1us',
                    '/(ax|test)es$/i'           => '\1is',
                    '/[^aeiou]s$/i'             => ''
                ] as $pattern => $replacement) {
                    if (preg_match($pattern, $value)) {
                        return preg_replace($pattern, $replacement, $value);
                    }
                }
                return $value;
            }
        };
    }
}