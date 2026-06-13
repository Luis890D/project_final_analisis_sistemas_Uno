<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Breadcrumb / Navigation -->
        <div class="flex items-center gap-2 mb-6 text-sm text-zinc-500 dark:text-zinc-400">
            <router-link :to="{ name: 'patients' }" class="hover:text-blue-600 transition-colors">
                Directorio
            </router-link>
            <span>/</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                Expediente del Paciente
            </span>
        </div>

        <!-- Loading State -->
        <div v-if="store.loading" class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-600 border-t-transparent mb-4"></div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm font-semibold">Cargando expediente...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="store.error" class="p-6 bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/30 rounded-2xl text-rose-700 dark:text-rose-400 text-center max-w-md mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 mx-auto mb-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <h3 class="font-bold text-lg">Error al cargar</h3>
            <p class="text-sm mt-1 mb-4">{{ store.error }}</p>
            <router-link :to="{ name: 'patients' }" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-semibold inline-block transition-colors">
                Volver al Directorio
            </router-link>
        </div>

        <!-- Main Content -->
        <div v-else-if="store.currentPatient" class="space-y-6">
            <!-- Header Block -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md p-6 rounded-2xl border border-zinc-200/50 dark:border-zinc-800/50 shadow-sm">
                <div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest block">HISTORIAL CLÍNICO INDIVIDUAL</span>
                    <h1 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight mt-1">
                        Expediente #{{ store.currentPatient.id }}
                    </h1>
                </div>
                <div class="flex gap-2">
                    <button 
                        @click="showEditModal = true"
                        class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold rounded-xl border border-zinc-200/50 dark:border-zinc-800/50 transition-colors"
                    >
                        Editar Datos Generales
                    </button>
                    <button 
                        @click="exportHistory"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all duration-200"
                    >
                        Imprimir Expediente
                    </button>
                </div>
            </div>

            <!-- Two-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Left: Patient Info Card (33%) -->
                <div class="lg:col-span-1">
                    <PatientInfoCard :patient="store.currentPatient" />
                </div>

                <!-- Right: Accesses & Medical History (67%) -->
                <div class="lg:col-span-2">
                    <PatientAccessTable :accesses="store.currentPatient.accesses || []" />
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-900">
                    <h3 class="font-bold text-zinc-900 dark:text-white text-lg">Editar Información del Paciente</h3>
                    <button @click="showEditModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleUpdate" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Nombre</label>
                            <input v-model="editForm.first_name" type="text" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Apellidos</label>
                            <input v-model="editForm.last_name" type="text" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">DPI / DNI</label>
                            <input v-model="editForm.dpi" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">F. Nacimiento</label>
                            <input v-model="editForm.birth_date" type="date" required class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Teléfono</label>
                            <input v-model="editForm.phone" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Género</label>
                            <select v-model="editForm.gender" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro / No especifica</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Tipo de Sangre</label>
                            <select v-model="editForm.blood_type" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
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
                            <input v-model="editForm.address" type="text" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">Alergias / Condiciones Especiales</label>
                        <textarea v-model="editForm.allergies" rows="2" class="w-full px-3.5 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 text-sm focus:outline-none focus:border-blue-500 text-zinc-800 dark:text-zinc-200"></textarea>
                    </div>

                    <div v-if="editError" class="text-rose-600 text-sm font-medium mt-1">
                        {{ editError }}
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 text-sm font-semibold rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="submitting" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                            {{ submitting ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePatientStore } from '@/stores/patients';
import PatientInfoCard from '../components/PatientInfoCard.vue';
import PatientAccessTable from '../components/PatientAccessTable.vue';

const route = useRoute();
const store = usePatientStore();

const showEditModal = ref(false);
const submitting = ref(false);
const editError = ref('');

const editForm = ref({
    first_name: '',
    last_name: '',
    dpi: '',
    birth_date: '',
    phone: '',
    gender: '',
    blood_type: '',
    address: '',
    allergies: '',
});

onMounted(() => {
    store.fetchPatient(route.params.id);
});

watch(() => store.currentPatient, (newPatient) => {
    if (newPatient) {
        editForm.value = {
            first_name: newPatient.first_name,
            last_name: newPatient.last_name,
            dpi: newPatient.dpi || '',
            birth_date: newPatient.birth_date,
            phone: newPatient.phone || '',
            gender: newPatient.gender || 'Masculino',
            blood_type: newPatient.blood_type || 'O+',
            address: newPatient.address || '',
            allergies: newPatient.allergies || '',
        };
    }
}, { immediate: true });

async function handleUpdate() {
    submitting.value = true;
    editError.value = '';
    try {
        await store.updatePatient(store.currentPatient.id, { ...editForm.value });
        showEditModal.value = false;
    } catch (err) {
        editError.value = err.response?.data?.message ?? 'Error al actualizar la información del paciente';
    } finally {
        submitting.value = false;
    }
}

function exportHistory() {
    window.print();
}
</script>
