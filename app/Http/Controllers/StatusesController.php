<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\LeadsController;
use App\Services\CandidatesService;
use App\Services\LeadsService;
use App\Services\TasksService;
use Carbon\Carbon;

class StatusesController extends Controller
{
    public function index(Request $req, LeadsService $ls, TasksService $t_srv, CandidatesService $cnd_srv)
    {
        $cResp = null;

        if ($req->action == 'setCandidateStatus') {
            if (isset($req->specialStatus)) {
                $cReq = $req;
                $cReq->id = $req->candidateId;

                $cResp = CandidateController::setStatusSpecial($cReq);

                if ($cResp->original['success'] == 'false') {
                    return $cResp;
                }
            } else {
                $cReq = $req;
                $cReq->id = $req->candidateId;
                $cReq->s = $req->status;
                $cReq->r = $req->comment;

                $cResp = CandidateController::setStatus($cReq);

                if ($cResp->original['success'] == 'false') {
                    return $cResp;
                }
            }
        } else
        if ($req->action == 'setLeadStatus') {
            $lReq = $req;
            $lReq->id = $req->leadId;

            $cResp = LeadsController::setStatus($lReq);

            if ($cResp->original['success'] == 'false') {
                return $cResp;
            }
        } else
        if ($req->action == 'createCandidateFromLead') {
            $lReq = $req;
            $lReq->id = $req->leadId;

            $cResp = $ls->createCandidate($req->leadId);

            if (isset($cResp->original) && $cResp->original['success'] == 'false') {
                return $cResp;
            }
        } else
        if ($req->action == 'setTaskStatus') {
            $comment = null;

            if ($req->status == '4' && $req->comment) {
                $comment = $req->comment;
            }

            $task = Task::find($req->taskId);

            $task->update([
                'status' => $req->status,
                'comment' => $comment,
            ]);

            if ($req->status == '2') {
                if ($task->task_group) {
                    Task::where('task_group', $task->task_group)
                        ->update([
                            'status' => 2,
                        ]);
                }

                $t_srv->createNextTaskFromTemplate($task->id);
            }
        } else
        if ($req->action == 'setLegalise') {
            $cnd_srv->setLegalise($req->candidateId, $req->status);
        }

        if (isset($req->taskId) && $req->action != 'setTaskStatus') {
            Task::find($req->taskId)->update(['status' => 2]);
        }

        if ($req->action == 'setLeadStatus') {
            $ls->distributeToUsers(true);
            // $server_seconds = Carbon::now()->second;

            // if ($server_seconds > 11 && $server_seconds < 55) {
            // }
        }

        if ($req->afterAction) {
            if ($req->afterAction == 'goToCandidateEditPage' && isset($cResp['candidate_id'])) {
                return response(array(
                    'success' => true,
                    'goTo' => '/candidate/add?id=' . $cResp['candidate_id'],
                ), 200);
            }
        }

        return response(array('success' => true), 200);
    }
}
