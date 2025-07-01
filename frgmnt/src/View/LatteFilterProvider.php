<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\View;

/**
 * Provides custom filters and global functions for the Latte templating engine.
 *
 * This class centralizes the registration of custom filters (such as number formatting, JSON encoding,
 * and array reversal) and defines a global function for generating custom URLs for redirection.
 * 
 * @package View
 */
class LatteFilterProvider
{
    /**
     *C onstructs URLs using the specified controller, action, and parameters
     * in a concise, consistent format:
     * 
     * ?controller={controller}&action={action}&params=[key1:value1,key2:value2,...]
     *
     * @param string $controller Name of the class to be called.
     * @param string $action Name of the method to be called.
     * @param array $params An associative array of parameters for the view.
     * @return string{} The generated href url.
     */
    public static function customLink(string $controller, string $action, array $params = []): string
    {
        $pairs = [];
        foreach ($params as $key => $value) {
            $pairs[] = "{$key}:{$value}";
        }
        $formattedParams = '[' . implode(',', $pairs) . ']';
        return "?controller={$controller}&action={$action}&params={$formattedParams}";
    }

    /**
     * Registers all custom filters with the given Latte engine.
     *
     * @param \Latte\Engine $latte
     * @return void
     */
    public static function registerFilters(\Latte\Engine $latte): void
    {
        $latte->addFilter('number_format', function ($value, $decimals = 0, $decimalSeparator = ',', $thousandSeparator = '.') {
            return number_format($value, $decimals, $decimalSeparator, $thousandSeparator);
        });
        $latte->addFilter('json_encode', function ($value) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        });
        $latte->addFilter('array_reverse', function ($value) {
            return array_reverse(array_reverse($value));
        });

        $latte->addFunction('redirect', [self::class, 'customLink']);
    }
}