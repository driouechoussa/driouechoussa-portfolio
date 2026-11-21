<?php

use App\Models\Education;
use App\Models\Experience;
use App\Support\ExperienceYears;
use Illuminate\Support\Carbon;

function position(string $start, ?string $end): Experience
{
    return Experience::create([
        'company' => 'Acme',
        'role' => ['en' => 'Developer'],
        'location' => ['en' => 'Remote'],
        'description' => ['en' => 'Work.'],
        'start_date' => $start,
        'end_date' => $end,
    ]);
}

it('falls back to the configured years when no experience exists', function () {
    config(['portfolio.stats.years' => 9]);

    expect(ExperienceYears::total())->toBe(9);
});

it('counts the months worked, including the end month', function () {
    position('2020-01-01', '2022-12-01'); // 36 months

    expect(ExperienceYears::total())->toBe(3);
});

it('does not count overlapping months twice and skips gaps', function () {
    position('2018-01-01', '2019-12-01'); // 24 months
    position('2019-06-01', '2020-05-01'); // overlaps, adds 5 months
    position('2022-01-01', '2022-12-01'); // after a gap, 12 months

    expect(ExperienceYears::total())->toBe(3); // 41 months
});

it('counts an ongoing position up to today', function () {
    position('2021-06-01', null);

    expect(ExperienceYears::total(Carbon::parse('2026-05-15')))->toBe(5) // 60 months
        ->and(ExperienceYears::total(Carbon::parse('2026-04-15')))->toBe(4); // 59 months
});

it('shows the number of years on the home and about pages', function (string $route) {
    position('2019-01-01', null);
    $years = ExperienceYears::total();

    $this->get(route($route))
        ->assertOk()
        ->assertViewHas('yearsOfExperience', $years)
        ->assertSee('data-count="'.$years.'"', false);
})->with(['home', 'about']);

it('sends experience and education dates as month/year', function () {
    position('2021-06-01', null);
    Education::create([
        'school' => ['en' => 'University'],
        'degree' => ['en' => 'Master'],
        'field' => ['en' => 'Computer science'],
        'description' => ['en' => 'Studies.'],
        'start_date' => '2016-09-01',
        'end_date' => '2018-07-01',
    ]);

    $data = $this->get(route('about'))->viewData('appData');

    expect($data['experience'][0])->toMatchArray(['start' => '06/2021', 'end' => null])
        ->and($data['education'][0])->toMatchArray(['start' => '09/2016', 'end' => '07/2018']);
});
