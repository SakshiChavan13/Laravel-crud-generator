# Laravel CRUD Generator

A simple and customizable Laravel package to generate CRUD operations from a JSON template.  

---

##  Under Development

This package is not yet available on Packagist. To use it, add it manually to your Laravel project's `composer.json`.

---

## 🛠️ Installation

### Step 1: Add the package to your Laravel project

In your Laravel project's `composer.json`, add the following:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/your-username/your-package-repository"
    }
],
```

### Step 2: Require the package
Run:
```
composer require sakshi-chavan/laravel-crud-generator
```

### Step 3: Generate CRUD template
Run:
```
php artisan make:crud-template {jsonFileName}
```

### Step 4: Edit the generated template
E.g Post.json template
```
{
    "model": "Post",
    "fields": [
        {
            "name": "title",
            "type": "string",
            "nullable":false
        },
        {
            "name": "content",
            "type": "text",
            "nullable": false
        },
        {
            "name": "user_id",
            "type": "integer",
            "nullable": false
        },
        {
            "name": "owner_id",
            "type": "integer",
            "nullable": false
        },

        {
            "name": "is_restricted",
            "type": "boolean",
            "nullable": false,
            "default": 1
        }
    ]
}
```

### Step 5: Generate CRUD Command
Run:
```
php artisan generate:crud {jsonFileName}
```


## 🧩 What It Does

This package allows you to automatically generate full CRUD functionality in Laravel using a single JSON template.

### Currently Supports:

- ✅ Model generation  
- ✅ Migration generation  
- ✅ Form Request classes along with accurate permissions to authorize
- ✅ Route entries  
- ✅ Controller with CRUD logic  
- ✅ Resource generation
- ✅ Permissions file
- ✅ Factory generation
- ✅ Basic Crud Test cases generation

 ⚠️ **Note**: All files and respective code are generated based on stub templates.  
> Imports for related models (like relationships) may not always be accurate.  
> Please review and adjust them after generation.

