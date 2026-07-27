<?php

namespace App\Console\Commands;

use App\Models\FormSubmission;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldDocuments extends Command
{
    protected $signature = 'documents:cleanup {--days=15 : Remove arquivos com mais de N dias}';

    protected $description = 'Remove arquivos enviados antigos e ajusta metadados de submissões.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = $days > 0 ? $days : 20;
        $threshold = Carbon::now()->subDays($days)->getTimestamp();

        $disk = Storage::disk('private_uploads');
        $deletedFiles = 0;
        foreach ($disk->allFiles() as $file) {
            $lastModified = $disk->lastModified($file);
            if ($lastModified !== false && $lastModified < $threshold) {
                $disk->delete($file);
                $deletedFiles++;
            }
        }

        $updatedRecords = 0;
        FormSubmission::chunk(200, function ($submissions) use (&$updatedRecords, $disk) {
            foreach ($submissions as $submission) {
                $docs = is_array($submission->documents) ? $submission->documents : [];
                $filtered = [];
                $changed = false;
                foreach ($docs as $doc) {
                    $path = $doc['path'] ?? null;
                    if ($path && $disk->exists($path)) {
                        $filtered[] = $doc;
                    } else {
                        $changed = true;
                    }
                }
                if ($changed) {
                    $submission->documents = $filtered;
                    $submission->save();
                    $updatedRecords++;
                }
            }
        });

        $this->info("Arquivos removidos: {$deletedFiles}; registros atualizados: {$updatedRecords}");
        return Command::SUCCESS;
    }
}
