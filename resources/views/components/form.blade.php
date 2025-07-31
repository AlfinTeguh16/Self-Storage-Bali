<div class="space-y-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    @if($type == 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="textarea textarea-bordered w-full {{ $errors->has($name) ? 'textarea-error' : '' }} focus:ring-primary focus:border-primary border rounded-lg p-1 border-gray-400 bg-gray-50"
        >{{ old($name, $value) }}</textarea>

    @elseif($type == 'checkbox' || $type == 'radio')
        <div class="space-y-2">
            @php
                $options = $options ?? [];
            @endphp
            @foreach($options as $option)
                <label class="flex items-center space-x-2 text-sm">
                    <input
                        type="{{ $type }}"
                        id="{{ $name }}_{{ $option['value'] }}"
                        name="{{ $type == 'checkbox' ? $name.'[]' : $name }}"
                        value="{{ $option['value'] }}"
                        {{ ($type=='checkbox' && in_array($option['value'], old($name, []))) || (old($name) == $option['value']) ? 'checked' : '' }}
                        class="checkbox checkbox-primary"
                        {{ $required ? 'required' : '' }}
                    >
                    <span>{{ $option['label'] }}</span>
                </label>
            @endforeach
        </div>

    @elseif($type == 'file')
        <div x-data="{ fileName: '' }">
            <input type="file" id="{{ $name }}" name="{{ $name }}"
                class="hidden"
                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                {{ $required ? 'required' : '' }}
            >

            <label for="{{ $name }}" class="flex items-center space-x-3 w-full px-4 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-200 focus:ring-2 focus:ring-primary focus:border-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 012-2h4a2 2 0 012 2v6h-2V2H8v6H6V2zm9 8V7a1 1 0 10-2 0v3H7V7a1 1 0 10-2 0v3a3 3 0 003 3h1v2a1 1 0 01-2 0v-1H7v1a3 3 0 006 0v-2h1a3 3 0 003-3z" clip-rule="evenodd"/>
                </svg>
                <span x-text="fileName || 'Pilih File'" class="truncate w-full"></span>
            </label>

            @if($required)
                <p class="text-xs text-gray-500 mt-1">File ini wajib diunggah <span class="text-red-500">*</span></p>
            @endif
        </div>

    @elseif($type == 'select')
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            class="select select-bordered w-full {{ $errors->has($name) ? 'select-error' : '' }} focus:ring-primary focus:border-primary border rounded-lg p-1 border-gray-400 bg-gray-50"
        >
            {{ $slot }}
        </select>

    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            class="input input-bordered w-full {{ $errors->has($name) ? 'input-error' : '' }} focus:ring-primary focus:border-primary border rounded-lg p-1 border-gray-400 bg-gray-50"
            {{ $required ? 'required' : '' }}
        >
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
