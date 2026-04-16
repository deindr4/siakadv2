<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Ifsnop\Mysqldump\Mysqldump;

class BackupController extends Controller
{
    private function dbConfig(): array
    {
        return [
            'host'     => config('database.connections.mysql.host'),
            'port'     => config('database.connections.mysql.port', 3306),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    private function cleanOldBackups(int $keep = 10): void
    {
        $dir   = storage_path('app' . DIRECTORY_SEPARATOR . 'backups');
        $files = collect(glob($dir . DIRECTORY_SEPARATOR . '*.sql'))
            ->map(fn($f) => ['path' => $f, 'time' => filemtime($f)])
            ->sortByDesc('time')
            ->values();

        if ($files->count() > $keep) {
            $files->slice($keep)->each(fn($f) => @unlink($f['path']));
        }
    }

    private function getFiles(): \Illuminate\Support\Collection
    {
        $dir = storage_path('app' . DIRECTORY_SEPARATOR . 'backups');

        if (!is_dir($dir)) return collect();

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');

        return collect($files)->map(function ($fullPath) {
            $name = basename($fullPath);
            $size = filesize($fullPath);
            $time = filemtime($fullPath);
            return (object)[
                'name'      => $name,
                'path'      => $fullPath,
                'size'      => $this->formatSize($size),
                'size_raw'  => $size,
                'date'      => date('d/m/Y H:i:s', $time),
                'timestamp' => $time,
            ];
        })
        ->sortByDesc('timestamp')
        ->values();
    }

    private function getSystemStatus(): array
    {
        // ── Database ──────────────────────────────────────────────
        $dbStatus = ['ok' => false, 'version' => 'MySQL', 'uptime' => '-'];
        try {
            $version = DB::selectOne('SELECT VERSION() as v')?->v ?? '-';
            $uptime  = DB::selectOne("SHOW STATUS LIKE 'Uptime'")?->Value ?? 0;
            $days    = intdiv((int) $uptime, 86400);
            $hours   = intdiv((int) $uptime % 86400, 3600);
            $dbStatus = [
                'ok'      => true,
                'version' => 'MySQL ' . explode('-', $version)[0],
                'uptime'  => $days > 0 ? "{$days}h {$hours}j" : "{$hours} jam",
            ];
        } catch (\Throwable) {}

        // ── Redis ─────────────────────────────────────────────────
        $redisStatus = ['ok' => false, 'version' => 'Redis', 'memory' => '-'];
        try {
            $info    = Redis::command('INFO', ['server']);
            $memInfo = Redis::command('INFO', ['memory']);
            preg_match('/redis_version:([^\r\n]+)/', $info, $vMatch);
            preg_match('/used_memory_human:([^\r\n]+)/', $memInfo, $mMatch);
            $redisStatus = [
                'ok'      => true,
                'version' => 'v' . trim($vMatch[1] ?? '?'),
                'memory'  => trim($mMatch[1] ?? '-'),
            ];
        } catch (\Throwable) {}

        // ── Active DB Connections ─────────────────────────────────
        $connStatus = ['ok' => true, 'active' => 0, 'max' => 151, 'driver' => 'PDO MySQL'];
        try {
            $active  = (int) (DB::selectOne("SHOW STATUS LIKE 'Threads_connected'")?->Value ?? 0);
            $maxConn = (int) (DB::selectOne("SHOW VARIABLES LIKE 'max_connections'")?->Value ?? 151);
            $connStatus = [
                'ok'     => $active < ($maxConn * 0.8),
                'active' => $active,
                'max'    => $maxConn,
                'driver' => 'PDO ' . ucfirst(config('database.default')),
            ];
        } catch (\Throwable) {}

        return ['db' => $dbStatus, 'redis' => $redisStatus, 'conn' => $connStatus];
    }

    // ── Halaman utama ─────────────────────────────────────────────
    public function index()
    {
        $files        = $this->getFiles();
        $dbInfo       = $this->dbConfig();
        $systemStatus = $this->getSystemStatus();

        return view('admin.backup.index', compact('files', 'dbInfo', 'systemStatus'));
    }

    // ── Buat backup baru ──────────────────────────────────────────
    public function create()
    {
        $db = $this->dbConfig();
        $filename = 'backup_' . $db['database'] . '_' . date('Ymd_His') . '.sql';
        $dir      = storage_path('app' . DIRECTORY_SEPARATOR . 'backups');
        $path     = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']}";

            $dump = new Mysqldump($dsn, $db['username'], $db['password'], [
                'add-drop-table'     => true,
                'single-transaction' => true,
                'skip-triggers'      => false,
                'add-locks'          => true,
                'extended-insert'    => true,
            ]);

            $dump->start($path);

            if (!file_exists($path) || filesize($path) < 100) {
                return back()->with('error', 'Backup gagal — file kosong atau tidak terbuat.');
            }

            $size = $this->formatSize(filesize($path));
            $this->cleanOldBackups(10);

            return back()->with('success', "Backup berhasil! <strong>{$filename}</strong> ({$size})");

        } catch (\Exception $e) {
            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    // ── Download ──────────────────────────────────────────────────
    public function download(Request $request)
    {
        $filename = basename($request->query('file'));
        $path     = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }

    // ── Hapus ─────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $filename = basename($request->query('file'));
        $path     = storage_path('app' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $filename);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        @unlink($path);
        return back()->with('success', "File <strong>{$filename}</strong> berhasil dihapus.");
    }

    // ── Restore dari upload ───────────────────────────────────────
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|max:524288',
        ], [
            'backup_file.required' => 'Pilih file backup terlebih dahulu.',
            'backup_file.max'      => 'Ukuran file maksimal 100MB.',
        ]);

        $file     = $request->file('backup_file');
        $tempPath = storage_path('app/backups/temp/restore_' . time() . '.sql');

        if (!is_dir(dirname($tempPath))) mkdir(dirname($tempPath), 0755, true);
        $file->move(dirname($tempPath), basename($tempPath));

        return $this->runRestore($tempPath, true);
    }

    // ── Restore dari file server ──────────────────────────────────
    public function restoreExisting(Request $request)
    {
        $filename = basename($request->input('filename'));
        $path     = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return $this->runRestore($path, false);
    }

    // ── Helper: jalankan restore ──────────────────────────────────
    private function runRestore(string $filePath, bool $deleteAfter = false): \Illuminate\Http\RedirectResponse
    {
        $db = $this->dbConfig();

        try {
            $sql = file_get_contents($filePath);

            if (empty(trim($sql))) {
                if ($deleteAfter) @unlink($filePath);
                return back()->with('error', 'File backup kosong atau tidak valid.');
            }

            $pdo = new \PDO(
                "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ]
            );

            $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');

            $statements = array_filter(
                array_map('trim', explode(";\n", $sql)),
                fn($s) => !empty($s)
            );

            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    $pdo->exec($statement);
                }
            }

            $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');

            if ($deleteAfter) @unlink($filePath);

            Cache::flush();

            return back()->with('success', 'Restore berhasil! Data telah dipulihkan. Cache telah direset.');

        } catch (\Exception $e) {
            if ($deleteAfter) @unlink($filePath);
            return back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }
}