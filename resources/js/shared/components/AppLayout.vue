<template>
    <div class="layout">
        <header class="layout__header">
            <div class="layout__brand">
                Hospital HIS
            </div>
            <nav class="layout__nav">
                <router-link class="layout__link" to="/">
                    Inicio
                </router-link>
                <template v-if="auth.token">
                    <router-link class="layout__link" to="/patients">
                        Pacientes
                    </router-link>
                    <span class="layout__user text-zinc-500 text-sm">
                        {{ auth.user?.name || 'Usuario' }} ({{ auth.tenantId }})
                    </span>
                    <button @click="handleLogout" class="layout__logout">
                        Salir
                    </button>
                </template>
                <template v-else>
                    <router-link class="layout__link" to="/login">
                        Acceso
                    </router-link>
                </template>
            </nav>
        </header>
        <main class="layout__main">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
    await auth.logout();
    router.push({ name: 'login' });
}
</script>

<style scoped>
.layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
    color: #0f172a;
}

.layout__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.layout__brand {
    font-weight: 600;
    letter-spacing: 0.02em;
}

.layout__nav {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.layout__link {
    color: #0f172a;
    text-decoration: none;
    font-weight: 500;
}

.layout__link.router-link-active {
    color: #2563eb;
}

.layout__user {
    font-size: 0.85rem;
    background: #f1f5f9;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.layout__logout {
    background: none;
    border: none;
    color: #dc2626;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.layout__logout:hover {
    background: #fef2f2;
}

.layout__main {
    flex: 1;
    padding: 1.5rem;
}
</style>
