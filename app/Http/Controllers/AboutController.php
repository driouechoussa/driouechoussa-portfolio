<?php

namespace App\Http\Controllers;

use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\SkillGroupResource;
use App\Http\Resources\ToolResource;
use App\Models\Education;
use App\Models\Experience;
use App\Models\SkillGroup;
use App\Models\Tool;
use App\Support\ExperienceYears;
use App\Support\PortfolioData;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'appData' => PortfolioData::make([
                'skills' => SkillGroupResource::collection(SkillGroup::ordered()->with('skills')->get()),
                'tools' => ToolResource::collection(Tool::ordered()->get()),
                'experience' => ExperienceResource::collection(Experience::ordered()->take(6)->get()),
                'education' => EducationResource::collection(Education::ordered()->get()),
            ]),
            'yearsOfExperience' => ExperienceYears::total(),
        ]);
    }
}
