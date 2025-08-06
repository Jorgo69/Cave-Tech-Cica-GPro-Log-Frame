{{-- Après vos champs fixes --}}
@if(!empty($dynamicFormFields))
    @foreach($dynamicFormFields as $section => $fields)
        <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">
                {{ ucfirst(str_replace('_', ' ', $section)) }}
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($fields as $field)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $field['question_text'] }}
                            @if($field['is_required']) <span class="text-red-500">*</span> @endif
                        </label>
                        
                        @if($field['input_type'] === 'textarea')
                            <textarea wire:model="dynamicFieldValues.{{ $field['field_name'] }}"
                                      class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                      rows="3"></textarea>
                        @elseif($field['input_type'] === 'select')
                            <select wire:model="dynamicFieldValues.{{ $field['field_name'] }}"
                                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Sélectionner une option</option>
                                @foreach(explode(',', $field['options']) as $option)
                                    <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $field['input_type'] }}"
                                   wire:model="dynamicFieldValues.{{ $field['field_name'] }}"
                                   class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                        @endif
                        
                        @error('dynamicFieldValues.' . $field['field_name'])
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <!-- Debug visuel -->
    <div class="p-4 bg-yellow-100 text-yellow-800 text-sm">
        Aucun champ dynamique configuré pour ce type de projet.
        Type sélectionné: {{ $selectedProjectTypeId ?? 'Aucun' }}
    </div>
@endif