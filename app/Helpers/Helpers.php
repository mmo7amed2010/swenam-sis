<?php

if (! function_exists('theme')) {
    function theme()
    {
        return app(App\Core\Theme::class);
    }
}

if (! function_exists('getName')) {
    /**
     * Get product name
     *
     * @return void
     */
    function getName()
    {
        return config('settings.KT_THEME');
    }
}

if (! function_exists('addHtmlAttribute')) {
    /**
     * Add HTML attributes by scope
     *
     *
     * @return void
     */
    function addHtmlAttribute($scope, $name, $value)
    {
        theme()->addHtmlAttribute($scope, $name, $value);
    }
}

if (! function_exists('addHtmlAttributes')) {
    /**
     * Add multiple HTML attributes by scope
     *
     *
     * @return void
     */
    function addHtmlAttributes($scope, $attributes)
    {
        theme()->addHtmlAttributes($scope, $attributes);
    }
}

if (! function_exists('addHtmlClass')) {
    /**
     * Add HTML class by scope
     *
     *
     * @return void
     */
    function addHtmlClass($scope, $value)
    {
        theme()->addHtmlClass($scope, $value);
    }
}

if (! function_exists('printHtmlAttributes')) {
    /**
     * Print HTML attributes for the HTML template
     *
     *
     * @return string
     */
    function printHtmlAttributes($scope)
    {
        return theme()->printHtmlAttributes($scope);
    }
}

if (! function_exists('printHtmlClasses')) {
    /**
     * Print HTML classes for the HTML template
     *
     *
     * @return string
     */
    function printHtmlClasses($scope, $full = true)
    {
        return theme()->printHtmlClasses($scope, $full);
    }
}

if (! function_exists('getSvgIcon')) {
    /**
     * Get SVG icon content
     *
     *
     * @return string
     */
    function getSvgIcon($path, $classNames = 'svg-icon', $folder = 'assets/media/icons/')
    {
        return theme()->getSvgIcon($path, $classNames, $folder);
    }
}

if (! function_exists('setModeSwitch')) {
    /**
     * Set dark mode enabled status
     *
     *
     * @return void
     */
    function setModeSwitch($flag)
    {
        theme()->setModeSwitch($flag);
    }
}

if (! function_exists('isModeSwitchEnabled')) {
    /**
     * Check dark mode status
     *
     * @return void
     */
    function isModeSwitchEnabled()
    {
        return theme()->isModeSwitchEnabled();
    }
}

if (! function_exists('setModeDefault')) {
    /**
     * Set the mode to dark or light
     *
     *
     * @return void
     */
    function setModeDefault($mode)
    {
        theme()->setModeDefault($mode);
    }
}

if (! function_exists('getModeDefault')) {
    /**
     * Get current mode
     *
     * @return void
     */
    function getModeDefault()
    {
        return theme()->getModeDefault();
    }
}

if (! function_exists('setDirection')) {
    /**
     * Set style direction
     *
     *
     * @return void
     */
    function setDirection($direction)
    {
        theme()->setDirection($direction);
    }
}

if (! function_exists('getDirection')) {
    /**
     * Get style direction
     *
     * @return void
     */
    function getDirection()
    {
        return theme()->getDirection();
    }
}

if (! function_exists('isRtlDirection')) {
    /**
     * Check if style direction is RTL
     *
     * @return void
     */
    function isRtlDirection()
    {
        return theme()->isRtlDirection();
    }
}

if (! function_exists('extendCssFilename')) {
    /**
     * Extend CSS file name with RTL or dark mode
     *
     *
     * @return void
     */
    function extendCssFilename($path)
    {
        return theme()->extendCssFilename($path);
    }
}

if (! function_exists('includeFavicon')) {
    /**
     * Include favicon from settings
     *
     * @return string
     */
    function includeFavicon()
    {
        return theme()->includeFavicon();
    }
}

if (! function_exists('includeFonts')) {
    /**
     * Include the fonts from settings
     *
     * @return string
     */
    function includeFonts()
    {
        return theme()->includeFonts();
    }
}

if (! function_exists('getGlobalAssets')) {
    /**
     * Get the global assets
     *
     *
     * @return array
     */
    function getGlobalAssets($type = 'js')
    {
        return theme()->getGlobalAssets($type);
    }
}

if (! function_exists('addVendors')) {
    /**
     * Add multiple vendors to the page by name. Refer to settings KT_THEME_VENDORS
     *
     *
     * @return void
     */
    function addVendors($vendors)
    {
        theme()->addVendors($vendors);
    }
}

if (! function_exists('addVendor')) {
    /**
     * Add single vendor to the page by name. Refer to settings KT_THEME_VENDORS
     *
     *
     * @return void
     */
    function addVendor($vendor)
    {
        theme()->addVendor($vendor);
    }
}

if (! function_exists('addJavascriptFile')) {
    /**
     * Add custom javascript file to the page
     *
     *
     * @return void
     */
    function addJavascriptFile($file)
    {
        theme()->addJavascriptFile($file);
    }
}

if (! function_exists('addCssFile')) {
    /**
     * Add custom CSS file to the page
     *
     *
     * @return void
     */
    function addCssFile($file)
    {
        theme()->addCssFile($file);
    }
}

if (! function_exists('getVendors')) {
    /**
     * Get vendor files from settings. Refer to settings KT_THEME_VENDORS
     *
     *
     * @return array
     */
    function getVendors($type)
    {
        return theme()->getVendors($type);
    }
}

if (! function_exists('getCustomJs')) {
    /**
     * Get custom js files from the settings
     *
     * @return array
     */
    function getCustomJs()
    {
        return theme()->getCustomJs();
    }
}

if (! function_exists('getCustomCss')) {
    /**
     * Get custom css files from the settings
     *
     * @return array
     */
    function getCustomCss()
    {
        return theme()->getCustomCss();
    }
}

if (! function_exists('getHtmlAttribute')) {
    /**
     * Get HTML attribute based on the scope
     *
     *
     * @return array
     */
    function getHtmlAttribute($scope, $attribute)
    {
        return theme()->getHtmlAttribute($scope, $attribute);
    }
}

if (! function_exists('isUrl')) {
    /**
     * Get HTML attribute based on the scope
     *
     *
     * @return mixed
     */
    function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}

if (! function_exists('image')) {
    /**
     * Get image url by path
     *
     *
     * @return string
     */
    function image($path)
    {
        return asset('assets/media/'.$path);
    }
}

if (! function_exists('getIcon')) {
    /**
     * Get icon
     *
     * @param  $path
     * @return string
     */
    function getIcon($name, $class = '', $type = '', $tag = 'span')
    {
        return theme()->getIcon($name, $class, $type, $tag);
    }
}
