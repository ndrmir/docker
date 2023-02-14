<?php

namespace App\Models;

use App\Models\Curl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
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
        'price',
        'responsible_user_id',
        'group_id',
        'status_id',
        'pipeline_id',
        'loss_reason_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'closed_at',
        'closest_task_at',
        'is_deleted',
        'custom_fields_values',
        'score',
        'account_id',
        'labor_cost',
        'is_price_computed',
        'company_id',
        ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_lead', 'lead_id', 'contact_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Получаем все сделки

    public function getAllLead()
    {
        $method = '/api/v4/leads';

        $response = $this->curl->curlRequest($method);
        $leads = $response['_embedded']['leads'];
        $leadsData = [];
        foreach ($leads as $key => $value) {
            foreach ($value as $ke => $val) {
                if (($ke !== 'custom_fields_values') && is_array($val)) {
                    continue;
                }
                if (($ke === 'custom_fields_values') && is_array($val)) {
                    foreach ($val as $k => $v) {
                        $cVal = [];
                        $strValue = '';
                        foreach ($v['values'] as $item) {
                            $cVal[] = $item['value'];
                        }
                        $strValue = implode(', ', $cVal);
                        $field_code = \Str::lower($v['field_code']);
                        $companyData[$response['id']][$field_code] = $strValue;
                    }
                } else {
                    $leadsData[$value['id']][$ke] = $val;
                }
            }
            $leadsData[$value['id']]['company_id'] = null;
            $leadsData[$value['id']]['created_at'] = \Carbon\Carbon::parse($leadsData[$value['id']]['created_at'])->format('d-m-Y H:i:s');
            $leadsData[$value['id']]['updated_at'] = \Carbon\Carbon::parse($leadsData[$value['id']]['updated_at'])->format('d-m-Y H:i:s');
        }

        return $leadsData;
    }

    // Получаем связанные сущности

    public function getLeadLink($leadsData)
    {
        $linkData = [];

        foreach ($leadsData as $id => $value) {
            $method = "/api/v4/leads/$id/links";

            $response = $this->curl->curlRequest($method);
            $links = $response['_embedded']['links'];

            foreach ($links as $v) {
                if ($v['to_entity_id'] && $v['to_entity_type']) {
                    $entityId = $v['to_entity_id'];
                    $entityType = $v['to_entity_type'];

                    $linkData[$id][$entityType][] = $entityId;
                }
            }
        }
        return $linkData;
    }
}
