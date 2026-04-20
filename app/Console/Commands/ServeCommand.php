<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

/**
 * Overrides default serve command for Windows path/network edge cases.
 */
class ServeCommand extends BaseServeCommand
{
    protected function host(): string
    {
        $host = parent::host();

        // On some Windows setups localhost resolution is unreliable.
        if (PHP_OS_FAMILY === 'Windows' && $host === 'localhost') {
            return '127.0.0.1';
        }

        return $host;
    }

    protected function serverCommand(): array
    {
        $server = file_exists(base_path('server.php'))
            ? base_path('server.php')
            : realpath(__DIR__ . '/../../../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

        $server = $this->resolveWindowsShortPath($server);

        return [
            php_binary(),
            '-S',
            $this->host() . ':' . $this->port(),
            $server,
        ];
    }

    protected function startProcess($hasEnvironment): Process
    {
        $process = new Process(
            $this->serverCommand(),
            public_path(),
            PHP_OS_FAMILY === 'Windows'
                ? (new Collection($_ENV))->merge(['PHP_CLI_SERVER_WORKERS' => $this->phpServerWorkers])->all()
                : (new Collection($_ENV))->mapWithKeys(function ($value, $key) use ($hasEnvironment) {
                    if ($this->option('no-reload') || ! $hasEnvironment) {
                        return [$key => $value];
                    }

                    return in_array($key, static::$passthroughVariables) ? [$key => $value] : [$key => false];
                })->merge(['PHP_CLI_SERVER_WORKERS' => $this->phpServerWorkers])->all()
        );

        $this->trap(fn () => [SIGTERM, SIGINT, SIGHUP, SIGUSR1, SIGUSR2, SIGQUIT], function ($signal) use ($process) {
            if ($process->isRunning()) {
                $process->stop(10, $signal);
            }
            exit;
        });

        $process->start($this->handleProcessOutput());

        return $process;
    }

    private function resolveWindowsShortPath(string $path): string
    {
        if (PHP_OS_FAMILY !== 'Windows' || strpos($path, ' ') === false) {
            return $path;
        }

        $escaped = str_replace("'", "''", $path);
        $short = trim((string) shell_exec(
            "powershell -NoProfile -Command \"(New-Object -ComObject Scripting.FileSystemObject).GetFile('{$escaped}').ShortPath\" 2>nul"
        ));

        return ($short && file_exists($short)) ? $short : $path;
    }
}
