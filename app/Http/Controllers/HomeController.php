<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\Certificates;
use App\Models\Cv;


class HomeController extends Controller
{
    function Index()
    {
        $projects = Projects::limit(8)->get(); 
        $certificates = Certificates::limit(4)->get();
        $cv = Cv::all();

        return view('index' , [
            'projects' => $projects,
            'certificates' => $certificates,
            'cv' => $cv
        ]);
    }
}
