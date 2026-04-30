<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ToggleSwitch extends Component
{
    public string $name;
    public mixed $value;
    public bool $isChecked;
    public ?string $id;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $value = 1, $checked = false, $id = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->isChecked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.toggle-switch');
    }
}
