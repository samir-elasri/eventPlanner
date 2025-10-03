<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\Registration\RegistrationsResource;
use App\Http\Resources\Dashboard\Registration\RegistrationResource;
use App\Repositories\Dashboard\RegistrationRepository;

class RegistrationController extends Controller
{
    private $registrationRepository;

    public function __construct(RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }

    public function index()
    {
        return $this->userRepository->all();
    }

    public function create(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params);
    }

    public function show(Request $request)
    {
        return $this->userRepository->show($request->registration_id);
    }

    public function update(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params, $request->registration_id);
    }

    public function destroy(Request $request)
    {
        return $this->userRepository->destroy($request->registration_id);
    }
}
