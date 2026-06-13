<div id="documents-container" class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <div class="bg-pc-orange/10 p-4 rounded-xl border border-pc-orange/20 flex-1 mr-6">
            <p class="text-[10px] font-black text-pc-orange uppercase tracking-widest leading-tight">
                <i class="fas fa-info-circle mr-1"></i> Formatos admitidos: PDF, DOC, JPG, PNG.
            </p>
            <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">Tamaño máximo por archivo: 2MB</p>
        </div>
        <button type="button" id="add-document" class="bg-pc-blue hover:bg-blue-800 text-white font-black text-[10px] uppercase px-6 py-4 rounded-xl shadow-lg shadow-blue-100 transition-all flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i> Adjuntar Documento
        </button>
    </div>

    {{-- Documentos Existentes --}}
    @if(isset($employee) && $employee->documents->count())
        <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100 mb-8">
            <h4 class="text-[10px] font-black text-pc-blue uppercase tracking-widest mb-4">Documentos Cargados Actuales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($employee->documents as $doc)
                    <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between group">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-10 h-10 rounded-lg bg-pc-blue/10 text-pc-blue flex items-center justify-center">
                                <i class="fas {{ str_contains($doc->file_name, 'pdf') ? 'fa-file-pdf' : 'fa-file-image' }} text-lg"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-[10px] font-black text-gray-800 uppercase truncate">{{ $doc->title }}</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase truncate">{{ $doc->file_name }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-pc-blue hover:text-blue-800 p-2">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                            {{-- Checkbox para marcar eliminación si fuera necesario en el controlador --}}
                            {{-- <input type="checkbox" name="delete_documents[]" value="{{ $doc->id }}" class="rounded text-pc-red"> --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="documents-list">
        <!-- Los documentos se agregan dinámicamente -->
    </div>
</div>

@push('scripts')
<script>
    let docIndex = 0;
    document.getElementById('add-document')?.addEventListener('click', function() {
        const container = document.getElementById('documents-list');
        const html = `
            <div class="card-pc p-4 document-item bg-gray-50 border-dashed border-2 hover:bg-white transition-all group">
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <label class="label-pc">Título del Documento</label>
                            <input type="text" name="new_documents[${docIndex}][title]" class="input-pc text-xs" placeholder="Ej: Certificado de Rescate" required>
                        </div>
                        <button type="button" class="ml-2 text-pc-red hover:bg-pc-red/10 p-2 rounded-lg transition-colors remove-document" title="Eliminar">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label-pc">Categoría</label>
                            <select name="new_documents[${docIndex}][type]" class="input-pc text-[10px] font-bold uppercase" required>
                                <option value="titulo">Título</option>
                                <option value="certificado">Certificado</option>
                                <option value="curso">Curso</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="label-pc">Archivo (PDF/IMG)</label>
                            <input type="file" name="new_documents[${docIndex}][file]" class="input-pc text-[10px] file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-[9px] file:font-black file:bg-pc-blue/10 file:text-pc-blue" accept=".pdf,.doc,.docx,.jpg,.png" required>
                        </div>
                    </div>

                    <div>
                        <label class="label-pc">Breve Descripción</label>
                        <input type="text" name="new_documents[${docIndex}][description]" class="input-pc text-xs" placeholder="Detalles adicionales...">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        docIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document')) {
            e.target.closest('.document-item').remove();
        }
    });
</script>
@endpush