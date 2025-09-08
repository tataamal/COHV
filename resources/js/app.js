import './bootstrap';

// 1. Kumpulkan semua import di bagian atas
import { library, dom } from '@fortawesome/fontawesome-svg-core';
import { faBoxOpen, faPowerOff, faUser } from '@fortawesome/free-solid-svg-icons';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import '@lottiefiles/dotlottie-wc';
import { Calendar } from 'fullcalendar';
import dayGridPlugin from '@fullawesome/daygrid';

// 2. Gunakan SATU event listener untuk semua inisialisasi yang butuh DOM
document.addEventListener('DOMContentLoaded', function() {
    
    // Inisialisasi Font Awesome
    library.add(faBoxOpen, faPowerOff, faUser);
    dom.watch();

    // Inisialisasi AlpineJS
    Alpine.plugin(collapse);
    window.Alpine = Alpine;
    Alpine.start();
    
    // Inisialisasi FullCalendar
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin],
            initialView: 'dayGridMonth',
        });
        calendar.render();
    }
});