/**
 * Alpine.js data component for the Vacation Edit Form.
 * Reads initial values from the #vacation-edit-data div attributes.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('editVacationForm', () => {
        const dataEl = document.getElementById('vacation-edit-data');

        const regAvailable = parseInt(dataEl?.dataset.regAvailable) || 0;
        const accAvailable = parseInt(dataEl?.dataset.accAvailable) || 0;
        const availableDays = parseInt(dataEl?.dataset.availableDays) || 0;

        const initialStartDate = dataEl?.dataset.initialStartDate || '';
        const initialEndDate = dataEl?.dataset.initialEndDate || '';
        const initialRegDays = parseInt(dataEl?.dataset.initialRegDays) || 0;
        const initialAccDays = parseInt(dataEl?.dataset.initialAccDays) || 0;
        
        let contingencies = [];
        try {
            contingencies = JSON.parse(dataEl?.dataset.contingencies || '[]');
        } catch(e) {}

        return {
            contingencies: contingencies,
            regAvailable: regAvailable,
            accAvailable: accAvailable,
            availableDays: availableDays,

            form: {
                start_date: initialStartDate,
                end_date: initialEndDate,
                reg_days: initialRegDays,
                acc_days: initialAccDays
            },

            totalDays: initialRegDays + initialAccDays,
            daysError: '',
            isSubmitting: false,

            calculateEndDate() {
                if (!this.form.start_date || this.totalDays <= 0) {
                    this.form.end_date = '';
                    return;
                }

                let remainingDays = this.totalDays;
                let curDate = new Date(this.form.start_date + 'T00:00:00');
                
                while (remainingDays > 0) {
                    const dayOfWeek = curDate.getDay();
                    const dateString = curDate.toISOString().split('T')[0];
                    
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    let isContingency = false;
                    
                    for (let plan of this.contingencies) {
                        if (dateString >= plan.start_date && dateString <= plan.end_date) {
                            isContingency = true;
                            break;
                        }
                    }

                    if (!isWeekend && !isContingency) {
                        remainingDays--;
                    }

                    if (remainingDays > 0) {
                        curDate.setDate(curDate.getDate() + 1);
                    }
                }

                this.form.end_date = curDate.toISOString().split('T')[0];
                this.validateDays();
            },

            updateTotalDays() {
                const reg = parseInt(this.form.reg_days) || 0;
                const acc = parseInt(this.form.acc_days) || 0;
                this.totalDays = reg + acc;
                this.calculateEndDate();
            },

            validateDays() {
                const reg = parseInt(this.form.reg_days) || 0;
                const acc = parseInt(this.form.acc_days) || 0;

                if (reg > this.regAvailable) {
                    this.daysError = `No puede solicitar más de ${this.regAvailable} días regulares.`;
                    return false;
                }
                if (acc > this.accAvailable) {
                    this.daysError = `No puede solicitar más de ${this.accAvailable} días acumulados.`;
                    return false;
                }
                if (reg + acc <= 0) {
                    this.daysError = 'Debe solicitar al menos 1 día en total.';
                    return false;
                }
                this.daysError = '';
                return true;
            },

            submitForm(e) {
                if (!this.validateDays()) {
                    e.preventDefault();
                    return;
                }
                // Let the form submit naturally (non-AJAX)
                e.target.submit();
            }
        };
    });
});
