<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Fila 1 --}}
    <div>
        <label class="label-pc">Primer Nombre *</label>
        <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name ?? '') }}" class="input-pc" required>
        @error('first_name') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Primer Apellido *</label>
        <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name ?? '') }}" class="input-pc" required>
        @error('last_name') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Número de Cédula *</label>
        <input type="text" name="id_number" value="{{ old('id_number', $employee->id_number ?? '') }}" class="input-pc" required>
        @error('id_number') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    {{-- Fila 2 --}}
    <div>
        <label class="label-pc">Fecha de Nacimiento</label>
        <input type="date" name="birth_date" value="{{ old('birth_date', isset($employee->birth_date) ? $employee->birth_date->format('Y-m-d') : '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Lugar de Nacimiento</label>
        <input type="text" name="birth_place" value="{{ old('birth_place', $employee->birth_place ?? '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Estado Civil</label>
        <select name="marital_status" class="input-pc">
            <option value="">Seleccione...</option>
            <option value="soltero" {{ old('marital_status', $employee->marital_status ?? '') == 'soltero' ? 'selected' : '' }}>Soltero/a</option>
            <option value="casado" {{ old('marital_status', $employee->marital_status ?? '') == 'casado' ? 'selected' : '' }}>Casado/a</option>
            <option value="divorciado" {{ old('marital_status', $employee->marital_status ?? '') == 'divorciado' ? 'selected' : '' }}>Divorciado/a</option>
            <option value="viudo" {{ old('marital_status', $employee->marital_status ?? '') == 'viudo' ? 'selected' : '' }}>Viudo/a</option>
        </select>
    </div>

    {{-- Fila 3 --}}
    <div>
        <label class="label-pc">Género / Sexo</label>
        <select name="gender" class="input-pc">
            <option value="">Seleccione...</option>
            <option value="masculino" {{ old('gender', $employee->gender ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="femenino" {{ old('gender', $employee->gender ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
            <option value="otro" {{ old('gender', $employee->gender ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="label-pc">Correo Electrónico Personal</label>
        <input type="email" name="email" value="{{ old('email', $employee->email ?? '') }}" class="input-pc" placeholder="ejemplo@correo.com">
    </div>

    {{-- Direcciones --}}
    <div class="md:col-span-3">
        <label class="label-pc">Dirección de Habitación Completa</label>
        <input type="text" name="address" value="{{ old('address', $employee->address ?? '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Sector / Urbanización</label>
        <input type="text" name="sector" value="{{ old('sector', $employee->sector ?? '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Parroquia</label>
        <input type="text" name="parish" value="{{ old('parish', $employee->parish ?? '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Teléfono de Habitación</label>
        <input type="text" name="home_phone" value="{{ old('home_phone', $employee->home_phone ?? '') }}" class="input-pc">
    </div>

    {{-- Contactos --}}
    <div>
        <label class="label-pc">Teléfono Móvil (Personal)</label>
        <input type="text" name="personal_phone" value="{{ old('personal_phone', $employee->personal_phone ?? '') }}" class="input-pc">
    </div>
    <div>
        <label class="label-pc">Contacto de Emergencia</label>
        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name ?? '') }}" class="input-pc" placeholder="Nombre completo">
    </div>
    <div>
        <label class="label-pc">Teléfono de Emergencia</label>
        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone ?? '') }}" class="input-pc">
    </div>

    {{-- Salud --}}
    <div>
        <label class="label-pc">Tipo de Sangre</label>
        <select name="blood_type" class="input-pc">
            <option value="">Seleccione...</option>
            @foreach($bloodTypes ?? ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $type)
                <option value="{{ $type }}" {{ old('blood_type', $employee->blood_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="label-pc">Alergias Conocidas</label>
        <input type="text" name="allergies" value="{{ old('allergies', $employee->allergies ?? '') }}" class="input-pc" placeholder="Ninguna / Desconocidas">
    </div>
</div>