<?php

namespace Pterodactyl\Http\Controllers\Admin\Extensions\{identifier};

use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\BlueprintFramework\Libraries\ExtensionLibrary\Admin\BlueprintAdminLibrary as BlueprintExtensionLibrary;
use Pterodactyl\Http\Requests\Admin\AdminFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pterodactyl\Models\Egg;
use Pterodactyl\Models\User;

class {identifier}ExtensionController extends Controller
{
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
        
        // Path to the logs directory
        $logDirPath = storage_path('logs');

        // Directory of the feedback
        $dir_feedback = "%%__NONCE__%%";

        // Fetch all log files from the logs directory
        $logFiles = File::glob($logDirPath . '/laravel-*.log');
    
        // Get the selected log file from the request, default to the latest log file
        $selectedLogFile = $request->input('log_file', end($logFiles));
    
        // Check if the selected log file exists    
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
    
        // Fetch the contents of the selected log file
        $logs = collect(explode("\n", File::get($selectedLogFile)))->slice(-1000)->implode("\n");
    
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

        // Check if the file exists
        if (File::exists($logFile)) {
            return response()->download($logFile, basename($logFile))->deleteFileAfterSend(false);
        }

        return redirect()->back()->with('error', 'Log file not found.');
    }
}