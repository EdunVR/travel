{{-- Responsive Table Component --}}
@props([
    'headers' => [],
    'responsive' => true,
    'striped' => true,
    'hoverable' => true
])

<div class="@if($responsive) overflow-x-auto @endif">
    <table class="min-w-full divide-y divide-gray-200">
        @if(!empty($headers))
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody class="bg-white divide-y divide-gray-200 @if($striped) divide-y divide-gray-200 @endif">
            {{ $slot }}
        </tbody>
    </table>
</div>

{{-- Usage:
<x-ui.table :headers="['Name', 'Email', 'Actions']">
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
        <td class="px-6 py-4 whitespace-nowrap">john@example.com</td>
        <td class="px-6 py-4 whitespace-nowrap">Actions</td>
    </tr>
</x-ui.table>
--}}