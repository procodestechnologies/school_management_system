<?php

namespace Modules\Curriculum\Actions;

use Modules\Curriculum\Models\Curriculum;

/**
 * Creating and editing a curriculum, in one place - shared by the Livewire
 * screen and the controller endpoint.
 */
class SaveCurriculum
{
    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // The name is free text; the system is what the grading scale is
            // actually chosen by.
            'system' => 'required|in:844,cbc',
            // Which of CBC's two scales - the four-band rubric or KJSEA's
            // eight levels. Ignored on 8-4-4, which has only the one.
            'grading_scheme' => 'nullable|in:'.implode(',', array_keys(Curriculum::SCHEMES)),
            'status' => 'required|in:active,dismissed',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function handle(array $data, int $institutionId, ?Curriculum $curriculum = null): Curriculum
    {
        $isCbc = $data['system'] === 'cbc';

        $payload = [
            'institution_id' => $institutionId,
            'name' => $data['name'],
            'system' => $data['system'],
            // Stored only where it means something. Switching a curriculum
            // to 8-4-4 clears it rather than leaving a CBC scale behind on
            // a curriculum that no longer has one.
            'grading_scheme' => $isCbc
                ? ($data['grading_scheme'] ?? Curriculum::SCHEME_RUBRIC)
                : null,
            'status' => $data['status'],
        ];

        if ($curriculum) {
            $curriculum->update($payload);

            return $curriculum;
        }

        return Curriculum::create($payload);
    }
}
