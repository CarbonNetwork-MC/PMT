<div wire:key="column-{{ $this->column->id }}" class="bg-gray-200 dark:bg-gray-900 p-2 rounded-md md:h-[75vh] 3xl:h-[85vh] sortable-column">
    {{-- Header --}}
    <div class="flex justify-between bg-white dark:bg-slate-700 rounded-md px-2 py-1">
        {{-- Column name + Number of cards --}}
        <div class="flex gap-x-2 items-center">
            <p class="font-bold text-white bg-{{ $this->column->color->name }}-{{ $this->column->color->background_color }} px-1.5 rounded-md">
                {{ $this->column->cards->count() }}
            </p>
            <h2 class="font-bold text-sm text-{{ $this->column->color->name }}-{{ $this->column->color->text_color }}">{{ $this->column->name }}</h2>
        </div>
        {{-- Add card button --}}
        <div class="flex justify-end">
            <i class="fi fi-rr-plus text-gray-800 dark:text-gray-200 me-1 cursor-pointer" wire:click="addCard('{{ $this->column->id }}')"></i>
        </div>
    </div>

    {{-- Cards --}}
    <div 
        class="mt-2 flex flex-col gap-y-2 min-h-[100px]"
        wire:sortable-group.item-group="{{ $this->column->id }}"
        wire:sortable-group.options="{ animation: 100 }"
    >
        @foreach ($this->cards as $card)
            <div 
                wire:key="card-{{ $card->id }}"
                wire:sortable-group.item="{{ $card->id }}"
            >
                <livewire:components.board.card 
                    :card="$card"
                    :users="$users"
                    wire:key="card-inner-{{ $card->id }}" 
                />
            </div>
        @endforeach
    </div>
</div>