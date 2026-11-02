<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuditionService;
use App\Services\MessageService;


class ApplicationsController extends Controller
{
    /**
     * @var AuditionService
     */
    private $auditionService;

    public function __construct(AuditionService $auditionService)
    {
        $this->auditionService = $auditionService;
    }

    public function getProfileApplications(Request $request)
    {
        $request->validate(['page' => 'integer']);
        return response()->json(
            $this->auditionService->getProfileApplications($request->get('page', 1))
        );
    }

    public function getProfileShortlists(Request $request)
    {
        $request->validate(['page' => 'integer']);
        return response()->json(
            $this->auditionService->getProfileShortlists($request->get('page', 1))
        );
    }

    public function getProfileAccepted(Request $request)
    {
        $request->validate(['page' => 'integer']);
        return response()->json(
            $this->auditionService->getProfileAccepted($request->get('page', 1))
        );
    }

    public function apply($auditionId)
    {
        $status = $this->auditionService->apply($auditionId);

        // if ($status) {
        //     MessageService::syncUserMessages(auth()->user()->profile->id, $auditionId, statusCondition: ['for_applicants' => true]);
        // }

        return response()->json([
            'status' => $status,
            'message' => $this->auditionService->getMessages()
        ], 200);
    }
}
