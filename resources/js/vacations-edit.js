/**
 * Lógica Alpine para el formulario de edición de vacaciones.
 * Los datos iniciales se obtienen del elemento #vacation-edit-data.
 */
(function() {
    'use strict';

    // Leer datos iniciales desde el elemento oculto
    const dataElement = document.getElementById('vacation-edit-data');
    if (!dataElement) {
        console.error('No se encontró el elemento #vacation-edit-data');
        return;
    }

    const initialData = {
        availableDays: parseInt(dataElement.dataset.availableDays) || 0,
        regAvailable: parseInt(dataElement.dataset.regAvailable) || 0,
        accAvailable: parseInt(dataElement.dataset.accAvailable) || 0,
        initialStartDate: dataElement.dataset.initialStartDate || '',
        initialEndDate: dataElement.dataset.initialEndDate || '',
        initialRegDays: parseInt(dataElement.dataset.initialRegDays) || 0,
        initialAccDays: parseInt(dataElement.dataset.initialAccDays) || 0
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('editVacationForm', () => ({
            availableDays: initialData.availableDays,
            regAvailable: initialData.regAvailable,
            accAvailable: initialData.accAvailable,
            form: {
                start_date: initialData.initialStartDate,
                end_date: initialData.initialEndDate,
                reg_days: initialData.initialRegDays,
                acc_days: initialData.initialAccDays
            },
            totalDays: initialData.initialRegDays + initialData.initialAccDays,
            daysError: '',

            updateTotalDays() {
                this.totalDays = (parseInt(this.form.reg_days) || 0) + (parseInt(this.form.acc_days) || 0);
                this.calculateEndDate();
                this.validateDays();
            },

            calculateEndDate() {
                if (!this.form.start_date || this.totalDays <= 0) return;
                const start = new Date(this.form.start_date);
                const end = new Date(start);
                end.setDate(start.getDate() + this.totalDays - 1);
                this.form.end_date = end.toISOString().split('T')[0];
            },

            validateDays() {
                const reg = parseInt(this.form.reg_days) || 0;
                const acc = parseInt(this.form.acc_days) || 0;

                if (reg > this.regAvailable) {
                    this.daysError = `Máximo ${this.regAvailable} días regulares.`;
                    return false;
                }
                if (acc > this.accAvailable) {
                    this.daysError = `Máximo ${this.accAvailable} días acumulados.`;
                    return false;
                }
                if (reg + acc <= 0) {
                    this.daysError = 'Debe solicitar al menos 1 día.';
                    return false;
                }
                this.daysError = '';
                return true;
            },

            submitForm(e) {
                if (!this.validateDays()) {
                    // Si falla la validación, el formulario no se envía (@@submit.prevent está en el HTML)
                    return;
                }
                e.target.submit();
            }
        }));
    });
})();