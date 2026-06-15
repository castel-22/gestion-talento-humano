<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Ifsnop\Mysqldump as IMysqldump;

class BackupController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Backup::class);
        $backups = Backup::with('creator')->paginate(10);
        return view('backups.index', compact('backups'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Backup::class);

        // 1. Asegurar que existe el directorio
        Storage::disk('local')->makeDirectory('backups');

        // 2. Generar el nombre de archivo
        $filename = 'respaldo_bd_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $path = 'backups/' . $filename;
        $absolutePath = Storage::disk('local')->path($path);

        // 3. Obtener credenciales de base de datos
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbDatabase = config('database.connections.mysql.database', 'gestion_talento');
        $dbUsername = config('database.connections.mysql.username', 'root');
        $dbPassword = config('database.connections.mysql.password', '');

        try {
            $dumpSettings = [
                'add-drop-table' => true,
                'no-data' => false,
                'skip-triggers' => false,
                'skip-comments' => false,
                'skip-definer' => true,
                'skip-tz-utc' => false,
                'no-create-info' => false,
            ];

            $dump = new IMysqldump\Mysqldump("mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}", $dbUsername, $dbPassword, $dumpSettings);
            $dump->start($absolutePath);

            // Verificar que el archivo se haya creado y no esté vacío
            if (Storage::disk('local')->exists($path) && Storage::disk('local')->size($path) > 0) {
                $sizeInKb = Storage::disk('local')->size($path) / 1024;

                Backup::create([
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $sizeInKb,
                    'created_by' => Auth::id()
                ]);

                return redirect()->route('backups.index')->with('success', 'El respaldo de la base de datos se ha generado exitosamente (' . number_format($sizeInKb, 2) . ' KB).');
            }

            return redirect()->route('backups.index')->with('error', 'Ocurrió un error al generar el respaldo de la base de datos (Archivo vacío).');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'Ocurrió un error crítico al generar el respaldo: ' . $e->getMessage());
        }
    }

    public function destroy(Backup $backup)
    {
        $this->authorize('delete', $backup);
        Storage::disk('local')->delete($backup->path);
        $backup->delete();
        return redirect()->route('backups.index')->with('success', 'Respaldo eliminado.');
    }

    public function download(Backup $backup)
    {
        $this->authorize('view', $backup);

        if (!Storage::disk('local')->exists($backup->path)) {
            return redirect()->route('backups.index')->with('error', 'El archivo físico del respaldo no se encuentra en el servidor.');
        }

        return Storage::disk('local')->download($backup->path);
    }

    public function restore(Backup $backup)
    {
        $this->authorize('restore', $backup);

        if (!Storage::disk('local')->exists($backup->path)) {
            return redirect()->route('backups.index')->with('error', 'El archivo de respaldo físico no se encuentra en el servidor.');
        }

        $absolutePath = Storage::disk('local')->path($backup->path);

        try {
            $sql = file_get_contents($absolutePath);
            DB::unprepared($sql);

            // Deslogueo para evitar inconsistencias de sesión con datos antiguos
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login')->with('success', 'El sistema ha sido restaurado exitosamente al punto seleccionado. Por favor, inicie sesión de nuevo.');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'Ocurrió un error al restaurar la base de datos: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('create', Backup::class);
        return view('backups.create');
    }

    public function upload(Request $request)
    {
        $this->authorize('create', Backup::class);

        $request->validate([
            'backup_file' => 'required|file|max:51200',
        ]);

        if ($request->hasFile('backup_file')) {
            $file = $request->file('backup_file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($extension, ['sql', 'zip', 'gz'])) {
                return back()->withErrors(['backup_file' => 'El formato de archivo no es válido (solo .sql, .zip, .gz).']);
            }

            // 1. Asegurar que existe el directorio
            Storage::disk('local')->makeDirectory('backups');
            
            // 2. Generar nombre de archivo
            $filename = 'respaldo_bd_externo_' . now()->format('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $extension;
            $path = 'backups/' . $filename;
            
            // 3. Guardar el archivo
            $file->storeAs('backups', $filename, 'local');
            
            // 4. Crear registro en BD
            if (Storage::disk('local')->exists($path)) {
                $sizeInKb = Storage::disk('local')->size($path) / 1024;
                
                Backup::create([
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $sizeInKb,
                    'created_by' => Auth::id()
                ]);
                
                return redirect()->route('backups.index')
                    ->with('success', 'El respaldo externo se ha subido exitosamente (' . number_format($sizeInKb, 2) . ' KB).');
            }
        }

        return redirect()->route('backups.index')->with('error', 'Ocurrió un error al subir el respaldo externo.');
    }
}