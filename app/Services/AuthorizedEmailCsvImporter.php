<?php

namespace App\Services;

use App\Models\AuthorizedEmail;
use App\Models\Edition;

class AuthorizedEmailCsvImporter
{
    /**
     * Import only the name and email columns from a registration CSV export.
     * Every other column (phone, birth date, housing, free text, etc.) is
     * intentionally ignored and never persisted.
     *
     * @return array{imported: int, skipped: int}
     */
    public function import(string $filePath, Edition $edition, string $source = AuthorizedEmail::SOURCE_IMPORT_CSV): array
    {
        $handle = fopen($filePath, 'r');

        $header = fgetcsv($handle);
        $emailIndex = $header ? array_search('Mail', $header, true) : false;
        $nameIndex = $header ? array_search('Participant', $header, true) : false;

        $imported = 0;
        $skipped = 0;

        if ($emailIndex === false) {
            fclose($handle);

            return ['imported' => 0, 'skipped' => 0];
        }

        while (($row = fgetcsv($handle)) !== false) {
            $email = trim((string) ($row[$emailIndex] ?? ''));
            $name = $nameIndex !== false ? trim((string) ($row[$nameIndex] ?? '')) : '';

            if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;

                continue;
            }

            AuthorizedEmail::updateOrCreate(
                ['email' => strtolower($email)],
                [
                    'name' => $name !== '' ? $name : null,
                    'edition_id' => $edition->id,
                    'source' => $source,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
