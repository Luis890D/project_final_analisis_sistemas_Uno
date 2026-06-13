<template>
    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-xl relative overflow-hidden transition-all duration-300">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                    Historial de Accesos y Consultas
                </h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Registro cronológico de atenciones médicas y citas.
                </p>
            </div>
            
            <!-- Type Filters -->
            <div class="flex flex-wrap gap-2">
                <button 
                    @click="filterType = 'all'" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                    :class="filterType === 'all' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                >
                    Todos
                </button>
                <button 
                    @click="filterType = 'Consulta'" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                    :class="filterType === 'Consulta' ? 'bg-teal-600 text-white shadow-md shadow-teal-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                >
                    Consultas
                </button>
                <button 
                    @click="filterType = 'Cita'" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                    :class="filterType === 'Cita' ? 'bg-amber-600 text-white shadow-md shadow-amber-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                >
                    Citas
                </button>
                <button 
                    @click="filterType = 'Emergencia'" 
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200"
                    :class="filterType === 'Emergencia' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                >
                    Emergencias
                </button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-4">
            <div class="relative">
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Buscar en descripción, médico..."
                    class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50/50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-zinc-800 dark:text-zinc-200"
                >
                <div class="absolute left-3.5 top-2.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.636Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table / Timeline list -->
        <div v-if="filteredAccesses.length > 0" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-zinc-100 dark:border-zinc-800">
                        <th class="pb-3 text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Fecha / Hora</th>
                        <th class="pb-3 text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Tipo</th>
                        <th class="pb-3 text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Detalle del Registro</th>
                        <th class="pb-3 text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Médico Responsable</th>
                        <th class="pb-3 text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    <tr v-for="access in filteredAccesses" :key="access.id" class="hover:bg-zinc-50/30 dark:hover:bg-zinc-800/10 transition-colors duration-150">
                        <!-- Date -->
                        <td class="py-4 text-sm font-mono text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            {{ formatDateTime(access.access_date) }}
                        </td>
                        <!-- Type badge -->
                        <td class="py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" :class="getTypeClass(access.type)">
                                {{ access.type }}
                            </span>
                        </td>
                        <!-- Description -->
                        <td class="py-4 pr-4 text-sm text-zinc-700 dark:text-zinc-300 max-w-xs md:max-w-md">
                            <p class="line-clamp-2 hover:line-clamp-none transition-all duration-300 cursor-pointer" :title="access.description">
                                {{ access.description }}
                            </p>
                        </td>
                        <!-- Doctor -->
                        <td class="py-4 text-sm font-medium text-zinc-800 dark:text-zinc-200 whitespace-nowrap">
                            {{ access.doctor_in_charge || 'No especificado' }}
                        </td>
                        <!-- Status badge -->
                        <td class="py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(access.status)">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getStatusDotClass(access.status)"></span>
                                {{ formatStatus(access.status) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-12 text-center">
            <div class="w-16 h-16 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center text-zinc-400 mb-4 border border-zinc-100 dark:border-zinc-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
            <h4 class="text-base font-bold text-zinc-800 dark:text-zinc-200">
                No se encontraron registros
            </h4>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 max-w-xs">
                Intente cambiando el filtro de búsqueda o el tipo de atención médica seleccionado.
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    accesses: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const filterType = ref('all');
const searchQuery = ref('');

const filteredAccesses = computed(() => {
    return props.accesses.filter(access => {
        const matchesType = filterType.value === 'all' || access.type === filterType.value;
        const matchesSearch = !searchQuery.value || 
            (access.description && access.description.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (access.doctor_in_charge && access.doctor_in_charge.toLowerCase().includes(searchQuery.value.toLowerCase()));
        return matchesType && matchesSearch;
    });
});

function formatDateTime(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleString('es-GT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getTypeClass(type) {
    switch (type) {
        case 'Consulta':
            return 'bg-teal-50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-400 border border-teal-100 dark:border-teal-900/30';
        case 'Cita':
            return 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30';
        case 'Emergencia':
            return 'bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30';
        default:
            return 'bg-zinc-50 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-800';
    }
}

function getStatusClass(status) {
    switch (status) {
        case 'completada':
            return 'bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900/30';
        case 'programada':
            return 'bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30';
        case 'cancelada':
            return 'bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/30';
        default:
            return 'bg-zinc-50 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300';
    }
}

function getStatusDotClass(status) {
    switch (status) {
        case 'completada': return 'bg-green-500';
        case 'programada': return 'bg-blue-500';
        case 'cancelada': return 'bg-red-500';
        default: return 'bg-zinc-400';
    }
}

function formatStatus(status) {
    if (!status) return '';
    return status.charAt(0).toUpperCase() + status.slice(1);
}
</script>
