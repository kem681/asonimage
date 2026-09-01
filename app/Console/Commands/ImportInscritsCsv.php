<?php

namespace App\Console\Commands;

use App\Models\Edition;
use App\Services\AuthorizedEmailCsvImporter;
use Illuminate\Console\Command;

class ImportInscritsCsv extends Command
{
    protected $signature = 'import:inscrits-csv {file} {--edition= : Annee de l\'edition (ex: 2026)}';

    protected $description = "Importe le nom et l'email des inscrits depuis un export CSV du formulaire d'inscription";

    public function handle(AuthorizedEmailCsvImporter $importer): int
    {
        $path = $this->argument('file');

        if (! is_readable($path)) {
            $this->error("Fichier introuvable ou illisible : {$path}");

            return self::FAILURE;
        }

        $year = $this->option('edition') ?: now()->year;
        $edition = Edition::firstOrCreate(['year' => $year], ['label' => "Edition {$year}"]);

        $result = $importer->import($path, $edition);

        $this->info("Importe : {$result['imported']} - Ignore (email invalide/manquant) : {$result['skipped']}");

        return self::SUCCESS;
    }
}
