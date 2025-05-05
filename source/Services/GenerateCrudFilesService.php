<?php

declare(strict_types=1);

namespace SakshiChavan\LaravelCrudGenerator\Services;


use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class GenerateCrudFilesService
{

    protected $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public  function genrateCrud($template)
    {
        try {

            $modelName = $template['model'];
            $fields = $template['fields'];
            // dump($fields);

            $this->generatePermissions($modelName);

            $this->generateRequest($modelName, 'STORE', $fields);
            $this->generateRequest($modelName, 'UPDATE', $fields);
            $this->generateRequest($modelName, 'INDEX', $fields);
            $this->generateRequest($modelName, 'DESTROY', $fields);
            $this->generateRequest($modelName, 'SHOW', $fields);

            $this->generateModel($modelName, $fields);
            $this->generateMigration($modelName, $fields);
            $this->generateResource($modelName, $fields);
            $this->generateController($modelName);


            $this->generateRoutes($modelName);

            $this->generateFactory($modelName, $fields);

            $this->generateTests($modelName);
        } catch (Exception $e) {

            throw ($e);
        }
    }


    private function generatePermissions($modelName)
    {
        try {
            $stub = $this->getStubContent('Permissions.stub');
            $upperModel = Str::upper($modelName);
            $tableName = Str::plural(Str::snake($modelName));

            $stub = str_replace(
                ['{{ modelName }}', '{{ upperModel }}', '{{ tableName }}'],
                [$modelName, $upperModel, $tableName],
                $stub
            );

            $dir = app_path('Constants/Permissions');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $path = "{$dir}/{$modelName}Permissions.php";
            File::put($path, $stub);

            if ($this->command) {
                $this->command->info("Permission constants created: $path");
            }
        } catch (Exception $e) {
            throw ($e);
        }
    }


    private function generateRequest($modelName, $type, $fields)
    {
        try {
            $stub = $this->getStubContent('Request.stub');

            $namespaceModel = Str::studly($modelName);
            $className = ucfirst(strtolower($type)) . "{$namespaceModel}Request";

            if ($type == 'INDEX' || $type == 'SHOW') {
                $permissionType = 'VIEW';
            } elseif ($type == 'STORE') {
                $permissionType = 'CREATE';
            } elseif ($type == 'DESTROY') {
                $permissionType = 'DELETE';
            } else {
                $permissionType = $type;
            }

            $rulesArray = [];

            if ($type === 'INDEX') {
                $rulesArray = [
                    'per_page' => ['sometimes', 'integer'],
                    'page' => ['sometimes', 'integer'],
                ];
            } elseif ($type == 'STORE' || $type == 'UPDATE') {
                foreach ($fields as $field) {
                    $name = $field['name'];

                    $rulesArray[$name][] = $field['type'];

                    if ($type === 'STORE') {
                        $rulesArray[$name][] = isset($field['nullable']) && $field['nullable']
                            ? 'nullable'
                            : 'required';
                    } elseif ($type === 'UPDATE') {
                        $rulesArray[$name][] = 'nullable';
                    }

                    if (str_contains($name, 'id')) {
                        $relation = str_replace('_id', '', $name);
                        $tableName = Str::plural($relation);
                        $rulesArray[$name][] = "exists:{$tableName},id";
                    }
                }
            }


            $permissionConst = strtoupper($permissionType) . '_' . strtoupper($modelName);

            $rulesString = self::convertRulesArrayToString($rulesArray);

            $stub = str_replace(
                ['{{ modelName }}', '{{ className }}', '{{ permissionConst }}', '{{ rules }}'],
                [$namespaceModel, $className, $permissionConst, $rulesString],
                $stub
            );

            $dir = app_path("Http/Requests/{$namespaceModel}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $path = "{$dir}/{$className}.php";
            File::put($path, $stub);

            $this->command->info("Request created: $path");
        } catch (Exception $e) {
            throw ($e);
        }
    }

    private function generateModel($modelName, $fields)
    {
        try {
            $stub = $this->getStubContent('Model.stub');

            $fillable = collect($fields)
                ->pluck('name')
                ->map(fn($f) => "'$f'")
                ->implode(', ');

            $relations = '';
            $imports = [];

            foreach ($fields as $field) {

                if (Str::endsWith($field['name'], '_id')) {

                    $relationName = Str::camel(str_replace('_id', '', $field['name']));
                    $relatedModel = Str::studly($relationName);

                    // Collect unique import
                    $imports[$relatedModel] = "use App\\Models\\{$relatedModel};";

                    $relations .= <<<EOD

    public function {$relationName}()
    {
        return \$this->belongsTo({$relatedModel}::class);
    }

EOD;
                }
            }


            $importLines = implode("\n", $imports);


            $stub = str_replace(
                ['{{ modelName }}', '{{ imports }}', '{{ fillable }}', '{{ relations }}'],
                [$modelName, $importLines, $fillable, trim($relations)],
                $stub
            );

            $path = app_path("Models/{$modelName}.php");
            File::put($path, $stub);

            $this->command->info("Model created: $path");
        } catch (Exception $e) {
            throw ($e);
        }
    }


    private function generateMigration($modelName, $fields)
    {
        try {
            $stub = $this->getStubContent('Migrations.stub');

            $tableName = Str::plural(Str::snake($modelName));
            $fieldLines = '';

            foreach ($fields as $field) {
                $name = $field['name'];
                $type = $field['type'];
                $nullable = isset($field['nullable']) && $field['nullable'];
                $default = $field['default'] ?? null;


                if (Str::endsWith($name, '_id')) {
                    $relation = str_replace('_id', '', $name);
                    $relatedTable = Str::plural($relation);
                    $line = "\$table->foreignId('{$name}')->constrained('{$relatedTable}')";
                } elseif ($type === 'integer') {
                    $line = "\$table->unsignedBigInteger('{$name}')";
                } else {
                    $line = "\$table->{$type}('{$name}')";
                }


                if ($nullable) {
                    $line .= "->nullable()";
                }


                if (!is_null($default)) {
                    $defaultValue = is_string($default) ? "'{$default}'" : $default;
                    $line .= "->default({$defaultValue})";
                }

                $fieldLines .= "            {$line};\n";
            }

            $stub = str_replace(
                ['{{ tableName }}', '{{ fields }}'],
                [$tableName, trim($fieldLines)],
                $stub
            );

            $timestamp = now()->format('Y_m_d_His');
            $fileName = "{$timestamp}_create_{$tableName}_table.php";
            $path = database_path("migrations/{$fileName}");

            File::put($path, $stub);
            $this->command->info("Migration created: $path");
        } catch (Exception $e) {
            throw ($e);
        }
    }


    private function generateResource($modelName, $fields)
    {
        try {

            $stub = $this->getStubContent('Resource.stub');
            $resourceFields = [
                "id" => '$this->id',

            ];
            $className =  Str::studly($modelName) . "Resource";
            $namespaceModel = Str::studly($modelName);

            foreach ($fields as $field) {
                $name = $field['name'];

                if (Str::endsWith($name, '_id')) {
                    $relation = Str::camel(str_replace('_id', '', $name));
                    $relationModel = ucfirst($relation);
                    $resource = Str::studly($relation) . 'Resource';

                    $imports[] = "use App\Http\Resources\\{$relationModel}\\{$resource};";

                    $resourceFields[$relation] = "new {$resource}(\$this->whenLoaded('{$relation}'))";
                } elseif (!in_array($name, ['id', 'created_at', 'updated_at'])) {
                    $resourceFields[$name] = "\$this->{$name}";
                }
            }
            $resourceFields = array_merge($resourceFields, [
                'created_at' => '$this->created_at',
                'updated_at' => '$this->updated_at',
            ]);

            $fieldsString = collect($resourceFields)
                ->map(fn($value, $key) => "'$key' => $value")
                ->implode(",\n            ");


            $imports = implode("\n", $imports);


            $stub = str_replace(
                ['{{ namespace }}', '{{ className }}', '{{ fields }}', '{{ imports }}'],
                [$namespaceModel, $className, $fieldsString, $imports],
                $stub
            );

            $dir = app_path("Http/Resources/{$namespaceModel}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $path = "{$dir}/{$className}.php";
            File::put($path, $stub);

            $this->command->info("Resource created: $path");
        } catch (Exception $e) {
            throw $e;
        }
    }




    private  function generateController($modelName)
    {
        try {
            $stub = $this->getStubContent('Controller.stub');

            $modelVariable = Str::camel($modelName);

            $stub = str_replace(
                ['{{ modelName }}', '{{ modelVariable }}'],
                [$modelName, $modelVariable],
                $stub
            );

            $dir = app_path("Http/Controllers/{$modelName}");
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $className = "{$modelName}Controller";

            $path = "{$dir}/{$className}.php";
            File::put($path, $stub);


            $this->command->info("Controller created: $path");
        } catch (Exception $e) {
            throw $e;
        }
    }


    private  function convertRulesArrayToString(array $rulesArray): string
    {
        try {
            $lines = [];
            $first = 1;
            foreach ($rulesArray as $field => $rules) {
                $rulesJoined = implode("', '", $rules);
                if ($first) {
                    $lines[] = "'{$field}' => ['{$rulesJoined}'],";
                    $first = 0;
                } else {

                    $lines[] = "            '{$field}' => ['{$rulesJoined}'],";
                }
            }

            return implode("\n", $lines);
        } catch (Exception $e) {
            throw $e;
        }
    }


    private function generateFactory($modelName, $fields)
    {
        $stub = $this->getStubContent('Factory.stub');

        $namespaceModel = Str::studly($modelName);
        $className = "{$namespaceModel}Factory";

        $definitions = [];

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $field['type'];
            $default = $field['default'] ?? null;

            if (Str::endsWith($name, '_id')) {
                $relation = Str::studly(str_replace('_id', '', $name));
                $imports[] = "use App\\Models\\{$relation}\\{$resource};";
                $definitions[] = "'{$name}' => {$relation}::factory(),";
            } elseif (!is_null($default)) {
                $value = is_string($default) ? "'{$default}'" : $default;
                $definitions[] = "'{$name}' => {$value},";
            } else {
                $definitions[] = "'{$name}' => " . $this->getFakerForType($type) . ",";
            }
        }

        $definitionsString = implode("\n            ", $definitions);
        $imports = implode("\n", $imports);

        $stub = str_replace(
            ['{{ modelName }}', '{{ className }}', '{{ definitions }}', '{{ imports }}'],
            [$namespaceModel, $className, $definitionsString, $imports],
            $stub
        );

        $path = database_path("factories/{$className}.php");
        File::put($path, $stub);
        $this->command->info("factory created: $path");
    }


    private function getFakerForType(string $type): string
    {
        return match ($type) {
            'string' => '$this->faker->sentence',
            'text' => '$this->faker->paragraph',
            'integer' => '$this->faker->randomNumber()',
            'boolean' => '$this->faker->boolean',
            'date' => '$this->faker->date()',
            'datetime' => '$this->faker->dateTime()',
            'email' => '$this->faker->unique()->safeEmail',
            'name' => '$this->faker->name',
            default => "'sample'",
        };
    }

    private function generateTests($modelName)
    {
        $stub = $this->getStubContent('Test.stub');;

        $className = Str::studly($modelName);
        $route = Str::plural(Str::kebab($modelName));

        $stub = str_replace(
            ['{{ modelName }}', '{{ route }}'],
            [$className, $route],
            $stub
        );

        $testDir = base_path("tests/Feature/{$className}");
        if (!File::exists($testDir)) {
            File::makeDirectory($testDir, 0755, true);
        }

        $filePath = "{$testDir}/{$className}Test.php";
        File::put($filePath, $stub);
        $this->command->info("test file created: $filePath");
    }

    private function generateRoutes($modelName)
    {
        $resourceName = Str::plural(Str::kebab($modelName));
        $controllerName = "{$modelName}Controller";

        $importStatement = "use App\\Http\\Controllers\\{$modelName}\\{$controllerName};";

        $routeEntry = "Route::apiResource('{$resourceName}', {$controllerName}::class);";

        $routeFile = base_path('routes/api.php');

          // Ensure the routes directory and file exist
          if (!File::exists($routeFile)) {
            File::ensureDirectoryExists(base_path('routes'));
            File::put($routeFile, "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\n");
            $this->command->info("routes/api.php file created.");
        }

        $contents = File::get($routeFile);


        if (!Str::contains($contents, $importStatement)) {
            $lines = explode("\n", $contents);
            $insertIndex = 0;
    
            // Find last use statement to insert after
            foreach ($lines as $index => $line) {
                if (Str::startsWith(trim($line), 'use ')) {
                    $insertIndex = $index + 1;
                }
            }
    
            array_splice($lines, $insertIndex, 0, $importStatement);
            $contents = implode("\n", $lines);
            File::put($routeFile, $contents);
            $this->command->info("Import added for {$controllerName} in routes/api.php");
        }

        if (!Str::contains($contents, $controllerName)) {
            File::append($routeFile, "\n" . $routeEntry);
            $this->command->info("API route added for {$modelName} in routes/api.php");
        } else {
            $this->command->warn("Route for {$modelName} already exists in routes/api.php");
        }
    }

    public function getStubContent(string $stubName): string
    {
        try {
           
            if (File::exists(resource_path('crud-generator-stubs/' . $stubName))) {
                
                return File::get(resource_path('crud-generator-stubs/' . $stubName));
            } else {
                
                $stubPath = realpath(__DIR__ . '/../Stubs/' . $stubName);
                
                if (!File::exists($stubPath)) {
                    throw new Exception("Stub file not found: " . $stubName);
                }

                return File::get($stubPath);
            }
        } catch (Exception $e) {
            throw new Exception("Error getting stub content: " . $e->getMessage());
        }
    }

}
