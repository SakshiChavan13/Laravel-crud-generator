<?php

namespace SakshiChavan\LaravelCrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeCrudTemplate extends Command
{
    protected $signature = 'make:crud-template {name}';
    protected $description = 'Create a CRUD template JSON file to edit';

    public function handle()
    {
        $name = $this->argument('name');
        $templatePath = base_path("crud-templates/{$name}.json");

        if (!File::exists(base_path('crud-templates'))) {
            File::makeDirectory(base_path('crud-templates'));
        }

        $template = [
            'model' => $name,
            'generate_validation_requests' => true,
            'generate_inside_folder' => true,
            'fields' => [
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'content', 'type' => 'text']
            ]
        ];

        File::put($templatePath, json_encode($template, JSON_PRETTY_PRINT));
        $this->info("Template created at: {$templatePath}");
    }
}
