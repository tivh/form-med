<?php

namespace App\Console\Commands;

use App\Models\FormSubmission;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldDocuments extends Command
{
    protected $signature = 'documents:cleanup {--days=0 : Mantém compatibilidade com a antiga opção, mas a limpeza automática foi desativada}';

    protected $description = 'Mantém os arquivos armazenados indefinidamente; a limpeza automática foi desativada.';

    public function handle(): int
    {
        $this->info('A retenção automática de arquivos foi desativada. Os arquivos não serão removidos por tempo de armazenamento.');

        return Command::SUCCESS;
    }
}
