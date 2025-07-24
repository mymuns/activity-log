<?php

namespace ActivityLog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ActivityLog\Models\ActivityLog;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RevertController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        if (!Gate::allows('activity-log.revert')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $log = ActivityLog::findOrFail($id);

        if (!$log->loggable_type || !$log->loggable_id || !$log->changes) {
            return response()->json(['message' => 'This log cannot be reverted.'], 400);
        }

        $modelClass = $log->loggable_type;
        $model = $modelClass::find($log->loggable_id);

        if (!$model) {
            return response()->json(['message' => 'Model not found.'], 404);
        }

        $originalData = $log->changes['old'] ?? null;

        if (!$originalData) {
            return response()->json(['message' => 'No revertible data found.'], 400);
        }

        $model->fill($originalData);
        $model->save();

        return response()->json([
            'message' => 'Model successfully reverted.',
            'model' => $model->fresh(),
        ]);
    }
}
