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
            '_comment' => 'You can add, remove, or update fields in this section.',
            'model' => $name,
            'generate_factory' => true,
            'generate_tests' => true,
            'fields' => [
                ['name' => 'title', 'type' => 'string', 'nullable' => false],
                ['name' => 'content', 'type' => 'text', 'nullable' => false],
                ['name' => 'user_id', 'type' => 'integer', 'nullable' => false],
                ['name' => 'is_active', 'type' => 'boolean', 'nullable' => false, 'default' => 1],
                 
            ]
        ];

        File::put($templatePath, json_encode($template, JSON_PRETTY_PRINT));
        $this->info("Template created at: {$templatePath}");
    }
}
