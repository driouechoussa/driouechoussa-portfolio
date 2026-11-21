<?php

namespace App\Http\Controllers;

use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProjectResource;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Support\ExperienceYears;
use App\Support\PortfolioData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'appData' => PortfolioData::make([
                'projects' => ProjectResource::collection(Project::ordered()->take(6)->get()),
                'education' => EducationResource::collection(Education::ordered()->get()),
                'experience' => ExperienceResource::collection(Experience::ordered()->take(6)->get()),
                'posts' => PostResource::collection(Post::published()->take(3)->get()),
            ]),
            'yearsOfExperience' => ExperienceYears::total(),
        ]);
    }
}
