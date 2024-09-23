<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
{{--    {{$record}}--}}

    @if($record)
        <div wire:ignore>
            <iframe id="pdfIframe" src="{{ route('document.stream', ['id' => $record->id??0]) }}" width="100%" height="600px"></iframe>
        </div>
    @endif


</div>


