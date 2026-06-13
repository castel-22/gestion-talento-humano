<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Fila 1 --}}
    <div>
        <label class="label-pc">Código Automático de Empleado</label>
        <div class="relative">
            <i class="fas fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="employee_code" value="{{ old('employee_code', $employeeCode ?? ($employee->employee_code ?? '')) }}" class="input-pc pl-12 bg-gray-50 font-black text-pc-blue" readonly>
        </div>
        <p class="text-[8px] text-gray-400 font-bold mt-1 uppercase tracking-widest">Generado por el sistema</p>
    </div>
    <div>
        <label class="label-pc">Fecha de Ingreso Institucional *</label>
        <input type="date" name="hired_date" value="{{ old('hired_date', isset($employee->hired_date) ? $employee->hired_date->format('Y-m-d') : '') }}" class="input-pc shadow-sm" required>
        @error('hired_date') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    {{-- Fila 2 --}}
    <div>
        <label class="label-pc">Cargo Nominal / Funcional *</label>
        <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" class="input-pc" placeholder="Ej: Especialista de Rescate I" required>
        @error('position') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Departamento o Unidad de Adscripción</label>
        <select name="department_id" class="input-pc">
            <option value="">Seleccione Unidad...</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    {{-- Fila 3 --}}
    <div>
        <label class="label-pc">Tipo de Relación Laboral</label>
        <select name="employment_type" class="input-pc">
            <option value="fijo" {{ old('employment_type', $employee->employment_type ?? '') == 'fijo' ? 'selected' : '' }}>Personal Fijo</option>
            <option value="contratado" {{ old('employment_type', $employee->employment_type ?? '') == 'contratado' ? 'selected' : '' }}>Personal Contratado</option>
            <option value="comision" {{ old('employment_type', $employee->employment_type ?? '') == 'comision' ? 'selected' : '' }}>Comisión de Servicio</option>
        </select>
        @error('employment_type') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Estado Operativo Actual *</label>
        <select name="status" class="input-pc border-l-4 border-l-pc-orange">
            <option value="activo" {{ old('status', $employee->status ?? '') == 'activo' ? 'selected' : '' }}>Activo / Operativo</option>
            <option value="inactivo" {{ old('status', $employee->status ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo / Baja</option>
            <option value="reposo" {{ old('status', $employee->status ?? '') == 'reposo' ? 'selected' : '' }}>En Reposo Médico</option>
        </select>
        @error('status') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    {{-- Jerarquía --}}
    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
        <div>
            <label class="label-pc text-pc-blue">Nivel Jerárquico</label>
            <select name="position_id" class="input-pc bg-white">
                <option value="">Seleccione Nivel...</option>
                @foreach($positions ?? [] as $pos)
                    <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id ?? '') == $pos->id ? 'selected' : '' }}>
                        {{ $pos->name }} ({{ $pos->level }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label-pc text-pc-blue">Rango de Oficialía</label>
            <select name="rank_id" class="input-pc bg-white">
                <option value="">No aplica (Personal Administrativo)</option>
                @foreach($ranks ?? [] as $rank)
                    <option value="{{ $rank->id }}" {{ old('rank_id', $employee->rank_id ?? '') == $rank->id ? 'selected' : '' }}>
                        {{ $rank->name }} ({{ $rank->abbreviation }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Otros --}}
    <div>
        <label class="label-pc">Código Institucional Único</label>
        <input type="text" name="institutional_code" value="{{ old('institutional_code', $employee->institutional_code ?? '') }}" placeholder="Ej: CPC-001" class="input-pc">
    </div>
    
    @if($showUserField ?? true)
    <div>
        <label class="label-pc text-pc-orange">Vincular a Usuario del Sistema</label>
        <select name="user_id" class="input-pc border-pc-orange/20">
            <option value="">-- Sin cuenta de acceso --</option>
            @foreach($users ?? [] as $user)
                <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>
    @endif
</div>