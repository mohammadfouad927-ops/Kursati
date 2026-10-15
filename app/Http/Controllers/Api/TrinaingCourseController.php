<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Training_Course;
use App\Http\Requests\StoreTraining_CourseRequest;
use App\Http\Requests\UpdateTraining_CourseRequest;
use App\Http\Resources\TrainingCourseResource;
use App\Models\Subject;
use App\Traits\ApiResponse;

class TrinaingCourseController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $training_courses = Training_Course::with('subjects')->paginate(10);
        return $this->successResponse(TrainingCourseResource::collection($training_courses));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTraining_CourseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Training_Course $training_course)
    {
        // there is 2 way to transform the model to resource
        // First way
        // return response()->json(['data' => new TrainingCourseResource($training_course)]);
        // Second way and You can omit(delete) the resource class name if it matches the model's default resource
        // return response()->json(['data' => $training_course->toResource(TrainingCourseResource::class)]);
        return $this->successResponse($training_course->load('subjects')->toResource(TrainingCourseResource::class));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTraining_CourseRequest $request, Training_Course $training_Course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Training_Course $training_Course)
    {
        //
    }
}
