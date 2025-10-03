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

    public function index()
    {
        return $this->registrationRepository->all();
    }

    public function join(Request $request)
    {
        return $this->registrationRepository->join($request->event_id);
    }

    public function cancel(Request $request)
    {
        return $this->registrationRepository->cancel($request->registration_id);
    }
}
