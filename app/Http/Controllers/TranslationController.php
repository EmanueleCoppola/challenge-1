<?php

namespace App\Http\Controllers;

use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

/**
 * @group Endpoints v1
 *
 * APIs for managing translations
 *
 * @subgroup Translations
 * @subgroupDescription Translation management
 */
class TranslationController extends Controller
{
    /**
     * Get all the available translations
     *
     * @apiResourceCollection App\Http\Resources\TranslationResource
     * @apiResourceModel App\Models\Translation
     */
    public function index(): ResourceCollection
    {
        $entries = Translation::with([
            'labels'
        ])->all();

        return TranslationResource::collection($entries);
    }

    /**
     * Get the specified translation
     *
     * @urlParam key string required The key that references the translation. Example: main-text
     *
     * @apiResourceModel App\Models\Translation
     */
    public function show(Translation $translation): JsonResource
    {
        $translation->with([
            'labels'
        ]);

        return TranslationResource::make($translation);
    }

    /**
     * Create a single translation with its labels
     *
     * @bodyParam key string required The key that references the translation. Example: main-text
     *
     * @apiResourceModel App\Models\Translation
     */
    public function store(Request $request): JsonResource
    {
        $request->validate([
            'key' => ['string', 'required', 'max:255', 'unique:translations,key'],
        ]);

        DB::beginTransaction();

        $translation = Translation::create([
            'key' => $request->input('key')
        ]);

        DB::commit();

        return TranslationResource::make($translation);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Translation $translation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation)
    {
        //
    }
}
