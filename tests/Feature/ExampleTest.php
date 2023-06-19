<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    const TEST_TRANSLATION = [
        'key' => 'test-key',
        'labels' => [
            'it' => 'Stringa di test',
            'en' => 'Test string',
            'fr' => 'Chaîne de texte'
        ]
    ];

    /**
     * A basic test example.
     */
    public function test_translation_store_api(): void
    {
        $response = $this->post(route('translation.store'), self::TEST_TRANSLATION);

        $response->assertStatus(201);
        $response->assertContent(
            json_encode(self::TEST_TRANSLATION)
        );

        $newTranslation = Translation::with(['labels'])->where('key', '=', self::TEST_TRANSLATION['key'])->first();
        $newLabels = $newTranslation->labels->keyBy('lang');

        $this->assertModelExists($newTranslation);

        foreach (array_keys(self::TEST_TRANSLATION['labels']) as $lang) {
            $newLabel = $newLabels->get($lang);

            $this->assertModelExists($newLabel);
            $this->assertSame($newLabel->lang, $lang);
            $this->assertSame($newLabel->text, self::TEST_TRANSLATION['labels'][$lang]);
        }
    }
}
