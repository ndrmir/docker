<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Company;
use App\Models\Contact;
use App\Models\LeadContact;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;

class LeadLinkController extends Controller
{
    /**
     * @var LeadLinkRepository
     */
    private $lead;
    private $company;
    private $contact;

    public function __construct()
    {
        $this->lead = app(Lead::class);
        $this->company = app(Company::class);
        $this->contact = app(Contact::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        //include base_path() . '/vendor/amocrm/amocrm-api-library/examples/get_token.php';
        //include base_path() . '/vendor/amocrm/amocrm-api-library/examples/get_token_1.php';
        return view('leadLink.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Флаг ошибки БД
        $summ = 0;

        // Получим все сделки
        $leadsData = $this->lead->getAllLead();

        // Внесем в базу
        foreach ($leadsData as $key => $value) {
            // dd($value);
            $item = Lead::updateOrCreate([
                'id'   => $value['id'],
             ], $value);
            $summ += (!$item) ? 1 : 0;
        }

        // Получим список связанных сущностей
        $linkData = $this->lead->getLeadLink($leadsData);

        //  Получим связвнные сущности, внесем в базу
        foreach ($linkData as $leadId => $value) {
            $dataContact = ['lead_id' => null, 'contact_id' => null];
            $dataCompany = ['company_id' => null];
            foreach ($value as $entityType => $entityId) {
                if ($entityType == 'companies') {
                    $company = $this->company->getCompany($entityId[0]);
                    $dataCompany['company_id'] = $company['id'];
                    $item = Company::updateOrCreate([
                        'id'   => $company['id'],
                     ], $company);
                    $summ += (!$item) ? 1 : 0;
                }
                if ($entityType == 'contacts') {
                    foreach ($entityId as $id) {
                        $contact = $this->contact->getContact($id);
                        $dataContact['contact_id'] = $contact['id'];
                        $dataContact['lead_id'] = $leadId;

                        $item = Contact::updateOrCreate([
                            'id'   => $contact['id'],
                        ], $contact);
                        $summ += (!$item) ? 1 : 0;

                        if ($dataContact['contact_id'] && $dataContact['lead_id']) {
                            $item = LeadContact::updateOrCreate([
                                'lead_id'   => $leadId,
                                'contact_id'   => $contact['id'],
                            ], $dataContact);
                            $summ += (!$item) ? 1 : 0;
                        }                        
                    }
                }
            }
            if ($dataCompany['company_id']) {
                $item = Lead::where('id', $leadId)->update($dataCompany);
            }

            $summ += (!$item) ? 1 : 0;
        }

        if (!$summ) {
            return redirect()
            ->route('leadLink.index', 1)
            ->with(['success' => 'Успешно сохранено']); // Отправляем в сессию
        } else {
            return back()
            ->withErrors(['msg' => 'Ошибка сохранения']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
