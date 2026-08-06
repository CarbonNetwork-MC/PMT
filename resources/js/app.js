import './bootstrap';

// Toaster
import '../../vendor/masmerise/livewire-toaster/resources/js';

// Flowbite
import 'flowbite';

// Livewire SortableJS
import '@wotz/livewire-sortablejs';

// Fancybox
import { Fancybox } from "@fancyapps/ui/dist/fancybox/";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

function initializeFancybox() {
    Fancybox.bind('[data-fancybox]', {
        animated: true,
        dragToClose: true,
    });
}

document.addEventListener('DOMContentLoaded', initializeFancybox);
document.addEventListener('livewire:navigated', initializeFancybox);