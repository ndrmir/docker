<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    private $curl;

    public function __construct(array $attributes = [])
    {
        $this->curl = app(Curl::class);
        parent::__construct($attributes);
    }

    protected $fillable = [
        'id',
        'name',
        'responsible_user_id',
        'group_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'closest_task_at',
        'is_deleted',
        'address',
        'email',
        'web',
        'phone',
        'account_id',
    ];

    public function getCompany($id)
    {
        $method = "/api/v4/companies/$id";
        $response = $this->curl->curlRequest($method);
        $companyData = [];

        foreach ($response as $key => $value) {
            if (($key !== 'custom_fields_values') && is_array($value)) {
                continue;
            }
            if (($key === 'custom_fields_values') && is_array($value)) {
                foreach ($value as $k => $v) {
                    $cVal = [];
                    $strValue = '';
                    foreach ($v['values'] as $item) {
                        $cVal[] = $item['value'];
                    }
                    $strValue = implode(', ', $cVal);
                    $field_code = \Str::lower($v['field_code']);
                    $companyData[$field_code] = $strValue;
                }
            } else {
                $companyData[$key] = $value;
            }
        }
        $companyData['created_at'] = \Carbon\Carbon::parse($companyData['created_at'])->format('d-m-Y H:i:s');
        $companyData['updated_at'] = \Carbon\Carbon::parse($companyData['updated_at'])->format('d-m-Y H:i:s');
        
        return $companyData;
    }
}
