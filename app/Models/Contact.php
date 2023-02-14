<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
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
        'first_name',
        'last_name',
        'responsible_user_id',
        'group_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'closest_task_at',
        'is_deleted',
        'is_unsorted',
        'phone',
        'email',
        'account_id',
        // 'contact_lead'
    ];

    public function leads()
    {
        return $this->belongsToMany(Lead::class);
    }

    public function getContact($id)
    {
        $method = "/api/v4/contacts/$id";
        $response = $this->curl->curlRequest($method);

        $contactData = [];

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
                    $contactData[$field_code] = $strValue;
                }
            } else {
                $contactData[$key] = $value;
            }
        }
        $contactData['created_at'] = \Carbon\Carbon::parse($contactData['created_at'])->format('d-m-Y H:i:s');
        $contactData['updated_at'] = \Carbon\Carbon::parse($contactData['updated_at'])->format('d-m-Y H:i:s');

        return $contactData;
    }
}
