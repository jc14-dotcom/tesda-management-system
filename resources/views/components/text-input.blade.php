@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-input']) }} @if($attributes->get('type') === 'email') oninput="this.value = this.value.toLowerCase()" @endif>
