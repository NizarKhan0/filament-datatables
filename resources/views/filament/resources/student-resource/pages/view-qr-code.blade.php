<x-filament-panels::page
@class([
    'fi-resource-view-record-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    'fi-resource-record-' . $record->getKey(),
])
>
    {{-- {{ dd($record) }} --}}
<div class="flex flex-col items-center">
    <h1 class="text-3xl">{{ $record->name }}</h1>
    <br>
    {!! QrCode::size(200)->generate($record->name) !!}
</div>

</x-filament-panels::page>
