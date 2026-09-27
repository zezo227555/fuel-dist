@if (session('success'))
    <div class="mb-6">
        <x-ui.alert type="success" :title="__('Done')" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    </div>
@endif

@if (session('error'))
    <div class="mb-6">
        <x-ui.alert type="error" :title="__('Error')" dismissible>
            {{ session('error') }}
        </x-ui.alert>
    </div>
@endif

@if (session('warning'))
    <div class="mb-6">
        <x-ui.alert type="warning" :title="__('Notice')" dismissible>
            {{ session('warning') }}
        </x-ui.alert>
    </div>
@endif

@if (session('info'))
    <div class="mb-6">
        <x-ui.alert type="info" dismissible>
            {{ session('info') }}
        </x-ui.alert>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6">
        <x-ui.alert type="error" :title="__('Please fix these problems')">
            <ul class="list-disc space-y-1 ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    </div>
@endif
