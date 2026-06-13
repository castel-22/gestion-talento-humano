<div class="card-pc p-4 sticky top-4 dark:bg-slate-900 dark:border-slate-800" x-data="attendanceApp()" x-init="init()">
    <h3 class="text-[11px] font-black mb-3 text-pc-blue dark:text-white uppercase tracking-widest flex items-center gap-2">
        <i class="fas fa-id-card text-pc-orange"></i> Marcar Asistencia
    </h3>

    <!-- Buscador (Autocompletado) -->
    <div class="flex flex-col gap-2 w-full relative">
        <div class="relative">
            <i class="fas fa-fingerprint absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            <input type="text" x-model="cedula" 
                   @@input.debounce.300ms="fetchSuggestions"
                   @@keydown.escape="closeSuggestions"
                   @@keydown.arrow-down.prevent="highlightNext"
                   @@keydown.arrow-up.prevent="highlightPrev"
                   @@keydown.enter.prevent="selectHighlighted"
                   @@focus="fetchSuggestions"
                   placeholder="C.I o Nombre del empleado"
                   class="input-pc pl-8 h-9 text-xs border-gray-200 dark:bg-slate-800 dark:border-slate-700 dark:text-white"
                   autocomplete="off">
            <div x-show="isSearching" class="absolute right-2 top-2">
                <i class="fas fa-spinner fa-spin text-gray-400 text-xs"></i>
            </div>
        </div>

        <div x-show="suggestions.length > 0 && showSuggestions" @@click.away="closeSuggestions"
             class="absolute z-50 w-full mt-10 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
            <template x-for="(emp, index) in suggestions" :key="emp.id">
                <div @@click="selectEmployee(emp)" @@mouseenter="highlightIndex = index"
                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 transition"
                     :class="{ 'bg-gray-100': highlightIndex === index }">
                    <div class="font-bold text-gray-800 text-[10px]" x-text="emp.label || emp.full_name"></div>
                    <div class="text-[9px] text-gray-500" x-text="emp.value || emp.id_number"></div>
                </div>
            </template>
        </div>

        <button @@click="buscar"
                class="btn-pc-secondary h-9 text-[10px] py-0 shadow-sm shadow-blue-900/10 active:scale-95">
            <i class="fas fa-search"></i> Buscar
        </button>
    </div>

    <!-- Información del empleado (oculta inicialmente) -->
    <div x-show="mostrarInfo" class="mt-6" x-transition x-cloak>
        <div class="bg-gray-50 dark:bg-slate-800/50 p-4 rounded-xl border border-gray-100 dark:border-slate-800 shadow-inner">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-pc-blue text-white flex items-center justify-center font-black text-xs">
                    <span x-text="empleado.nombre ? empleado.nombre.split(' ').map(n => n[0]).join('').substring(0,2) : ''"></span>
                </div>
                <div class="min-w-0">
                    <p class="font-black text-gray-900 dark:text-white text-sm truncate" x-text="empleado.nombre"></p>
                    <p class="text-[10px] text-pc-orange font-bold uppercase tracking-tighter" x-text="'CI: ' + empleado.cedula"></p>
                </div>
            </div>
            
            <p class="text-[10px] text-gray-400 font-bold uppercase mb-4" x-text="empleado.departamento"></p>

            <div class="grid grid-cols-2 gap-3">
                <button @@click="registrar('check_in')" :disabled="!puedeEntrar"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-emerald-900/20 transition-all active:scale-95 disabled:opacity-30">
                    <i class="fas fa-sign-in-alt mr-1"></i> Entrada
                </button>
                <button @@click="registrar('check_out')" :disabled="!puedeSalir"
                        class="bg-pc-red hover:bg-red-700 text-white py-3 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-red-900/20 transition-all active:scale-95 disabled:opacity-30">
                    <i class="fas fa-sign-out-alt mr-1"></i> Salida
                </button>
            </div>
        </div>
    </div>

    <!-- Mensaje general (Éxito o Error) -->
    <div x-show="mensaje" x-transition x-cloak class="mt-4 p-2 rounded-lg text-[10px] text-center font-black uppercase tracking-widest"
         :class="mensajeError ? 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400' : 'bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400'"
         x-text="mensaje"></div>

    <!-- Hora del servidor -->
    <div class="mt-3 text-xs text-gray-500 text-center">
        Hora servidor: <span x-text="horaServidor" class="font-mono"></span>
    </div>
