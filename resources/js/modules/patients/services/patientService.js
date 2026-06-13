import api from '@/plugins/axios';

export default {
    getPatients() {
        return api.get('/patients');
    },
    getPatient(id) {
        return api.get(`/patients/${id}`);
    },
    createPatient(data) {
        return api.post('/patients', data);
    },
    updatePatient(id, data) {
        return api.put(`/patients/${id}`, data);
    },
};
