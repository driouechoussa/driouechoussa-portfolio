from django.shortcuts import render

# Create your views here.


def projectsRendering(request):
    return render(request , 'layouts/projects.html')