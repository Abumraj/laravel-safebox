<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ParentIdBaseRequest extends FormRequest
{
    public ?File $parent = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $parentId = $this->input('parent_id');
    
        // Convert parent_id to integer if it's a string
        if (is_string($parentId)) {
            $parentId = (int) $parentId;
        }
    
        $this->parent = File::query()->where('id', $parentId)->first();
    
        if ($this->parent && !$this->parent->isOwnedBy(Auth::id())) {
            return false;
        }
    
        return true;
    }
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */

     public function rules(): array
     {
         return [
             'parent_id' => [
                 function ($attribute, $value, $fail) {
                     // Convert parent_id to integer if it's a string
                     if (is_string($value)) {
                         $value = (int) $value;
                     }
     
                     // Check existence based on the modified value
                     Rule::exists(File::class, 'id')
                         ->where(function (Builder $query) use ($value) {
                             return $query
                                 ->where('id', $value)
                                 ->where('is_folder', '=', '1')
                                 ->where('created_by', '=', Auth::id());
                         });
                        //  ->passes($attribute, $value) ?: $fail('The selected :attribute is invalid.');
                 }
             ]
         ];
     }
     






    // public function rules(): array
    // {
    //     return [
    //         'parent_id' => [
    //             Rule::exists(File::class, 'id')
    //                 ->where(function (Builder $query) {
    //                     return $query
    //                         ->where('is_folder', '=', '1')
    //                         ->where('created_by', '=', Auth::id());
    //                 })
    //         ]
    //     ];
    // }
}
