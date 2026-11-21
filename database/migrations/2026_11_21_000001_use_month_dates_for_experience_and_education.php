<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the year-only columns with real dates so the site can show
 * "06/2021 — 03/2024" and count experience in months.
 *
 * Existing rows keep their years: a start year becomes January of that
 * year and an end year becomes December, so no period gets shorter.
 */
return new class extends Migration
{
    private const TABLES = ['experiences', 'education'];

    public function up(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->date('start_date')->nullable()->after('start_year');
                $table->date('end_date')->nullable()->after('start_date'); // null = ongoing
            });

            DB::table($name)->orderBy('id')->each(function (object $row) use ($name) {
                DB::table($name)->where('id', $row->id)->update([
                    'start_date' => $row->start_year.'-01-01',
                    'end_date' => filled($row->end_year) ? $row->end_year.'-12-01' : null,
                ]);
            });

            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn(['start_year', 'end_year']);
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->string('start_year', 4)->nullable()->after('start_date');
                $table->string('end_year', 4)->nullable()->after('start_year');
            });

            DB::table($name)->orderBy('id')->each(function (object $row) use ($name) {
                DB::table($name)->where('id', $row->id)->update([
                    'start_year' => $row->start_date ? substr($row->start_date, 0, 4) : null,
                    'end_year' => $row->end_date ? substr($row->end_date, 0, 4) : null,
                ]);
            });

            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn(['start_date', 'end_date']);
            });
        }
    }
};
