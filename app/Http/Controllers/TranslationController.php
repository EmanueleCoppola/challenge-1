<?php

namespace App\Http\Controllers;

use App\Enums\LabelLanguage;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * APIs for managing translations
 */
class TranslationController extends Controller
{
    /**
     * Get all the available translations
     */
    public function index(): ResourceCollection
    {
        $entries = Translation::with([
            'labels'
        ])->get();

        return TranslationResource::collection($entries);
    }

    /**
     * Get the specified translation
     */
    public function show(Translation $translation): JsonResource
    {
        $translation->with([
            'labels'
        ]);

        return TranslationResource::make($translation);
    }

    private function _validateLabels(): callable
    {
        return function ($attribute, $value, $fail) {
            // TODO: move this to a validation helper

            $expectedKeys = LabelLanguage::values();
            $missingKeys = array_diff($expectedKeys, array_keys($value));

            if (!empty($missingKeys)) {
                $fail('The ' . $attribute . ' must have keys: ' . implode(', ', $missingKeys));
            }
        };
    }

    /**
     * Create a single translation with its labels
     */
    public function store(Request $request): JsonResource
    {
        $attributes = $request->validate([
            'key' => ['string', 'required', 'max:255', 'unique:translations,key'],
            'labels' => ['required', 'array', $this->_validateLabels()]
        ]);

        DB::beginTransaction();

        $translation = Translation::create([
            'key' => $request->input('key')
        ]);

        $translation->labels()->createMany(
            collect(
                data_get($attributes, 'labels')
            )
                ->map(function(string $value, string $key) {
                    return [
                        'lang' => $key,
                        'text' => $value
                    ];
                })
                ->values()
                ->toArray()
        );

        DB::commit();

        // if an exception gets thrown the root handler will catch it
        // and will render the correct json

        return TranslationResource::make($translation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Translation $translation)
    {
        // this is a temporary workaround for the demo purposes only
        // to fix it properly, the web server should have the APFD extension enabled
        // https://stackoverflow.com/questions/50691938/patch-and-put-request-does-not-working-with-form-data
        $request->request->remove('_method');

        // load relationships
        $translation->load([
            'labels'
        ]);

        $attributes = $request->validate([
            ...(
                collect(LabelLanguage::values())->mapWithKeys(function(string $lang) {
                    return [$lang => 'required'];
                })->toArray()
            ),
            '*' => [
                'required',
                'string',
                'max:255',
                function(string $language, string $text, $fail) {
                    if (!in_array($language, LabelLanguage::values())) {
                        $fail('The "' . $language . '" language is not supported!');
                    }
                }
            ]
        ]);

        $labels = $translation->labels->keyBy('lang');

        DB::beginTransaction();

        foreach (array_keys($attributes) as $lang) {
            $label = $labels->get($lang);

            $newText = $attributes[$lang];

            if ($label) {
                $label->update([
                    'text' => $attributes[$lang]
                ]);

                continue;
            }

            $translation->labels()->create([
                'lang' => $lang,
                'text' => $newText
            ]);
        }

        DB::commit();

        return TranslationResource::make($translation->refresh());
    }

    /**
     * Remove the specified resource from the DB.
     */
    public function destroy(Translation $translation): JsonResponse
    {
        $translation->delete();

        return response()->json(status: 204);
    }
}
