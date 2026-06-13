import { defineStore } from 'pinia';
import patientService from '@/modules/patients/services/patientService';

export const usePatientStore = defineStore('patients', {
    state: () => ({
        patients: [],
        currentPatient: null,
        loading: false,
        error: null,
    }),
    actions: {
        async fetchPatients() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await patientService.getPatients();
                this.patients = data;
            } catch (err) {
                this.error = err.response?.data?.message ?? 'Error al cargar pacientes';
                throw err;
            } finally {
                this.loading = false;
            }
        },
        async fetchPatient(id) {
            this.loading = true;
            this.error = null;
            this.currentPatient = null;
            try {
                const { data } = await patientService.getPatient(id);
                this.currentPatient = data;
            } catch (err) {
                this.error = err.response?.data?.message ?? 'Error al cargar el detalle del paciente';
                throw err;
            } finally {
                this.loading = false;
            }
        },
        async createPatient(patientData) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await patientService.createPatient(patientData);
                this.patients.push(data);
                return data;
            } catch (err) {
                this.error = err.response?.data?.message ?? 'Error al registrar paciente';
                throw err;
            } finally {
                this.loading = false;
            }
        },
        async updatePatient(id, patientData) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await patientService.updatePatient(id, patientData);
                const index = this.patients.findIndex(p => p.id === id);
                if (index !== -1) {
                    this.patients[index] = data;
                }
                if (this.currentPatient && this.currentPatient.id === id) {
                    this.currentPatient = { ...this.currentPatient, ...data };
                }
                return data;
            } catch (err) {
                this.error = err.response?.data?.message ?? 'Error al actualizar paciente';
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
