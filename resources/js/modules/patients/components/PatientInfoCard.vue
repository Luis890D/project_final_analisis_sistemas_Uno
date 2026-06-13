<template>
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-xl relative overflow-hidden transition-all duration-300 hover:shadow-2xl">
        <!-- Accent Glow background decoration -->
        <div class="absolute -top-20 -right-20 w-48 h-48 bg-blue-500/10 dark:bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
            <div class="flex items-center gap-4">
                <!-- Avatar block -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white leading-tight">
                        {{ patient.first_name }} {{ patient.last_name }}
                    </h2>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                        DPI/DNI: <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ patient.dpi || 'No registrado' }}</span>
                    </p>
                </div>
            </div>

            <!-- Blood type badge -->
            <div class="flex flex-col items-end">
                <span class="text-xs uppercase text-zinc-400 dark:text-zinc-500 font-bold tracking-wider">Tipo Sangre</span>
                <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-full text-base font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30 mt-1">
                    🩸 {{ patient.blood_type || 'N/A' }}
                </span>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Fecha Nacimiento / Edad -->
            <div class="flex items-center gap-3 p-3 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-100/50 dark:border-zinc-800/30">
                <div class="text-blue-500 dark:text-blue-400 p-2 bg-blue-50 dark:bg-blue-950/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-zinc-400 block">Fecha Nacimiento (Edad)</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                        {{ formatDate(patient.birth_date) }} ({{ calculateAge(patient.birth_date) }} años)
                    </span>
                </div>
            </div>

            <!-- Genero -->
            <div class="flex items-center gap-3 p-3 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-100/50 dark:border-zinc-800/30">
                <div class="text-indigo-500 dark:text-indigo-400 p-2 bg-indigo-50 dark:bg-indigo-950/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-zinc-400 block">Género</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                        {{ patient.gender || 'No especificado' }}
                    </span>
                </div>
            </div>

            <!-- Telefono -->
            <div class="flex items-center gap-3 p-3 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-100/50 dark:border-zinc-800/30">
                <div class="text-emerald-500 dark:text-emerald-400 p-2 bg-emerald-50 dark:bg-emerald-950/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.184-4.162-6.985-6.985l1.293-.97c.362-.271.528-.732.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-zinc-400 block">Teléfono / Celular</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                        {{ patient.phone || 'No registrado' }}
                    </span>
                </div>
            </div>

            <!-- Direccion -->
            <div class="flex items-center gap-3 p-3 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/30 border border-zinc-100/50 dark:border-zinc-800/30">
                <div class="text-amber-500 dark:text-amber-400 p-2 bg-amber-50 dark:bg-amber-950/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-zinc-400 block">Dirección</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate max-w-[200px] inline-block" :title="patient.address">
                        {{ patient.address || 'No registrada' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Allergies Warn card -->
        <div class="mt-4 p-4 rounded-xl border transition-all duration-300" 
             :class="hasAllergies ? 'bg-red-50/50 dark:bg-red-950/20 border-red-200/50 dark:border-red-900/30' : 'bg-green-50/30 dark:bg-green-950/10 border-green-100/50 dark:border-green-900/10'">
            <div class="flex items-start gap-3">
                <div class="p-1.5 rounded-lg mt-0.5" :class="hasAllergies ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'">
                    <svg v-if="hasAllergies" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider" :class="hasAllergies ? 'text-red-800 dark:text-red-400' : 'text-green-800 dark:text-green-400'">
                        Alergias e Intolerancias
                    </h4>
                    <p class="text-sm font-medium mt-1" :class="hasAllergies ? 'text-red-700 dark:text-red-300' : 'text-green-700 dark:text-green-400'">
                        {{ patient.allergies || 'Sin alergias conocidas registradas.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Optional User account detail link -->
        <div v-if="patient.user" class="mt-4 p-4 rounded-xl bg-blue-50/30 dark:bg-blue-950/10 border border-blue-100/50 dark:border-blue-900/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-blue-500 dark:text-blue-400 p-2 bg-blue-50 dark:bg-blue-950/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751A11.977 11.977 0 0 1 12 5.714Z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-blue-800 dark:text-blue-400 font-bold uppercase tracking-wider block">Usuario Vinculado</span>
                        <span class="text-sm text-zinc-700 dark:text-zinc-300 font-medium">
                            {{ patient.user.email }} ({{ patient.user.name }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    patient: {
        type: Object,
        required: true,
    },
});

const hasAllergies = computed(() => {
    return props.patient.allergies && 
        props.patient.allergies.toLowerCase() !== 'ninguna' && 
        props.patient.allergies.toLowerCase() !== 'ninguna conocida';
});

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-GT', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC'
    });
}

function calculateAge(birthDateStr) {
    if (!birthDateStr) return 0;
    const birthDate = new Date(birthDateStr);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}
</script>
