<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PlayedAtLeastOneTrack implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     * Check if there is at least one track that have been played.
     *
     * @param  string $attribute
     * @param $tracks
     * @return bool
     */
    public function passes($attribute, $tracks)
    {
        foreach ($tracks as $track) {
            if (array_key_exists('played', $track)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No tracks were selected!';
    }
}
