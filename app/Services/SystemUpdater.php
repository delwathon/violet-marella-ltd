<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class SystemUpdater
{
    /**
     * @return array{backup_file: string}
     */
    public function run(): array
    {
        $backupFile = $this->backupDatabase();

        $this->runProcess(['git', 'pull'], 'Git pull failed.');

        $migrateExitCode = Artisan::call('migrate', ['--force' => true]);
        if ($migrateExitCode !== 0) {
            throw new RuntimeException('Migration failed: ' . $this->normalizeOutput(Artisan::output()));
        }

        // $seedExitCode = Artisan::call('db:seed', [
        //     '--class' => 'Database\\Seeders\\SystemUpdateSeeder',
        //     '--force' => true,
        // ]);
        // if ($seedExitCode !== 0) {
        //     throw new RuntimeException('Database seeding failed: ' . $this->normalizeOutput(Artisan::output()));
        // }

        return [
            'backup_file' => $backupFile,
        ];
    }

    private function backupDatabase(): string
    {
        $defaultConnection = (string) config('database.default');
        $connection = (array) config("database.connections.{$defaultConnection}", []);
        $driver = strtolower((string) ($connection['driver'] ?? ''));

        if ($driver === '') {
            throw new RuntimeException('Unable to detect database driver for backup.');
        }

        $backupDirectory = $this->backupDirectory();
        $timestamp = now()->format('Ymd_His');

        return match ($driver) {
            'mysql', 'mariadb' => $this->backupMysql($connection, $backupDirectory, $timestamp, $defaultConnection),
            'pgsql' => $this->backupPostgres($connection, $backupDirectory, $timestamp, $defaultConnection),
            'sqlite' => $this->backupSqlite($connection, $backupDirectory, $timestamp, $defaultConnection),
            default => throw new RuntimeException("Database driver [{$driver}] is not supported for automated backup."),
        };
    }

    private function backupMysql(array $connection, string $backupDirectory, string $timestamp, string $connectionName): string
    {
        $database = trim((string) ($connection['database'] ?? ''));
        if ($database === '') {
            throw new RuntimeException('MySQL backup failed: database name is missing.');
        }

        $fileName = "{$connectionName}_{$timestamp}.sql";
        $fullPath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;
        $mysqldump = 'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe';

        $command = [
            $mysqldump,
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
        ];

        // $command = [
        //     'mysqldump',
        //     '--single-transaction',
        //     '--quick',
        //     '--lock-tables=false',
        // ];

        $socket = trim((string) ($connection['unix_socket'] ?? ''));
        if ($socket !== '') {
            $command[] = '--socket=' . $socket;
        } else {
            $command[] = '--host=' . ((string) ($connection['host'] ?? '127.0.0.1'));
            $port = trim((string) ($connection['port'] ?? '3306'));
            if ($port !== '') {
                $command[] = '--port=' . $port;
            }
        }

        $username = trim((string) ($connection['username'] ?? ''));
        if ($username !== '') {
            $command[] = '--user=' . $username;
        }

        $password = (string) ($connection['password'] ?? '');
        if ($password !== '') {
            $command[] = '--password=' . $password;
        }

        $command[] = $database;
        $command[] = '--result-file=' . $fullPath;

        $this->runProcess($command, 'Database backup failed.');

        if (!File::exists($fullPath)) {
            throw new RuntimeException('Database backup failed: backup file was not created.');
        }

        return $this->toStorageRelativePath($fullPath);
    }

    private function backupPostgres(array $connection, string $backupDirectory, string $timestamp, string $connectionName): string
    {
        $database = trim((string) ($connection['database'] ?? ''));
        if ($database === '') {
            throw new RuntimeException('PostgreSQL backup failed: database name is missing.');
        }

        $fileName = "{$connectionName}_{$timestamp}.sql";
        $fullPath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;

        $command = [
            'pg_dump',
            '--host=' . ((string) ($connection['host'] ?? '127.0.0.1')),
            '--port=' . ((string) ($connection['port'] ?? '5432')),
            '--username=' . ((string) ($connection['username'] ?? '')),
            '--file=' . $fullPath,
            $database,
        ];

        $env = [];
        $password = (string) ($connection['password'] ?? '');
        if ($password !== '') {
            $env['PGPASSWORD'] = $password;
        }

        $this->runProcess($command, 'Database backup failed.', $env);

        if (!File::exists($fullPath)) {
            throw new RuntimeException('Database backup failed: backup file was not created.');
        }

        return $this->toStorageRelativePath($fullPath);
    }

    private function backupSqlite(array $connection, string $backupDirectory, string $timestamp, string $connectionName): string
    {
        $database = trim((string) ($connection['database'] ?? ''));

        if ($database === '' || $database === ':memory:') {
            throw new RuntimeException('SQLite backup failed: database path is invalid.');
        }

        $databasePath = $this->isAbsolutePath($database) ? $database : base_path($database);
        if (!File::exists($databasePath)) {
            throw new RuntimeException('SQLite backup failed: database file does not exist.');
        }

        $fileName = "{$connectionName}_{$timestamp}.sqlite";
        $fullPath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (!copy($databasePath, $fullPath)) {
            throw new RuntimeException('SQLite backup failed: unable to copy database file.');
        }

        return $this->toStorageRelativePath($fullPath);
    }

    private function backupDirectory(): string
    {
        $directory = storage_path('backups');

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return $directory;
    }

    /**
     * @param array<int, string> $command
     * @param array<string, string> $env
     */
    private function runProcess(array $command, string $errorMessage, array $env = []): void
    {
        $process = new Process($command, base_path(), $env === [] ? null : $env);
        $process->setTimeout(900);
        $process->run();

        if (!$process->isSuccessful()) {
            $output = $process->getErrorOutput() . PHP_EOL . $process->getOutput();
            throw new RuntimeException($errorMessage . ' ' . $this->normalizeOutput($output));
        }
    }

    private function normalizeOutput(string $output): string
    {
        $normalized = trim((string) preg_replace('/\s+/', ' ', $output));

        if ($normalized === '') {
            return 'No additional output.';
        }

        return substr($normalized, 0, 400);
    }

    private function toStorageRelativePath(string $fullPath): string
    {
        return 'storage/' . ltrim(str_replace(storage_path(), '', $fullPath), DIRECTORY_SEPARATOR);
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') {
            return false;
        }

        if ($path[0] === '/' || $path[0] === '\\') {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:[\\\\\\/]/', $path);
    }
}
