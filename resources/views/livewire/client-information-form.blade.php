<div>
    <form wire:submit="create">
        {{ $this->form }}

        <div class="flex justify-center ">
            <x-filament::button type="submit" class="mt-4  text-white py-2 px-4 rounded mx-auto w-60">
                Submit
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
<script>

    function updateScreenSize() {
        const screenWidth = window.innerWidth;
        let screenSize = 'desktop';

        if (screenWidth < 769) {
            screenSize = 'mobile';
        } else if (screenWidth >= 769 && screenWidth < 1024) {
            screenSize = 'md';
        }
        @this.set('screenSize',screenSize);
    }

    // Normal document load
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            updateScreenSize(); // Trigger screen size detection after 200ms delay
        }, 50);
        {{--if (@js($has_data)) {--}}
        {{--    setTimeout(() => {--}}
        {{--        // Open the modal using Filament's modal manager--}}
        {{--        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'hasdata-modal' }}));--}}
        {{--    }, 200); // Delay by 200ms, adjust as necessary--}}
        {{--}--}}
    });

    window.addEventListener('resize', function(event) {
        updateScreenSize();
    }, true);

</script>
