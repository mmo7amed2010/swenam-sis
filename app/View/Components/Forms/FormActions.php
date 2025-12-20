<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

/**
 * Form Actions Component
 *
 * Standard Cancel/Submit button group for forms.
 */
class FormActions extends Component
{
    /**
     * Route for cancel button
     */
    public string $cancelRoute;

    /**
     * Text for submit button
     */
    public string $submitText;

    /**
     * Create a new component instance.
     *
     * @param  string  $cancelRoute  Route name or URL for cancel button
     * @param  string  $submitText  Text for submit button (default: 'Save')
     */
    public function __construct(string $cancelRoute, string $submitText = 'Save')
    {
        $this->cancelRoute = $cancelRoute;
        $this->submitText = $submitText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.forms.form-actions');
    }
}
