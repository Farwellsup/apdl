<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Company;

class ThemeCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears old company css files compiled by service.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

          $companies = Company::all();

        foreach ($companies as $company) {
            $themeFiles = glob(public_path("themes/company-{$company->id}-*.css"));

            if (count($themeFiles) <= 1) {
                continue; // nothing to clean
            }

            if ($company->theme_updated_at) {
                $currentThemeFile = public_path("themes/company-{$company->id}-{$company->theme_updated_at->timestamp}.css");
                if (file_exists($currentThemeFile)) {
                    // Remove current theme file from deletion list
                    $themeFiles = array_filter($themeFiles, fn($file) => $file !== $currentThemeFile);
                }
            }

            // Sort files by modified time descending (newest first)
            usort($themeFiles, fn($a, $b) => filemtime($b) <=> filemtime($a));

            // Keep the newest, delete the rest
            array_shift($themeFiles); // remove newest
            foreach ($themeFiles as $file) {
                File::delete($file);
                $this->info("Deleted old theme file: " . basename($file));
            }
        }

        $this->info('Old theme CSS cleanup completed.');
        return 0;
    }
    
}
