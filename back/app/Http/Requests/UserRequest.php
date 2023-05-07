<?php

namespace App\Http\Requests;

use App\Enums\JobTitleEnum;
use App\Rules\ChangeExistEmail;
use App\Rules\ParentUserExistsInTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $user = $this->route()->parameter('user');
        return match (request()->method()) {
            'POST' => [
                'name'      => ['required', 'string', 'max:255'],
                'surname'   => ['required', 'string', 'max:255'],
                'email'     => ['required', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:255', Rule::unique('users')],
                'job_title' => ['required', Rule::in(JobTitleEnum::JobTitle)],
                'phone'     => ['required', 'string', Rule::unique('users')],
                'note'      => ['nullable', 'string'],
                'parentId'  => ['nullable', 'integer', new ParentUserExistsInTable()]
            ],
            'PATCH' => [
                'name'      => ['sometimes', 'string', 'max:255'],
                'surname'   => ['sometimes', 'string', 'max:255'],
                'email'     => ['sometimes', 'email', 'regex:/(.+)@(.+)\.(.+)/i', 'max:255', Rule::unique('users')->ignore($user->id), new ChangeExistEmail($user)],
                'job_title' => ['sometimes', Rule::in(JobTitleEnum::JobTitle)],
                'phone'     => ['sometimes', 'string', Rule::unique('users')->ignore($user->id)],
                'note'      => ['nullable', 'string'],
                'parentId'  => ['nullable', 'integer', new ParentUserExistsInTable()]
            ]
        };
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                [
                'success' => false,
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}