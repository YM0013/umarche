<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    //laravelドキュメント→基礎→フォームリクエストバリデーションを確認
    public function rules()
    {
        return
            [
                'image' => 'image|mimes:jpg,jpeg,png|max:2048',
                'files.*.image' => 'image|mimes:jpg,jpeg,png|max:2048',
            ];
    }

    //laravelドキュメント→基礎→フォームリクエストバリデーションを確認
    //laravelドキュメント→基礎→フォームリクエストバリデーション→エラーメッセージのカスタマイズを確認
    public function messages()
    {
        return
            [
                'image' => '指定されたファイルが画像ではありません。',
                'mimes' => '指定された拡張子（jpg/jpeg/png）ではありません。',
                'max' => 'ファイルサイズは2MB以内にしてください。',
            ];
    }
}
