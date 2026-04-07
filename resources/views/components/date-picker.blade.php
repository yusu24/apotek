@props([
    'placeholder' => 'Select date...',
])

<div wire:ignore class="w-full relative">
    <input 
        x-data="{
            value: @entangle($attributes->wire('model')),
            instance: undefined,
            init() {
                this.instance = flatpickr(this.$refs.input, {
                    dateFormat: 'Y-m-d',
                    defaultDate: this.value,
                    onChange: (selectedDates, dateStr, instance) => {
                        this.value = dateStr;
                    }
                });

                this.$watch('value', value => {
                    if (this.instance.selectedDates[0] !== undefined) {
                        let currentSelecteDate = this.instance.formatDate(this.instance.selectedDates[0], 'Y-m-d');
                        if (currentSelecteDate !== value) {
                            this.instance.setDate(value);
                        }
                    } else if(value) {
                         this.instance.setDate(value);
                    } else {
                         this.instance.clear();
                    }
                });
            }
        }"
        x-ref="input"
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full block']) }}
    />
</div>
