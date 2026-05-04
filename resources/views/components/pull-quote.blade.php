@props(['attribution' => null])

<figure class="pull-quote">
  <blockquote class="pull-quote__text font-serif">
    {{ $slot }}
  </blockquote>
  @if ($attribution)
    <figcaption class="pull-quote__attribution">
      {{ $attribution }}
    </figcaption>
  @endif
</figure>
