<?php
namespace App\Jobs;

use App\Services\UserImportService;
use App\Imports\UserImport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $rows;
    public $companyId;
    public $fileName;

    public function __construct(array $rows, $companyId, $fileName)
    {
        $this->rows = $rows;
        $this->companyId = $companyId;
        $this->fileName = $fileName;
    }

    public function handle(UserImportService $userImportService)
    {
        $result = $userImportService->handle($this->rows, $this->companyId, $this->fileName);

        return $result;
    }
}