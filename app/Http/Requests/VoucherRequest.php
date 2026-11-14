<?php

/*
 * @author Harris Marfel <hrace009@gmail.com>
 * @link https://youtube.com/c/hrace009
 * @copyright Copyright (c) 2022.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'max_redeem' => 'required|integer|min:1',
            'name' => 'required|min:3|max:255',
            'item_id' => 'required|numeric|min:0',			
            'count' => 'required|numeric|min:1',
            'max_count' => 'required|numeric|min:1',
            'protection_type' => 'numeric|min:0',
            'expire_date' => 'numeric',
        ];
    }
}
