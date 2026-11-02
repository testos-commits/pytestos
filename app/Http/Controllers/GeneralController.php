<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeneralService;
use App\Models\Faq;
use App\Http\Resources\FaqResource;
use App\Mail\ContactForm;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class GeneralController extends Controller
{
    private $generalService;

    public function __construct()
    {
        $this->generalService = new GeneralService();
    }

    public function getGeneralData()
    {
        Log::channel('debug')->info('Fetching general data');
        $generalData = $this->generalService->getGeneralData();
        return response()->json($generalData);
    }

    public function sendContact(Request $request)
    {

        $data = request()->validate([
            'name' => 'required|string',
            'phone' => "nullable|string",
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        Mail::to(env('MAIL_CONTACT_ADDRESS'))->send(new ContactForm(
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['message']
        ));

        return response()->json(['message' => 'Contact form sent successfully']);
    }

    public function getFaqs()
    {
        Log::channel('debug')->info('Fetching FAQs');
        $faqs = Faq::all();
        return response()->json(FaqResource::collection($faqs));
    }

    public function getAppState()
    {   
        Log::channel('debug')->info('Fetching app state');
        return response()->json($this->generalService->getCurrentAppState());
    }
}