</div>

<script>
    function attendanceApp() {
        return {
            cedula: '',
            mostrarInfo: false,
            empleado: { id: null, nombre: '', cedula: '', departamento: '' },
            puedeEntrar: false,
            puedeSalir: false,
            mensaje: '',
            mensajeError: false,
            horaServidor: '',
            procesando: false,

            suggestions: [],
            showSuggestions: false,
            highlightIndex: -1,
            isSearching: false,

            init() {
                this.actualizarHora();
                setInterval(() => this.actualizarHora(), 60000);
            },
            actualizarHora() {
                fetch('{{ url("/server-time") }}')
                    .then(res => res.json())
                    .then(data => this.horaServidor = data.time)
                    .catch(() => this.horaServidor = new Date().toLocaleTimeString());
            },

            async fetchSuggestions() {
                if (this.cedula.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                this.isSearching = true;
                try {
                    const response = await fetch(`{{ url('/api/employees/autocomplete') }}?term=${encodeURIComponent(this.cedula)}`);
                    if (response.ok) {
                        this.suggestions = await response.json();
                        this.showSuggestions = this.suggestions.length > 0;
                        this.highlightIndex = -1;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.isSearching = false;
                }
            },
            closeSuggestions() { this.showSuggestions = false; },
            highlightNext() {
                if (this.suggestions.length === 0) return;
                this.highlightIndex = (this.highlightIndex + 1) % this.suggestions.length;
                this.showSuggestions = true;
            },
            highlightPrev() {
                if (this.suggestions.length === 0) return;
                this.highlightIndex = (this.highlightIndex - 1 + this.suggestions.length) % this.suggestions.length;
                this.showSuggestions = true;
            },
            selectHighlighted() {
                if (this.highlightIndex >= 0 && this.highlightIndex < this.suggestions.length) {
                    this.selectEmployee(this.suggestions[this.highlightIndex]);
                } else if (this.cedula) {
                    this.buscar();
                }
            },
            selectEmployee(emp) {
                const idNumber = emp.value || emp.id_number;
                this.cedula = idNumber;
                this.suggestions = [];
                this.showSuggestions = false;
                this.buscar();
            },

            buscar() {
                if (this.procesando) return;
                if (!this.cedula) {
                    this.mostrarMensaje('Ingrese una cédula', true);
                    return;
                }
                this.procesando = true;
                fetch(`{{ url('/attendance/search') }}?id_number=${encodeURIComponent(this.cedula)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.procesando = false;
                        if (!data.success) {
                            this.mostrarMensaje(data.message, true);
                            this.mostrarInfo = false;
                            return;
                        }
                        this.empleado = {
                            id: data.employee.id,
                            nombre: data.employee.full_name,
                            cedula: data.employee.id_number,
                            departamento: data.employee.department
                        };
                        this.puedeEntrar = data.can_check_in;
                        this.puedeSalir = data.can_check_out;
                        this.mostrarInfo = true;
                        this.mensaje = '';
                    })
                    .catch(err => { this.procesando = false; this.mostrarMensaje('Error de conexión', true); });
            },
            registrar(tipo) {
                if (this.procesando) return;
                if ((tipo === 'check_in' && !this.puedeEntrar) || (tipo === 'check_out' && !this.puedeSalir)) return;
                this.procesando = true;
                fetch('{{ url('/attendance/register') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ employee_id: this.empleado.id, type: tipo })
                })
                .then(res => res.json())
                .then(data => {
                    this.procesando = false;
                    this.mostrarMensaje(data.message, !data.success);
                    if (data.success) {
                        // Reset completo después de registro exitoso
                        setTimeout(() => {
                            this.cedula = '';
                            this.mostrarInfo = false;
                            this.empleado = { id: null, nombre: '', cedula: '', departamento: '' };
                            this.puedeEntrar = false;
                            this.puedeSalir = false;
                        }, 2000);
                    }
                })
                .catch(err => { this.procesando = false; this.mostrarMensaje('Error al registrar', true); });
            },
            mostrarMensaje(msg, isError) {
                this.mensaje = msg;
                this.mensajeError = isError;
                setTimeout(() => { this.mensaje = ''; }, 3000);
            }
        }
    }
</script>