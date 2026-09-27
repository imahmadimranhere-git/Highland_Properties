<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleSlug;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            /*
             * Company address only. The pattern accepts any ending
             * (.com, .pk, .test), so moving the live domain later needs no
             * code change — but a personal gmail cannot be used to create a
             * staff account.
             */
            'email' => [
                'required', 'email', 'max:150',
                'regex:/@highlandproperties\.[a-z]{2,}$/i',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:120'],
            /*
             * Only consultant accounts can be created here. The existing
             * super admin keeps its own role: editing that account passes
             * the role it already has, which is why the current value is
             * allowed through as well.
             */
            'role_id' => [
                'required',
                Rule::in(array_filter([
                    Role::where('slug', RoleSlug::SalesConsultant->value)->value('id'),
                    $user?->role_id,
                ])),
            ],
            'is_active' => ['boolean'],

            // Required when creating; on edit the password is changed separately.
            'password' => [$user ? 'prohibited' : 'required', 'confirmed', Password::min(8)->letters()->numbers()],

            'target_deals' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            // A new account is always a consultant, whatever the form sent.
            'role_id' => $this->route('user')
                ? $this->input('role_id')
                : Role::where('slug', RoleSlug::SalesConsultant->value)->value('id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'email.regex' => 'Use a company address ending in @highlandproperties.',
            'role_id.in' => 'Only sales consultant accounts can be created here.',
        ];
    }
}
