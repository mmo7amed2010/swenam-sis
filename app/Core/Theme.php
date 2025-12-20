<?php

namespace App\Core;

class Theme
{
    /**
     * Variables
     *
     * @var bool
     */
    public static $modeSwitchEnabled = false;

    public static $modeDefault = 'light';

    public static $direction = 'ltr';

    public static $htmlAttributes = [];

    public static $htmlClasses = [];

    /**
     * Keep page level assets
     *
     * @var array
     */
    public static $javascriptFiles = [];

    public static $cssFiles = [];

    public static $vendorFiles = [];

    /**
     * Get product name
     */
    public function getName(): mixed
    {
        return null;
    }

    /**
     * Add HTML attributes by scope
     */
    public function addHtmlAttribute(string $scope, string $name, mixed $value): void
    {
        self::$htmlAttributes[$scope][$name] = $value;
    }

    /**
     * Add multiple HTML attributes by scope
     */
    public function addHtmlAttributes(string $scope, array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            self::$htmlAttributes[$scope][$key] = $value;
        }
    }

    /**
     * Add HTML class by scope
     */
    public function addHtmlClass(string $scope, string $value): void
    {
        self::$htmlClasses[$scope][] = $value;
    }

    /**
     * Remove HTML class by scope
     */
    public function removeHtmlClass(string $scope, string $value): void
    {
        $key = array_search($value, self::$htmlClasses[$scope]);
        unset(self::$htmlClasses[$scope][$key]);
    }

    /**
     * Print HTML attributes for the HTML template
     */
    public function printHtmlAttributes(string $scope): string
    {
        $attributes = [];
        if (isset(self::$htmlAttributes[$scope])) {
            foreach (self::$htmlAttributes[$scope] as $key => $value) {
                $attributes[] = sprintf('%s="%s"', $key, $value);
            }
        }

        return implode(' ', $attributes);
    }

    /**
     * Print HTML classes for the HTML template
     */
    public function printHtmlClasses(string $scope, bool $full = true): string|array
    {
        if (empty(self::$htmlClasses)) {
            return '';
        }

        $classes = [];
        if (isset(self::$htmlClasses[$scope])) {
            $classes = self::$htmlClasses[$scope];
        }

        if ($full) {
            return sprintf('class="%s"', implode(' ', (array) $classes));
        }

        return $classes;
    }

    /**
     * Get SVG icon content
     */
    public function getSvgIcon(string $path, string $classNames = 'svg-icon'): string
    {
        if (file_exists(public_path('assets/media/icons/'.$path))) {
            return sprintf('<span class="%s">%s</span>', $classNames, file_get_contents(public_path('assets/media/icons/'.$path)));
        }

        return '';
    }

    /**
     * Set dark mode enabled status
     */
    public function setModeSwitch(bool $flag): void
    {
        self::$modeSwitchEnabled = $flag;
    }

    /**
     * Check dark mode status
     */
    public function isModeSwitchEnabled(): bool
    {
        return self::$modeSwitchEnabled;
    }

    /**
     * Set the mode to dark or light
     */
    public function setModeDefault(string $mode): void
    {
        self::$modeDefault = $mode;
    }

    /**
     * Get current mode
     */
    public function getModeDefault(): string
    {
        return self::$modeDefault;
    }

    /**
     * Set style direction
     */
    public function setDirection(string $direction): void
    {
        self::$direction = $direction;
    }

    /**
     * Get style direction
     */
    public function getDirection(): string
    {
        return self::$direction;
    }

    /**
     * Extend CSS file name with RTL or dark mode
     */
    public function extendCssFilename(string $path): string
    {
        if ($this->isRtlDirection()) {
            $path = str_replace('.css', '.rtl.css', $path);
        }

        return $path;
    }

    /**
     * Check if style direction is RTL
     */
    public function isRtlDirection(): bool
    {
        return self::$direction === 'rtl';
    }

    /**
     * Include favicon from settings
     */
    public function includeFavicon(): string
    {
        return sprintf('<link rel="shortcut icon" href="%s" />', asset(config('settings.KT_THEME_ASSETS.favicon')));
    }

    /**
     * Include the fonts from settings
     */
    public function includeFonts(): string
    {
        $content = '';

        foreach (config('settings.KT_THEME_ASSETS.fonts') as $url) {
            $content .= sprintf('<link rel="stylesheet" href="%s">', asset($url));
        }

        return $content;
    }

    /**
     * Get the global assets
     */
    public function getGlobalAssets(string $type = 'js'): array
    {
        return array_map(function ($path) {
            return $this->extendCssFilename($path);
        }, config('settings.KT_THEME_ASSETS.global.'.$type));
    }

    /**
     * Add multiple vendors to the page by name. Refer to settings KT_THEME_VENDORS
     */
    public function addVendors(array $vendors): array
    {
        foreach ($vendors as $value) {
            self::$vendorFiles[] = $value;
        }

        return array_unique(self::$vendorFiles);
    }

    /**
     * Add single vendor to the page by name. Refer to settings KT_THEME_VENDORS
     */
    public function addVendor(string $vendor): void
    {
        self::$vendorFiles[] = $vendor;
    }

    /**
     * Add custom javascript file to the page
     */
    public function addJavascriptFile(string $file): void
    {
        self::$javascriptFiles[] = $file;
    }

    /**
     * Add custom CSS file to the page
     */
    public function addCssFile(string $file): void
    {
        self::$cssFiles[] = $file;
    }

    /**
     * Get vendor files from settings. Refer to settings KT_THEME_VENDORS
     */
    public function getVendors(string $type): array
    {
        $files = [];
        foreach (self::$vendorFiles as $vendor) {
            $vendors = config('settings.KT_THEME_VENDORS.'.$vendor);
            if (isset($vendors[$type])) {
                foreach ($vendors[$type] as $path) {
                    $files[] = $path;
                }
            }
        }

        return array_unique($files);
    }

    /**
     * Get custom js files from the settings
     */
    public function getCustomJs(): array
    {
        return self::$javascriptFiles;
    }

    /**
     * Get custom css files from the settings
     */
    public function getCustomCss(): array
    {
        return self::$cssFiles;
    }

    /**
     * Get HTML attribute based on the scope
     */
    public function getHtmlAttribute(string $scope, string $attribute): array
    {
        return self::$htmlAttributes[$scope][$attribute] ?? [];
    }

    /**
     * Get icon markup
     */
    public function getIcon(string $name, string $class = '', string $type = '', string $tag = 'span'): string
    {
        $type = config('settings.KT_THEME_ICONS', 'duotone');

        if ($type === 'duotone') {
            $icons = cache()->remember('duotone-icons', 3600, function () {
                return json_decode(file_get_contents(public_path('icons.json')), true);
            });

            $pathsNumber = data_get($icons, 'duotone-paths.'.$name, 0);

            $output = '<'.$tag.' class="ki-'.$type.' ki-'.$name.(! empty($class) ? ' '.$class : '').'">';

            for ($i = 0; $i < $pathsNumber; $i++) {
                $output .= '<'.$tag.' class="path'.($i + 1).'"></'.$tag.'>';
            }

            $output .= '</'.$tag.'>';
        } else {
            $output = '<'.$tag.' class="ki-'.$type.' ki-'.$name.(! empty($class) ? ' '.$class : '').'"></'.$tag.'>';
        }

        return $output;
    }
}
