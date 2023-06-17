<?php

use Illuminate\Support\Facades\Validator;
use App\Jobs\SendError;

if (!function_exists('validate_request')) {
    function validate_request($request, array $rules, $customMsg = [])
    {
        $validator = Validator::make($request->all(), $rules, $customMsg);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                return $message;
            }
        } else {
            return '';
        }
    }
}
