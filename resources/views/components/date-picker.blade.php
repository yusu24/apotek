@props([
    'placeholder' => 'Select date...',
])

<div class="w-full relative">
    <input 
        x-data="{
            value: @entangle($attributes->wire('model')),
            instance: undefined,
            _destroyed: false,
            init() {
                let self = this;
                this.instance = flatpickr(this.$refs.input, {
                    dateFormat: 'Y-m-d',
                    defaultDate: this.value,
                    disableMobile: true,
                    allowInput: false,
                    onChange: (selectedDates, dateStr, instance) => {
                        if (self._destroyed || self._isProgrammatic) return;
                        self.value = dateStr;
                    }
                });

                this._isProgrammatic = false;

                this.$watch('value', value => {
                    if (self._destroyed || value === undefined || !self.instance) return;
                    
                    self._isProgrammatic = true;
                    if (self.instance.selectedDates[0] !== undefined) {
                        let currentSelecteDate = self.instance.formatDate(self.instance.selectedDates[0], 'Y-m-d');
                        if (currentSelecteDate !== value) {
                            self.instance.setDate(value);
                        }
                    } else if(value) {
                         self.instance.setDate(value);
                    } else {
                         self.instance.clear();
                    }
                    self._isProgrammatic = false;
                });
            },
            destroy() {
                this._destroyed = true;
                if (this.instance) {
                    this.instance.destroy();
                    this.instance = undefined;
                }
            }
        }"
        wire:ignore
        x-ref="input"
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full block']) }}
    />
</div>
