# Laravel CRUD Generator

A simple and customizable Laravel package to generate CRUD operations from a JSON template.  

---

## 🛠️ Installation

### Step 1: Require the package
Run:
```
composer require sakshi-chavan/laravel-crud-generator
```

### Step 2: Generate CRUD template
Run:
```
php artisan make:crud-template {jsonFileName}
```

### Step 3: Edit the generated template
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

### Step 4: Generate CRUD Command
Run:
```
php artisan generate:crud {jsonFileName}
```


## 🧩 What It Does

This package allows you to automatically generate full CRUD functionality in Laravel using a single JSON template.

### Currently Supports:

- ✅ Model generation  
- ✅ Migration generation  
- ✅ Form Request classes 
- ✅ Route entries  
- ✅ Controller with CRUD logic  
- ✅ Resource generation
- ✅ Factory generation
- ✅ Basic Crud Test cases generation(PEST)

 ⚠️ **Note**: All files and respective code are generated based on stub templates.  
> Imports for related models (like relationships) may not always be accurate.  
> Please review and adjust them after generation.

