<?php

namespace App\Console\Commands;

use App\Services\GenerateCrudFilesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCrud extends Command
{
    protected $signature = 'generate:crud {name}';
    protected $description = 'Generate CRUD files from a template JSON';

    public function handle()
    {
        $name = $this->argument('name');
        $templatePath = base_path("crud-templates/{$name}.json");

        if (!File::exists($templatePath)) {
            $this->error("Template file not found at: {$templatePath}");
            return;
        }

        $template = json_decode(File::get($templatePath), true);


        $service = new GenerateCrudFilesService($this); 
        $service->genrateCrud($template);
       
    }


  
}
