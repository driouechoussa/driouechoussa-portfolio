<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Support\PortfolioData;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('pages.projects', [
            'appData' => PortfolioData::make([
                'projects' => ProjectResource::collection(Project::ordered()->get()),
            ]),
        ]);
    }

    public function show(Project $project): View
    {
        // The full list is needed for the previous / next project links.
        return view('pages.project-details', [
            'project' => $project,
            'appData' => PortfolioData::make([
                'projects' => ProjectResource::collection(Project::ordered()->get()),
            ]),
        ]);
    }
}
