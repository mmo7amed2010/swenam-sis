<?php

/**
 * UI Configuration
 *
 * Centralized configuration for UI components, colors, sizes, and icons.
 * Provides consistent theming and easy customization across the application.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Color Variants
    |--------------------------------------------------------------------------
    |
    | Available Bootstrap color variants for buttons, badges, cards, etc.
    | These map to Bootstrap 5 color utility classes.
    |
    */

    'colors' => [
        'primary' => '#3b82f6',
        'secondary' => '#6c757d',
        'success' => '#50cd89',
        'danger' => '#f1416c',
        'warning' => '#ffc700',
        'info' => '#7239ea',
        'light' => '#f8f9fa',
        'dark' => '#212529',
    ],

    /*
    |--------------------------------------------------------------------------
    | Size Variants
    |--------------------------------------------------------------------------
    |
    | Standard size variants used across components (buttons, inputs, etc.)
    |
    */

    'sizes' => [
        'xs' => 'Extra Small',
        'sm' => 'Small',
        'md' => 'Medium',
        'lg' => 'Large',
        'xl' => 'Extra Large',
    ],

    /*
    |--------------------------------------------------------------------------
    | Icon Mappings
    |--------------------------------------------------------------------------
    |
    | Common action icons mapped to semantic names for easy reference.
    | Uses the getIcon() helper function.
    |
    */

    'icons' => [
        'add' => 'plus',
        'create' => 'plus',
        'edit' => 'pencil',
        'update' => 'pencil',
        'delete' => 'trash',
        'remove' => 'trash',
        'view' => 'eye',
        'show' => 'eye',
        'hide' => 'eye-slash',
        'search' => 'magnifier',
        'filter' => 'filter',
        'sort' => 'sort',
        'download' => 'download',
        'upload' => 'file-up',
        'export' => 'file-down',
        'import' => 'file-up',
        'print' => 'printer',
        'refresh' => 'arrows-rotate',
        'settings' => 'gear',
        'user' => 'profile-user',
        'users' => 'people',
        'course' => 'book',
        'courses' => 'book',
        'module' => 'file-text',
        'assignment' => 'clipboard',
        'grade' => 'award',
        'calendar' => 'calendar',
        'check' => 'check',
        'close' => 'cross',
        'info' => 'information',
        'warning' => 'exclamation-triangle',
        'error' => 'exclamation-circle',
        'success' => 'check-circle',
    ],

    /*
    |--------------------------------------------------------------------------
    | Component Defaults
    |--------------------------------------------------------------------------
    |
    | Default configuration values for various components
    |
    */

    'defaults' => [
        'button' => [
            'size' => 'sm',
            'color' => 'primary',
            'variant' => 'solid',
        ],
        'card' => [
            'flush' => true,
            'padding' => 'py-4',
        ],
        'modal' => [
            'centered' => true,
            'scrollable' => false,
            'size' => null, // null = default, or 'sm', 'lg', 'xl'
        ],
        'form' => [
            'validation' => true,
            'realtime' => false,
        ],
        'table' => [
            'striped' => false,
            'bordered' => false,
            'hover' => true,
            'responsive' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Empty State Defaults
    |--------------------------------------------------------------------------
    |
    | Default messages and icons for common empty states
    |
    */

    'empty_states' => [
        'courses' => [
            'icon' => 'book',
            'title' => 'No courses yet',
            'message' => 'Get started by creating your first course.',
        ],
        'students' => [
            'icon' => 'profile-user',
            'title' => 'No students enrolled',
            'message' => 'This course doesn\'t have any students yet.',
        ],
        'modules' => [
            'icon' => 'file-text',
            'title' => 'No modules yet',
            'message' => 'Start adding modules to organize your course content.',
        ],
        'assignments' => [
            'icon' => 'clipboard',
            'title' => 'No assignments',
            'message' => 'Create assignments to assess student learning.',
        ],
        'search' => [
            'icon' => 'magnifier',
            'title' => 'No results found',
            'message' => 'Try adjusting your search or filter criteria.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for tables and lists
    |
    */

    'pagination' => [
        'per_page' => 15,
        'per_page_options' => [10, 15, 25, 50, 100],
        'show_page_info' => true,
        'show_page_links' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Toast Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for toast notifications
    |
    */

    'toast' => [
        'duration' => 5000, // milliseconds
        'position' => 'top-right', // top-left, top-right, bottom-left, bottom-right
        'max_toasts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Loading Overlay Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for loading overlays
    |
    */

    'loading' => [
        'default_message' => 'Processing your request...',
        'spinner_color' => 'primary',
        'backdrop_blur' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Form Validation Settings
    |--------------------------------------------------------------------------
    |
    | Default validation messages and rules
    |
    */

    'validation' => [
        'realtime_debounce' => 300, // milliseconds
        'scroll_to_error' => true,
        'show_success_state' => true,
    ],

];
