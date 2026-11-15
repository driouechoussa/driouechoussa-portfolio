<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\SkillGroup;
use App\Models\Tool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the portfolio content from database/seeders/data/portfolio.json.
 * Safe to run more than once: rows are matched by slug / key / name.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('seeders/data/portfolio.json')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        DB::transaction(function () use ($data) {
            $this->seedProjects($data['projects']);
            $this->seedPosts($data['posts']);
            $this->seedEducation($data['education']);
            $this->seedExperience($data['experience']);
            $this->seedSkills($data['skills']);
            $this->seedTools($data['tools']);
        });
    }

    private function seedProjects(array $projects): void
    {
        foreach ($projects as $order => $p) {
            Project::updateOrCreate(['slug' => $p['id']], [
                'category' => $p['category'],
                'title' => $p['title'],
                'preview' => $p['preview'],
                'icon' => $p['icon'],
                'accent' => $p['accent'],
                'year' => $p['year'],
                'client' => $p['client'] ?? null,
                'duration' => $p['duration'],
                'role' => $p['role'],
                'stack' => $p['stack'],
                'description' => $p['desc'],
                'overview' => $p['overview'],
                'challenge' => $p['challenge'],
                'solution' => $p['solution'],
                'results' => $p['results'],
                'sort_order' => $order,
            ]);
        }
    }

    private function seedPosts(array $posts): void
    {
        foreach ($posts as $p) {
            Post::updateOrCreate(['slug' => $p['id']], [
                'category' => $p['category'],
                'image' => $p['image'] ?? null,
                'title' => $p['title'],
                'excerpt' => $p['excerpt'],
                'body' => $p['body'],
                'read_time' => $p['readTime'],
                'published_at' => $p['date'],
            ]);
        }
    }

    private function seedEducation(array $education): void
    {
        Education::query()->delete();

        foreach ($education as $order => $e) {
            Education::create([
                'school' => $e['school'],
                'degree' => $e['degree'],
                'field' => $e['field'],
                'description' => $e['desc'],
                'start_year' => $e['start'],
                'end_year' => $e['end'],
                'sort_order' => $order,
            ]);
        }
    }

    private function seedExperience(array $experience): void
    {
        Experience::query()->delete();

        foreach ($experience as $order => $x) {
            Experience::create([
                'role' => $x['role'],
                'company' => $x['company'],
                'location' => $x['location'],
                'description' => $x['desc'],
                'start_year' => $x['start'],
                'end_year' => $x['end'],
                'sort_order' => $order,
            ]);
        }
    }

    private function seedSkills(array $groups): void
    {
        foreach ($groups as $order => $g) {
            $group = SkillGroup::updateOrCreate(['key' => $g['group']], [
                'icon' => $g['icon'],
                'sort_order' => $order,
            ]);

            $group->skills()->delete();

            foreach ($g['items'] as $i => [$name, $level]) {
                $group->skills()->create([
                    'name' => $name,
                    'level' => $level,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    private function seedTools(array $tools): void
    {
        foreach ($tools as $order => $t) {
            Tool::updateOrCreate(['name' => $t['name']], [
                'icon' => $t['icon'],
                'sort_order' => $order,
            ]);
        }
    }
}
