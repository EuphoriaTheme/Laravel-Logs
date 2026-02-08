<?php

namespace Pterodactyl\Http\Controllers\Admin\Extensions\{identifier};

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Pterodactyl\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Blueprint admin controller for the Laravel Logs extension.
 *
 * Developer notes:
 * - `{identifier}` is a Blueprint placeholder replaced during install.
 * - Access is restricted to root admins.
 * - Log file access is restricted to `storage/logs/laravel-*.log` and guarded by realpath checks.
 */
class {identifier}ExtensionController extends Controller
{
    private const MAX_TAIL_LINES = 1000;

    public function __construct(
        private ViewFactory $view,
        private BlueprintExtensionLibrary $blueprint,
        private ConfigRepository $config,
        private SettingsRepositoryInterface $settings,
    ){}

    public function index(Request $request): View
    {
        if (!$request->user() || !$request->user()->root_admin) {
            throw new AccessDeniedHttpException();
        }
        
        $logDirPath = storage_path('logs');

        $logFiles = File::glob($logDirPath . '/laravel-*.log');
    
        // Default to the last match returned by glob(). (Not guaranteed to be the newest.)
        $selectedLogFile = $request->input('log_file', end($logFiles));
    
        if ($selectedLogFile && !$this->isValidLogFile($selectedLogFile, $logDirPath)) {
            return $this->view->make(
                'admin.extensions.{identifier}.index', [
                    'root' => "/admin/extensions/{identifier}",
                    'blueprint' => $this->blueprint,
                    'logs' => 'Access denied: File outside logs directory.',
                    'logFiles' => $logFiles,
                    'selectedLogFile' => $selectedLogFile,
                ]
            );
        }
    
        if (!File::exists($selectedLogFile)) {
            return $this->view->make(
                'admin.extensions.{identifier}.index', [
                    'root' => "/admin/extensions/{identifier}",
                    'blueprint' => $this->blueprint,
                    'logs' => 'Log file does not exist.',
                    'logFiles' => $logFiles,
                    'selectedLogFile' => $selectedLogFile,
                ]
            );
        }
    
        // Tail the log for performance (avoids rendering huge files in the browser).
        $logs = collect(explode("\n", File::get($selectedLogFile)))->slice(-self::MAX_TAIL_LINES)->implode("\n");
    
        return $this->view->make(
            'admin.extensions.{identifier}.index', [
                'root' => "/admin/extensions/{identifier}",
                'blueprint' => $this->blueprint,
                'logs' => $logs,
                'logFiles' => $logFiles,
                'selectedLogFile' => $selectedLogFile,
            ]
        );
    }     

    public function downloadLogs(Request $request)
    {
        if (!$request->user() || !$request->user()->root_admin) {
            throw new AccessDeniedHttpException();
        }

        $logFile = $request->input('log_file');
        $logDirPath = storage_path('logs');

        if (!$this->isValidLogFile($logFile, $logDirPath)) {
            return redirect()->back()->with('error', 'Access denied: File outside logs directory.');
        }

        if (File::exists($logFile)) {
            return response()->download($logFile, basename($logFile))->deleteFileAfterSend(false);
        }

        return redirect()->back()->with('error', 'Log file not found.');
    }

    /**
     * Returns true only for real files under `$logDirPath` matching `laravel-*.log`.
     * Uses realpath() checks to mitigate traversal and symlink escapes.
     */
    private function isValidLogFile($filePath, $logDirPath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        $realFilePath = realpath($filePath);
        $realLogDirPath = realpath($logDirPath);

        if ($realFilePath === false || $realLogDirPath === false) {
            return false;
        }

        if (strpos($realFilePath, $realLogDirPath) !== 0) {
            return false;
        }

        $fileName = basename($realFilePath);
        if (!preg_match('/^laravel-.*\.log$/', $fileName)) {
            return false;
        }

        return true;
    }
}
