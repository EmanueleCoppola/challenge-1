<?php

namespace App\Http\Controllers;

use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
        $entries = Translation::all();

        return TranslationResource::collection($entries);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Translation $translation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Translation $translation)
    {
        //
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
