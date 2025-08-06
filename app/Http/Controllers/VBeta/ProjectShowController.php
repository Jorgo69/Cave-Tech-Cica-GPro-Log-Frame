<?php

namespace App\Http\Controllers\VBeta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectShowController extends Controller
{
    public function index(string $projectId){

        return view('v_beta.project-show', [
            'projectId' => $projectId,
        ]);
    }
}
