<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    // Gets the exercises from each section attached to the provided focus
    public function sections(int $focus)
    {
        $sections = [];

        $attributes = [
            'created_human',
            'excerpt',
            'video',
            'steps',
            'image_path',
        ];

        $sections['warmups'] = Exercise::join('_pivot_exercise_sections', 'exercise_id', 'exercises.id')
            ->join('_pivot_exercise_focus', '_pivot_exercise_focus.exercise_id', 'exercises.id')
            ->where('_pivot_exercise_focus.focus_id', $focus)
            ->where('_pivot_exercise_sections.section_id', 1)
            ->where('exercises.paid', 0)
            ->select('_pivot_exercise_sections.id as pivot_id',
                'exercises.id as id', 'exercises.image_path', 'exercises.name', 'exercises.duration',
                'exercises.duration_per_rep')
            ->get();

        foreach ($sections['warmups'] as $warmup) {
            $warmup->makeHidden($attributes);
        }

        $sections['training'] = Exercise::join('_pivot_exercise_sections', 'exercise_id', 'exercises.id')
            ->join('_pivot_exercise_focus', '_pivot_exercise_focus.exercise_id', 'exercises.id')
            ->where('_pivot_exercise_focus.focus_id', $focus)
            ->where('_pivot_exercise_sections.section_id', 2)
            ->where('exercises.paid', 0)
            ->select('_pivot_exercise_sections.id as pivot_id',
                'exercises.id as id', 'exercises.image_path', 'exercises.name', 'exercises.duration',
                'exercises.duration_per_rep')
            ->get();

        foreach ($sections['training'] as $training) {
            $training->makeHidden($attributes);
        }

        $sections['cooldowns'] = Exercise::join('_pivot_exercise_sections', 'exercise_id', 'exercises.id')
            ->join('_pivot_exercise_focus', '_pivot_exercise_focus.exercise_id', 'exercises.id')
            ->where('_pivot_exercise_focus.focus_id', $focus)
            ->where('_pivot_exercise_sections.section_id', 3)
            ->where('exercises.paid', 0)
            ->select('_pivot_exercise_sections.id as pivot_id',
                'exercises.id as id', 'exercises.image_path', 'exercises.name', 'exercises.duration',
                'exercises.duration_per_rep')
            ->get();

        foreach ($sections['cooldowns'] as $cooldown) {
            $cooldown->makeHidden($attributes);
        }

        return response()->json($sections);
    }
}
