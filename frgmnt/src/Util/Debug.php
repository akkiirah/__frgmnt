<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Util;

/**
 * Provides interactive debugging functionality for PHP variables.
 *
 * This class offers a static method to output variables in a structured,
 * collapsible format, making it easier to inspect nested arrays and objects.
 * The output is rendered in dedicated panels and utilizes external CSS for styling.
 *
 * Usage examples:
 * - Debug::var_dump('$data', $data);
 * - Debug::var_dump([
 *       '$data'      => $data,
 *       '$otherData' => $otherData,
 *   ]);
 *
 * @package Util
 */
class Debug
{
    /**
     * Dumps one or more variables for debugging purposes.
     *
     * Accepts either two parameters (a title and a variable)
     * or a single associative array where each key is the title.
     * Output consists of one or more debug panels with interactive expand/collapse
     * for nested structures.
     *
     * @param mixed ...$args Either a pair (string $name, mixed $data) or an associative array.
     * @return void
     */
    public static function var_dump(...$args): void
    {
        $debugs = [];
        if (count($args) === 1 && is_array($args[0]) && array_keys($args[0]) !== range(0, count($args[0]) - 1)) {
            $debugs = $args[0];
        } elseif (count($args) === 2 && is_string($args[0])) {
            $debugs[$args[0]] = $args[1];
        } else {
            foreach ($args as $i => $value) {
                $debugs["Debug " . ($i + 1)] = $value;
            }
        }

        echo '<div id="debug-container">';
        echo '<script>
        function toggleExpand(link) {
            var content = link.nextElementSibling;
            if (content.style.display === "none") {
                content.style.display = "block";
                link.textContent = " - ";
            } else {
                content.style.display = "none";
                link.textContent = " + ";
            }
        }
        </script>';

        foreach ($debugs as $name => $value) {
            self::renderPanel($name, $value);
        }
        echo '</div>';
    }

    /**
     * Renders an individual debug panel with a header and content.
     *
     * Each panel displays a title and the recursively formatted output of the variable.
     *
     * @param string $name  The title for the debug panel.
     * @param mixed  $value The variable to be dumped.
     * @return void
     */
    private static function renderPanel(string $name, $value): void
    {
        echo '<div class="debug-panel">';
        echo '<div class="debug-header">' . htmlspecialchars($name) . '</div>';
        echo '<pre class="debug-content">' . self::renderRecursive($value) . '</pre>';
        echo '</div>';
    }

    /**
     * Recursively renders arrays and objects in a structured format.
     *
     * Nested arrays and objects are initially collapsed and can be expanded
     * by clicking the toggle link. Indentation is added according to the nesting level
     * including public, protected, and private attributes using reflection.
     * Also applies formatting to differentiate data types.
     *
     * @param mixed $var   The variable to render.
     * @param int   $level The current nesting level for indentation.
     * @return string Returns the formatted string representation of the variable.
     */
    private static function renderRecursive($var, int $level = 0): string
    {
        $indent = str_repeat("  ", $level);
        if (is_array($var)) {
            $output = "array(" . count($var) . ") {";
            foreach ($var as $key => $value) {
                $output .= "\n" . $indent . "  [" . htmlspecialchars($key) . "] => ";
                if (is_array($value) || is_object($value)) {
                    $output .= '<a href="#" class="toggle-link" onclick="toggleExpand(this); return false;"> + </a>';
                    $output .= '<div class="toggle-content" style="display: none;">' . self::renderRecursive($value, $level + 1) . '</div>';
                } else {
                    $output .= self::formatValue($value);
                }
            }
            $output .= "\n" . $indent . "}";
            return $output;
        } elseif (is_object($var)) {
            $class = get_class($var);
            $output = "object(" . $class . ") {";
            $reflection = new \ReflectionClass($var);
            $properties = $reflection->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                if ($property->isPublic()) {
                    $visibility = 'public';
                } elseif ($property->isProtected()) {
                    $visibility = 'protected';
                } else {
                    $visibility = 'private';
                }
                $propName = $property->getName();
                $propValue = $property->getValue($var);
                $output .= "\n" . $indent . "  [$visibility \$$propName] => ";
                if (is_array($propValue) || is_object($propValue)) {
                    $output .= '<a href="#" class="toggle-link" onclick="toggleExpand(this); return false;"> + </a>';
                    $output .= '<div class="toggle-content" style="display: none;">' . self::renderRecursive($propValue, $level + 1) . '</div>';
                } else {
                    $output .= self::formatValue($propValue);
                }
            }
            $output .= "\n" . $indent . "}";
            return $output;
        } else {
            return self::formatValue($var);
        }
    }

    /**
     * Formats a scalar value with type-specific styling.
     *
     * Strings are wrapped in quotes and assigned a CSS class,
     * booleans, numbers, and NULL values are similarly wrapped for differentiation.
     *
     * @param mixed $value The scalar value to format.
     * @return string The formatted value as an HTML string.
     */
    private static function formatValue($value): string
    {
        if (is_string($value)) {
            return '<span class="debug-string">"' . htmlspecialchars($value) . '"</span>';
        } elseif (is_bool($value)) {
            return '<span class="debug-bool">' . ($value ? 'true' : 'false') . '</span>';
        } elseif (is_null($value)) {
            return '<span class="debug-null">NULL</span>';
        } elseif (is_int($value) || is_float($value)) {
            return '<span class="debug-number">' . $value . '</span>';
        } else {
            return '<span class="debug-scalar">' . htmlspecialchars(var_export($value, true)) . '</span>';
        }
    }
}
