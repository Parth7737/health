<div class="form-check form-switch form-check-inline">
    <input 
        class="form-check-input switch-primary check-size" 
        type="checkbox" 
        role="switch"
        name="{{ $name }}" 
        id="{{ $id ?? $name }}"
        value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }}
    >
</div>
