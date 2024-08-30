<?php

namespace App\Helpers;

use App\Models\Workout;
use Illuminate\Support\Str;

/**
 * Helper Class full of helpful functions
 **/
class Helper
{
    /**
     * Generates a random token string.
     **/
    public static function generateToken(string $key)
    {
        $arrToken = [
            'key' => $key,
            'token' => Str::random(60),
        ];
        $token = json_encode($arrToken);
        return base64_encode($token);
    }

    /**
     * Generates a slug for the provided model that hasn't yet been used,
     * increments an int when the slug is taken
     **/
    public static function generateSlug($model, $text)
    {
        $slug = Str::slug($text, '-');
        $i = 1;
        while ($model::where('slug', $slug)->first()) {
            $slug = Str::slug($text . ' ' . $i, '-');
            $i++;
        }

        return $slug;
    }

    /**
     * Calculates the times of a workout if different from default
     **/
    public static function calculateExerciseTime($array)
    {
        foreach ($array as $exercise) {
            if ($exercise->pivot->store_workout_type == null) {
                continue;
                $exercise->duration += $exercise->rest_value;
            } elseif ($exercise->pivot->store_workout_type == 'duration') {
                $exercise->duration = $exercise->pivot->custom_duration;
                $exercise->duration += $exercise->pivot->custom_rest;
            } elseif ($exercise->pivot->store_workout_type == 'setsReps') {
                $exercise->duration = $exercise->duration_per_rep * $exercise->pivot->custom_sets * $exercise->pivot->custom_reps;
                $exercise->duration += $exercise->pivot->custom_rest * $exercise->pivot->custom_sets;
            }
        }

        return $array;
    }

    public static function sortCustomWorkoutDurations(
        $exercise,
        Workout $workout,
        $workout_section_id,
        $admin = false
    ): array {
        if ($admin) {
            $exercise = (object)$exercise;
            $exercise->pivot = (object)$exercise->pivot;

            return [
                'workout_section_id' => $workout_section_id,
                'exercise_id' => $exercise->id,
                'workout_id' => $workout->id,
                'store_workout_type' => isset($exercise->pivot->store_workout_type) ? $exercise->pivot->store_workout_type : null,
                'custom_duration' => isset($exercise->pivot->custom_duration) ? $exercise->pivot->custom_duration : null,
                'custom_sets' => isset($exercise->pivot->custom_sets) ? $exercise->pivot->custom_sets : null,
                'custom_reps' => isset($exercise->pivot->custom_reps) ? $exercise->pivot->custom_reps : null,
                'custom_rest' => isset($exercise->pivot->custom_rest) ? $exercise->pivot->custom_rest : null,
            ];
        }

        $customReps = $customSets = $customRest = $customDuration = null;
        if (array_key_exists('storeValues', $exercise)) {
            if ($exercise['storeValues']) {
                $customRest = $exercise['restValue'];
                if ($exercise['workoutStoreType'] == 'setsReps') {
                    $customSets = $exercise['setsValue'];
                    $customReps = $exercise['repsValue'];
                } else {
                    $customDuration = $exercise['customDuration'];
                }
            } else {
                $exercise['workoutStoreType'] = null;
            }
        } else {
            $exercise['workoutStoreType'] = null;
        }

        return [
            'workout_section_id' => $workout_section_id,
            'exercise_id' => $exercise['id'],
            'workout_id' => $workout->id,
            'store_workout_type' => $exercise['workoutStoreType'],
            'custom_duration' => $customDuration,
            'custom_sets' => $customSets,
            'custom_reps' => $customReps,
            'custom_rest' => $customRest,
        ];
    }

    public static function CurrencyPenceToPounds($value, $decimal = 0): string
    {
        return number_format(($value / 100), $decimal);
    }
}
