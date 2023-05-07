<?php

namespace App\Http\Controllers;

use App\Enums\JobTitleEnum;
use App\Http\Requests\UserRequest;
use App\Models\Relation;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UsersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            EmployeeService::getRelatedEmployers($request->query('employer_id')),
            Response::HTTP_OK
        );
    }

    public function destroy(User $user): JsonResponse
    {
        return response()->json(
            $user->deleteRecursive(),
            Response::HTTP_NO_CONTENT
        );
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = User::create($request->all());
        EmployeeService::createRelation($user, $request->input('parentId'));
        return response()->json($user, Response::HTTP_OK);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $user->update($request->all());
        $request->filled('parentId') && Relation::whereEmployeeId($user->id)
            ->updateOrCreate(
                ['employee_id' => $user->id],
                ['employer_id' => $request->input('parentId')]
            );
        return response()->json(
            $user,
            Response::HTTP_OK
        );
    }
}