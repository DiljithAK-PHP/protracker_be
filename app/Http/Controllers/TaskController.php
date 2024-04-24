<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        return Task::where('user_id', $userId)->get();
    }

    public function create()
    {
        // 
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required',
            'task_description' => 'required',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $task = Task::create($data);
        if ($task) {
            return response()->json(['status' => 1, 'message' => 'Task created successfully.'], 201);
        } else {
            return response()->json(['status' => 0, 'message' => 'Failed to create task.'], 409);
        }
    }

    public function show($id)
    {
        $task = Task::find($id);
        if ($task) {
            return response()->json(['status' => 1, 'message' => 'Success.', 'data' => $task], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Data not found.'], 400);
        }
    }

    public function edit(Task $task)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'task_name' => 'required',
            'task_description' => 'required',
        ]);
        $data = $request->all();
        $task = Task::find($id);
        if ($task) {
            if ($task->update($data)) {
                return response()->json(['status' => 1, 'message' => 'Task updated successfully.'], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Failed to update task.']);
            }
        } else {
            return response()->json(['status' => 0, 'Data not found.'], 400);
        }
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if ($task) {
            if ($task->delete()) {
                return response()->json(['status' => 1, 'message' => 'Task deleted successfully.'], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Failed to delete task.'], 400);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Data not found.'], 400);
        }
    }

    public function changeStatus($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->status = ($task->status == 1) ? 2 : 1;
            if ($task->save()) {
                return response()->json(['status' => 1, 'message' => 'Task status changed successfully.'], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Failed to change task status'], 400);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Data not found.'], 400);
        }
    }
}
