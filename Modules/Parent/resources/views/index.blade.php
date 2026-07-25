<x-layouts::app :title="__(config('parent.name'))">
    <h1>Hello World</h1>

    <p>Module: {!! config('parent.name') !!}</p>
</x-layouts::app>