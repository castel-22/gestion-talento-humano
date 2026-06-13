<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="label-pc">Nivel Educativo Alcanzado</label>
        <select name="education_level" class="input-pc">
            <option value="">Seleccione...</option>
            @foreach($educationLevels ?? ['Primaria', 'Bachillerato', 'Técnico Medio', 'Técnico Superior', 'Universitario', 'Especialista', 'Magister', 'Doctorado'] as $level)
                <option value="{{ $level }}" {{ old('education_level', $employee->education_level ?? '') == $level ? 'selected' : '' }}>
                    {{ $level }}
                </option>
            @endforeach
        </select>
        @error('education_level') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Título o Mención Obtenida</label>
        <input type="text" name="degree" value="{{ old('degree', $employee->degree ?? '') }}" class="input-pc" placeholder="Ej: Lic. en Administración">
        @error('degree') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label-pc">Alma Mater / Institución de Egreso</label>
        <input type="text" name="institution" value="{{ old('institution', $employee->institution ?? '') }}" class="input-pc">
        @error('institution') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="label-pc">Año de Promoción / Graduación</label>
        <input type="number" name="graduation_year" value="{{ old('graduation_year', $employee->graduation_year ?? '') }}" class="input-pc" min="1950" max="{{ date('Y') }}">
        @error('graduation_year') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 bg-pc-blue/5 p-4 rounded-xl border border-pc-blue/10 flex items-center gap-4">
        <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out bg-gray-200 rounded-full">
            <input type="checkbox" name="currently_studying" value="1" {{ old('currently_studying', $employee->currently_studying ?? false) ? 'checked' : '' }} class="absolute w-6 h-6 rounded-full bg-white border-2 border-gray-200 appearance-none cursor-pointer checked:translate-x-6 checked:bg-pc-orange transition-all">
        </div>
        <span class="text-xs font-black text-pc-blue uppercase">¿El integrante se encuentra cursando estudios actualmente?</span>
        @error('currently_studying') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="label-pc">Especializaciones, Cursos y Certificaciones</label>
        <textarea name="specializations" rows="3" class="input-pc" placeholder="Enumere sus certificaciones relevantes..."></textarea>
        <p class="text-[9px] text-gray-400 font-bold mt-2 uppercase tracking-widest"><i class="fas fa-info-circle mr-1"></i> Separe cada formación con una coma (,)</p>
        @error('specializations') <p class="text-pc-red text-[9px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
    </div>
</div>