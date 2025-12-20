<?php

namespace App\View\Components\Forms;

use Illuminate\View\Component;

/**
 * Card Section Component
 *
 * Standard card wrapper with header and body for form sections.
 */
class CardSection extends Component
{
    /**
     * Card title displayed in header
     */
    public string $title;

    /**
     * Additional CSS classes for the card
     */
    public string $class;

    /**
     * Create a new component instance.
     *
     * @param  string  $title  Card title for header
     * @param  string  $class  Additional CSS classes (optional)
     */
    public function __construct(string $title, string $class = '')
    {
        $this->title = $title;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.forms.card-section');
    }
}
