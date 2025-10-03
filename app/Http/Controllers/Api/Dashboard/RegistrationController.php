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

    /**
     * @OA\Get(
     *     path="/api/dashboard/registrations",
     *     summary="Get all registrations",
     *     @OA\Response(response=200, description="Registrations retrieved successfully")
     * )
     */
    public function index()
    {
        return $this->userRepository->all();
    }

    /**
     * @OA\Post(
     *     path="/api/dashboard/registrations",
     *     summary="Create a new registration",
     *     @OA\Response(response=201, description="Registration created successfully")
     * )
     */
    public function create(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params);
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/registrations/{registration_id}",
     *     summary="Get registration by ID",
     *     @OA\Response(response=200, description="Registration retrieved successfully")
     * )
     */
    public function show(Request $request)
    {
        return $this->userRepository->show($request->registration_id);
    }

    /**
     * @OA\Put(
     *     path="/api/dashboard/registrations/{registration_id}",
     *     summary="Update registration",
     *     @OA\Response(response=200, description="Registration updated successfully")
     * )
     */
    public function update(Request $request)
    {
        $params = $request->all();
        return $this->userRepository->create($params, $request->registration_id);
    }

    /**
     * @OA\Delete(
     *     path="/api/dashboard/registrations/{registration_id}",
     *     summary="Delete registration",
     *     @OA\Response(response=200, description="Registration deleted successfully")
     * )
     */
    public function destroy(Request $request)
    {
        return $this->userRepository->destroy($request->registration_id);
    }
}
