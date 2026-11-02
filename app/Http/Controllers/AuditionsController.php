<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuditionRequest;
use App\Models\UserSubscription;
use App\Services\AuditionService;
use Illuminate\Support\Facades\Log;
use App\Traits\RequiresSubscription;

class AuditionsController extends Controller
{
    use RequiresSubscription;
    /**
     * @var AuditionService
     */
    private $auditionService;


    public function __construct(AuditionService $auditionService)
    {
        $this->auditionService = $auditionService;
    }

    public function index(Request $request)
    {
        Log::channel('debug')->info('Fetching auditions');
        $request->validate(['page' => 'integer']);

        return response()->json(
            $this->auditionService->getUserCountryAuditions($request->get('page', 1))
        );
    }

    public function forYou(Request $request)
    {
        Log::channel('debug')->info('Fetching for you');

        $request->validate(['page' => 'integer']);

        $subscriptionActive = $this->hasAccessToPremiumFeatures(auth()->user()->id);

        return response()->json(
            $subscriptionActive ?
                $this->auditionService->getForUser($request->get('page', 1))
                : $subscriptionActive
        );
    }


    public function show($id)
    {
        return response()->json($this->auditionService->getById($id));
    }

    public function create(AuditionRequest $auditionRequest)
    {
        $this->auditionService->create($auditionRequest->validated());
    }
}
