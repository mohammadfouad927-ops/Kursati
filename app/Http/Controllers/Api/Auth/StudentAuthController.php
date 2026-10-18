<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentLoginRequest;
use App\Http\Requests\StudentRegisterRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Models\Student;
use App\Traits\ApiResponse;
use App\Http\Resources\StudentResource;

class StudentAuthController extends Controller
{
    use ApiResponse;

    public function login(StudentLoginRequest $request){
        $student = $request->authenticate();

        $token = $student->createToken('student_auth_token')->plainTextToken;

        return $this->successResponse([
            'token'   => $token,
            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'email' => $student->email,
            ]
        ], 'Login successful');
    }
    
    public function register(StudentRegisterRequest $request){

        $validateData = $request->validated();

        if($request->hasFile('avater')){

            $validateData['profile_picture'] = $request->file('avater')->store('profilePictures','public');
        }

        $student = Student::create($validateData);

        return $this->successResponse($student->toResource(), 'Successful Register');

    }

    public function profile(){   
        return $this->successResponse(auth('student')->user()->toResource(), 'Student profile retrieved');
    }

    public function update(StudentUpdateRequest $request)
    {
        $validateData = $request->validated();

        if($request->hasFile('avater')){
            $validateData['profile_picture'] = $request->file('avater')->store('profilePictures','public');
        }
        
        $student = Student::find(auth('student')->id);
        $student->update($validateData);

        return $this->successResponse($student, 'Student profile updated successfully');

    }
}
