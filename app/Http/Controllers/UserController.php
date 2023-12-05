<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Create a new user
     * takes a JSON object with the following fields (* = required):
     * *first_name, *last_name, *email, *password, address
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateUserRequest $request)
    {
        try {
            $user = User::create($request->only(["first_name", "last_name", "email", "password"]));

            if ($request->has("address")) {
                $user->userDetails()->create(["address" => $request->input("address")]);
            }

            $userWithDetails = User::where("email", $request->input("email"))->with("userDetails")->first();

            return response()->json(["success" => true, "data" => $userWithDetails], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Update an existing user
     * takes a JSON object with the following fields (* = required):
     * *first_name, *last_name, *email, *password, address
     * @param UpdateUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(["success" => false, "message" => "User not found"], Response::HTTP_NOT_FOUND);
            }

            $user->update($request->only(["first_name", "last_name", "email", "password"]));

            if ($request->has("address")) {
                $user->userDetails()->updateOrCreate(["user_id" => $user->id], ["address" => $request->input("address")]);
            } elseif ($user->userDetails) {
                $user->userDetails->delete();
            }

            return response()->json(["success" => true, "data" => $user], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a user
     * @param string $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($userId)
    {
        try {
            $user = User::find($userId);

            if ($user === null) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "User with id {$userId} not found"
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }

            if ($user->delete() === false) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Couldn't delete the user with id {$userId}"
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            return response()->json([
                "success" => true,
                "message" => "User with id {$userId} deleted successfully",
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List all users
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        try {
            $users = User::with("userDetails")->get();

            return response()->json(["success" => true, "data" => $users], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

