<?php

declare(strict_types=1);

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use App\Constants\Permissions\PostPermissions;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return $this->user()?->can(PostPermissions::UPDATE_POST['name']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'nullable'],
            'content' => ['text', 'nullable'],
            'user_id' => ['integer', 'nullable', 'exists:users,id'],
            'owner_id' => ['integer', 'nullable', 'exists:owners,id'],
            'is_restricted' => ['boolean', 'nullable'],
        ];
    }
}
