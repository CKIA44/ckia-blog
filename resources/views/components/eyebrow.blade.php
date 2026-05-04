@props(['color' => ''])

<div class="eyebrow {{ $color ? 'eyebrow--' . $color : '' }}">{{ $slot }}</div>
