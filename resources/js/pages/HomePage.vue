<template>
    <section class="max-w-6xl mx-auto py-12 px-4">
        <!-- Welcoming Hero Card -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-zinc-900 via-zinc-950 to-indigo-950 text-white p-8 md:p-12 shadow-2xl mb-12 border border-zinc-800">
            <!-- Decorative blur shapes -->
            <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 uppercase tracking-widest mb-4">
                    🏥 HIS - Hospital Information System
                </span>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight mb-4 leading-tight">
                    Infraestructura Digital <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">
                        Hospital General San Marcos
                    </span>
                </h1>
                <p class="text-zinc-400 text-base md:text-lg mb-8 leading-relaxed">
                    Acceda a expedientes clínicos, controle el historial de visitas, administre citas y centralice la atención de pacientes de forma ágil y multitenant.
                </p>

                <div class="flex flex-wrap gap-4">
                    <template v-if="auth.token">
                        <router-link 
                            :to="{ name: 'patients' }"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200 flex items-center gap-2"
                        >
                            Ir al Directorio de Pacientes
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </router-link>
                    </template>
                    <template v-else>
                        <router-link 
                            :to="{ name: 'login' }"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200 flex items-center gap-2"
                        >
                            Iniciar Sesión en el Sistema
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </router-link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Patient Module Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/60 rounded-2xl p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xl mb-4">
                    👥
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Expedientes de Pacientes</h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed mb-4">
                    Ver registros clínicos de atenciones, buscar por DPI, nombres o apellidos y gestionar datos médicos de contacto.
                </p>
                <router-link 
                    v-if="auth.token"
                    :to="{ name: 'patients' }" 
                    class="text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline inline-flex items-center gap-1"
                >
                    Acceder al módulo &rarr;
                </router-link>
                <span v-else class="text-zinc-400 text-xs font-bold block">
                    Inicie sesión para acceder &rarr;
                </span>
            </div>

            <!-- Health History Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/60 rounded-2xl p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/30 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-xl mb-4">
                    📜
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Historial de Visitas</h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed mb-4">
                    Visualización de atenciones anteriores de médicos y enfermeras asignadas a cada paciente en tiempo real.
                </p>
                <router-link 
                    v-if="auth.token"
                    :to="{ name: 'patients' }" 
                    class="text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline inline-flex items-center gap-1"
                >
                    Acceder al módulo &rarr;
                </router-link>
                <span v-else class="text-zinc-400 text-xs font-bold block">
                    Inicie sesión para acceder &rarr;
                </span>
            </div>

            <!-- Multitenancy Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/60 rounded-2xl p-6 shadow-md transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-xl mb-4">
                    🛡️
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Aislamiento por Tenant</h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed mb-4">
                    Los datos de pacientes están completamente aislados y seguros utilizando claves de tenant por cabecera de red.
                </p>
                <span class="text-zinc-400 text-xs font-bold block">
                    Seguridad integrada activa
                </span>
            </div>
        </div>
    </section>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
</script>
