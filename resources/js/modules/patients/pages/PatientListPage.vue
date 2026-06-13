<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-3">
                    <span class="p-2 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-500/20">👥</span>
                    Directorio de Pacientes
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                    Gestione el registro, datos generales e historial clínico.
                </p>
            </div>
            
            <button 
                @click="showModal = true"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200 flex items-center gap-2 text-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Registrar Paciente
            </button>
        </div>

        <!-- Quick Stats Banner -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="p-6 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 shadow-md">
                <span class="text-xs text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider block">Pacientes Registrados</span>
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400 mt-1 block">{{ store.patients.length }}</span>
            </div>
            <div class="p-6 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 shadow-md">
                <span class="text-xs text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider block">Tipos de Sangre</span>
                <span class="text-3xl font-black text-teal-600 dark:text-teal-400 mt-1 block">O+, A+, B-, AB+</span>
            </div>
            <div class="p-6 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 shadow-md">
                <span class="text-xs text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider block">Soporte Multitenant</span>
                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mt-2 block truncate">Activo</span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <input 
                    v-model="search"
                    type="text" 
                    placeholder="Buscar paciente por nombre, apellido, DPI..."
                    class="w-full pl-10 pr-4 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-white/80 dark:bg-zinc-900/80 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-zinc-800 dark:text-zinc-200"
                >
                <div class="absolute left-3.5 top-3.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.636Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="store.loading" class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent mb-4"></div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm font-semibold">Cargando directorio de pacientes...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="store.error" class="p-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/30 rounded-xl text-rose-700 dark:text-rose-400 mb-6 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <p class="text-sm font-medium">{{ store.error }}</p>
        </div>

        <!-- Grid of Patients -->
        <div v-else-if="filteredPatients.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="patient in filteredPatients" 
                :key="patient.id"
                class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-md hover:shadow-xl hover:translate-y-[-2px] transition-all duration-300 flex flex-col justify-between group relative overflow-hidden"
            >
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-lg group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                            {{ patient.first_name[0] }}{{ patient.last_name[0] }}
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-white leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ patient.first_name }} {{ patient.last_name }}
                            </h3>
                            <span class="text-xs text-zinc-400 font-mono">DPI: {{ patient.dpi || 'N/R' }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400 mb-6">
                        <div class="flex justify-between">
                            <span>F. Nacimiento:</span>
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ formatDate(patient.birth_date) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tipo Sangre:</span>
                            <span class="font-medium text-rose-600 dark:text-rose-400">🩸 {{ patient.blood_type || 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Alergias:</span>
                            <span class="font-medium text-zinc-800 dark:text-zinc-200 truncate max-w-[150px]">{{ patient.allergies || 'Ninguna conocida' }}</span>
                        </div>
                    </div>
                </div>

                <router-link 
                    :to="{ name: 'patient-detail', params: { id: patient.id } }"
                    class="w-full text-center py-2 bg-zinc-100 hover:bg-blue-600 dark:bg-zinc-800 dark:hover:bg-blue-600 text-zinc-700 hover:text-white dark:text-zinc-300 dark:hover:text-white font-bold rounded-xl text-xs transition-all duration-200 block"
                >
                    Ver Expediente Completo
                </router-link>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 p-6 shadow-md max-w-md mx-auto">
            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Sin pacientes registrados</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-2 text-sm leading-relaxed">
                No hay pacientes que coincidan con los filtros de búsqueda o el tenant indicado. Puede registrar uno nuevo.
            </p>
        </div>

        <!-- Create Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-900">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Registrar Nuevo Paciente</h3>
                    <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleCreate" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Nombre</label>
                            <input v-model="form.first_name" type="text" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Apellidos</label>
                            <input v-model="form.last_name" type="text" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">DPI / DNI</label>
                            <input v-model="form.dpi" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">F. Nacimiento</label>
                            <input v-model="form.birth_date" type="date" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Teléfono</label>
                            <input v-model="form.phone" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Género</label>
                            <select v-model="form.gender" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro / No especifica</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Tipo de Sangre</label>
                            <select v-model="form.blood_type" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Dirección</label>
                            <input v-model="form.address" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Alergias / Condiciones Especiales</label>
                        <textarea v-model="form.allergies" rows="2" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200"></textarea>
                    </div>

                    <div v-if="modalError" class="text-rose-600 text-sm font-medium mt-1">
                        {{ modalError }}
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-sm font-semibold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="submitting" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            {{ submitting ? 'Guardando...' : 'Guardar Paciente' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePatientStore } from '@/stores/patients';

const store = usePatientStore();
const search = ref('');
const showModal = ref(false);
const submitting = ref(false);
const modalError = ref('');

const form = ref({
    first_name: '',
    last_name: '',
    dpi: '',
    birth_date: '',
    phone: '',
    gender: 'Masculino',
    blood_type: 'O+',
    address: '',
    allergies: '',
});

const filteredPatients = computed(() => {
    return store.patients.filter(p => {
        const query = search.value.toLowerCase();
        const fullName = `${p.first_name} ${p.last_name}`.toLowerCase();
        return fullName.includes(query) || (p.dpi && p.dpi.includes(query));
    });
});

onMounted(() => {
    store.fetchPatients();
});

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-GT', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        timeZone: 'UTC'
    });
}

async function handleCreate() {
    submitting.value = true;
    modalError.value = '';
    try {
        await store.createPatient({ ...form.value });
        showModal.value = false;
        // reset form
        form.value = {
            first_name: '',
            last_name: '',
            dpi: '',
            birth_date: '',
            phone: '',
            gender: 'Masculino',
            blood_type: 'O+',
            address: '',
            allergies: '',
        };
    } catch (err) {
        modalError.value = err.response?.data?.message ?? 'Error al guardar el paciente';
    } finally {
        submitting.value = false;
    }
}
</script>
