<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SecureFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain',
            ],
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'A file is required.',
            'file.max' => 'The file size cannot exceed 10MB.',
            'file.mimes' => 'The file must be a valid image or document.',
            'file.mimetypes' => 'The file type is not allowed.',
            'description.max' => 'The description cannot exceed 500 characters.',
            'category.max' => 'The category cannot exceed 100 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');
                
                // Check file extension against MIME type
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'];
                $extension = strtolower($file->getClientOriginalExtension());
                
                if (!in_array($extension, $allowedExtensions)) {
                    $validator->errors()->add('file', 'File extension not allowed.');
                }
                
                // Check for suspicious file content
                $this->validateFileContent($file, $validator);
            }
        });
    }

    /**
     * Validate file content for security
     */
    private function validateFileContent($file, $validator): void
    {
        $content = file_get_contents($file->getPathname());
        
        // Check for executable content
        $suspiciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/eval\(/i',
            '/exec\(/i',
            '/system\(/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $validator->errors()->add('file', 'File contains suspicious content.');
                break;
            }
        }
        
        // Check file header for image files
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $this->validateImageHeader($file, $validator);
        }
    }

    /**
     * Validate image file headers
     */
    private function validateImageHeader($file, $validator): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getPathname());
        
        $validHeaders = [
            'jpg' => ["\xFF\xD8\xFF"],
            'jpeg' => ["\xFF\xD8\xFF"],
            'png' => ["\x89\x50\x4E\x47"],
            'gif' => ["\x47\x49\x46\x38"],
            'webp' => ["\x52\x49\x46\x46"],
        ];
        
        if (isset($validHeaders[$extension])) {
            $valid = false;
            foreach ($validHeaders[$extension] as $header) {
                if (substr($content, 0, strlen($header)) === $header) {
                    $valid = true;
                    break;
                }
            }
            
            if (!$valid) {
                $validator->errors()->add('file', 'Invalid image file format.');
            }
        }
    }
}