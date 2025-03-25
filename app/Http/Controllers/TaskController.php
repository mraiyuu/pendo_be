<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Str;
use Validator;

class TaskController extends Controller
{
    public function createTask(Request $request): JsonResponse
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|exists:users,email',
                'title' => 'required|string',
                'description' => 'required|string',
                'due_date' => 'required|date_format:Y-m-d|after_or_equal:today',

            ]);

            $error = collect($validate->errors()->all())->first();
            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => '0',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            $user = User::where('email', $request->input('email'))->first();

            $task = Task::create([
                'task_id' => Str::uuid(),
                'user_id' => $user->user_id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'due_date' => $request->input('due_date'),
            ]);


            return response()->json(
                [
                    'responseCode' => 0,
                    'responseMessage' => 'task created sucessfully',
                    'task' => [
                        'task_id' => $task->task_id,
                        'title' => $task->title,
                        'description' => $task->description,
                    ],
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllTask(Request $request): JsonResponse
    {
        try {
            $validate = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,user_id',
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100',
            ]);

            $error = collect($validate->errors()->all())->first();
            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => '0',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            // Set default pagination parameters
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            $tasks = Task::where('user_id', $request->user_id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['task_id', 'title', 'description', 'due_date'], 'page', $page);

            if ($tasks->isEmpty()) {
                return response()->json([
                    'responseCode' => 0,
                    'responseMessage' => 'No tasks found',
                    'tasks' => []
                ], 200);
            }

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'Tasks retrieved successfully',
                'tasks' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'per_page' => $tasks->perPage(),
                    'total_pages' => $tasks->lastPage(),
                    'total_items' => $tasks->total()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllTasksForGuests(Request $request): JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);

            $tasks = Task::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'Tasks retrieved successfully',
                'tasks' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'per_page' => $tasks->perPage(),
                    'total_pages' => $tasks->lastPage(),
                    'total_items' => $tasks->total()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTask(Request $request): JsonResponse
    {
        try {
            $validate = Validator::make($request->all(), [
                'task_id' => 'required|exists:tasks,task_id',
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'due_date' => 'sometimes|date|after_or_equal:today',
            ]);

            $error = collect($validate->errors()->all())->first();
            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => '1',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            $task = Task::where('task_id', $request->task_id)->first();

            if (!$task) {
                return response()->json([
                    'responseCode' => 0,
                    'responseMessage' => 'No tasks found',
                    'tasks' => []
                ], 200);
            }
            $updateTask = $request->only(['title', 'description', 'due_date']);
            $task->fill($updateTask);
            $task->save();

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'Task updated successfully',
                'task' => [
                    'task_id' => $task->task_id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'due_date' => $task->due_date
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTask(Request $request): JsonResponse
    {
        try {
            $validate = Validator::make($request->all(), [
                'task_id' => 'required|exists:tasks,task_id',
            ]);

            $error = collect($validate->errors()->all())->first();
            if ($validate->fails()) {
                return response()->json([
                    'responseCode' => '1',
                    'responseMessage' => 'Validation failed',
                    'errorMessage' => $error,
                ], 422);
            }

            $task = Task::where('task_id', $request->task_id)->first();

            if (!$task) {
                return response()->json([
                    'responseCode' => 0,
                    'responseMessage' => 'No tasks found',
                    'tasks' => []
                ], 200);
            }

            $task->delete();

            return response()->json([
                'responseCode' => 0,
                'responseMessage' => 'task deleted successfully',
                'task' => [
                    'task_id' => $task->task_id,
                    'title' => $task->title
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 1,
                'errorMessage' => $e->getMessage()
            ], 500);
        }
    }
}
