<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Outhebox\TranslationsUI\Models\Language;
use Outhebox\TranslationsUI\Models\Translation;
use Outhebox\TranslationsUI\Models\TranslationFile;

class FixArabicTranslationsCommand extends Command
{
    protected $signature = 'translations:fix-arabic';

    protected $description = 'Fix Arabic translations by properly importing them from language files';

    public function handle(): void
    {
        $this->info('Fixing Arabic translations...');

        // Get Arabic language
        $arabicLanguage = Language::where('code', 'ar')->first();
        if (! $arabicLanguage) {
            $this->error('Arabic language not found!');

            return;
        }

        // Get English source translation
        $englishTranslation = Translation::where('source', true)->first();
        if (! $englishTranslation) {
            $this->error('English source translation not found!');

            return;
        }

        // Get Arabic translation record
        $arabicTranslation = Translation::where('language_id', $arabicLanguage->id)
            ->where('source', false)
            ->first();

        if (! $arabicTranslation) {
            $this->error('Arabic translation record not found!');

            return;
        }

        // Read Arabic language files
        $translations = $this->getArabicTranslations();

        if (empty($translations)) {
            $this->error('No Arabic translations found in language files!');

            return;
        }

        $this->info('Found '.count($translations).' translation files');

        $updatedCount = 0;
        $createdCount = 0;
        $linkedCount = 0;

        foreach ($translations as $file => $fileTranslations) {
            $this->info("Processing file: $file");

            // Get or create translation file record
            $translationFile = $this->getOrCreateTranslationFile($file);

            foreach (Arr::dot($fileTranslations) as $key => $value) {
                if (is_array($value) && empty($value)) {
                    continue;
                }

                // Find the English source phrase
                $englishPhrase = $englishTranslation->phrases()
                    ->where('key', $key)
                    ->first(); // Remove group constraint since languages have different groups

                // Find existing Arabic phrase or create new one
                $arabicPhrase = $arabicTranslation->phrases()
                    ->where('key', $key)
                    ->where('group', $translationFile->name)
                    ->first();

                if ($arabicPhrase) {
                    // Update existing phrase
                    $updated = false;
                    if ($arabicPhrase->value !== $value) {
                        $arabicPhrase->update([
                            'value' => $value,
                            'parameters' => is_string($value) ? $this->getPhraseParameters($value) : null,
                        ]);
                        $updated = true;
                    }

                    // Link to English source phrase if not already linked
                    if ($englishPhrase && $arabicPhrase->phrase_id !== $englishPhrase->id) {
                        $arabicPhrase->update(['phrase_id' => $englishPhrase->id]);
                        $linkedCount++;
                        $updated = true;
                    }

                    if ($updated) {
                        $updatedCount++;
                    }
                } else {
                    // Create new phrase
                    $arabicTranslation->phrases()->create([
                        'key' => $key,
                        'group' => $translationFile->name,
                        'translation_file_id' => $translationFile->id,
                        'value' => $value,
                        'parameters' => is_string($value) ? $this->getPhraseParameters($value) : null,
                        'phrase_id' => $englishPhrase ? $englishPhrase->id : null,
                    ]);
                    $createdCount++;
                }
            }
        }

        $this->info('Arabic translations fixed!');
        $this->info("Updated: $updatedCount phrases");
        $this->info("Created: $createdCount phrases");
        $this->info("Linked to source: $linkedCount phrases");

        // Show final statistics
        $totalPhrases = $arabicTranslation->phrases()->count();
        $translatedPhrases = $arabicTranslation->phrases()->whereNotNull('value')->where('value', '!=', '')->count();
        $linkedPhrases = $arabicTranslation->phrases()->whereNotNull('phrase_id')->count();
        $percentage = round(($translatedPhrases / $totalPhrases) * 100, 1);

        $this->info("Final status: $translatedPhrases/$totalPhrases phrases translated ($percentage%)");
        $this->info("Linked phrases: $linkedPhrases/$totalPhrases");
    }

    private function getArabicTranslations(): array
    {
        $translations = [];
        $langPath = lang_path();

        // Read JSON file
        $jsonFile = $langPath.'/ar.json';
        if (File::exists($jsonFile)) {
            $jsonContent = File::get($jsonFile);
            $jsonTranslations = json_decode($jsonContent, true);
            if ($jsonTranslations) {
                $translations['ar.json'] = $jsonTranslations;
            }
        }

        // Read PHP files
        $arDir = $langPath.'/ar';
        if (File::isDirectory($arDir)) {
            $files = File::allFiles($arDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $relativePath = 'ar/'.$file->getRelativePathname();
                    $translations[$relativePath] = File::getRequire($file->getPathname());
                }
            }
        }

        return $translations;
    }

    private function getOrCreateTranslationFile(string $file): TranslationFile
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $name = str_replace(['ar.json', 'ar/'], '', $file);
        $name = str_replace('.'.$extension, '', $name);

        $isRoot = $file === 'ar.json' || $file === 'ar.php';

        return TranslationFile::firstOrCreate([
            'name' => $name ?: 'ar',
            'extension' => $extension,
            'is_root' => $isRoot,
        ]);
    }

    private function getPhraseParameters(string $value): ?array
    {
        preg_match_all('/:(\w+)/', $value, $matches);

        return ! empty($matches[1]) ? $matches[1] : null;
    }
}
