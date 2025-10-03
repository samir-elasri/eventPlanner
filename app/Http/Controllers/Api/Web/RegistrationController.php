<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Web\Registration\RegistrationsResource;
use App\Repositories\Web\RegistrationRepository;

class RegistrationController extends Controller
{
    private $registrationRepository;

    public function __construct(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/web/registrations",
     *     summary="Get user registrations",
     *     @OA\Response(response=200, description="Registrations retrieved successfully")
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Registration::class);
        return $this->registrationRepository->all();
    }

    /**
     * @OA\Post(
     *     path="/api/web/registrations/join",
     *     summary="Join an event",
     *     @OA\Response(response=201, description="Event joined successfully")
     * )
     */
    public function join(Request $request)
    {
        $this->authorize('create', Registration::class);
        return $this->registrationRepository->join($request->event_id);
    }

    /**
     * @OA\Delete(
     *     path="/api/web/registrations/cancel",
     *     summary="Cancel event registration",
     *     @OA\Response(response=200, description="Registration cancelled successfully")
     * )
     */
    public function cancel(Request $request)
    {
        $registration = Registration::findOrFail($request->registration_id);
        $this->authorize('delete', $registration);
        return $this->registrationRepository->cancel($request->registration_id);
    }
}
