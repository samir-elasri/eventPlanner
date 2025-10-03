<?php

namespace App\Repositories\Dashboard;

use App\Models\Registration;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\Dashboard\Registration\RegistrationsResource;
use App\Http\Resources\Dashboard\Registration\RegistrationResource;

class RegistrationRepository
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $registrations =  Registration::paginate(15);
            $data = [
                'registrations' => RegistrationsResource::collection($registrations),
                'pagination' => [
                    'total' => $registrations->total(),
                    'per_page' => $registrations->perPage(),
                    'current_page' => $registrations->currentPage(),
                    'total_pages' => $registrations->lastPage(),
                    'next_page_url' => $registrations->nextPageUrl(),
                    'prev_page_url' => $registrations->previousPageUrl()
                ]
            ];
            return $data;
        } catch(Exception $e){
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a newly created resource in storage.
     */
    public function create(Request $request)
    {
        try {
            $registration = Registration::create([
                "event_id" => $request->input('event_id'),
                "user_id" => $request->input('user_id'),
                "status" => $request->input('status', 'pending'),
                "joined_at" => now()
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Event registration created successfully',
                'data' => new RegistrationResource($registration)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
            'status' => 500,
            'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($registration_id)
    {
        try {
            $registration = Registration::findOrFail($registration_id);
            return [
                'data' => new RegistrationResource($registration)
            ];
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Event Registration Not Found!";
            return [
                'error' => $errorMessage,
                'status' => 404
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $registration_id)
    {
        try {
            $registration = Registration::findOrFail($registration_id);

            $registration->update($request->only([
                'event_id',
                'user_id',
                'status',
                'joined_at'
            ]));

            return response()->json([
                'status' => 200,
                'message' => 'Event registration updated successfully',
                'data' => new RegistrationResource($registration)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($registration_id)
    {
        try {
            $registration = Registration::findOrFail($registration_id);
            $registration->delete();
            return response()->json([
                'status' => 200,
                'message' => "Event registration deleted successfully",
            ], 200);
        } catch (ModelNotFoundException $e) {
            $errorMessage = "Event registration not found!";
            return response()->json([
                'status' => 404,
                'message' => $errorMessage,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
