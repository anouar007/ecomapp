<?php

namespace Tests\Feature;

use App\Services\OfficialProductImageService;
use Tests\TestCase;

class ProductStudio4kSearchTest extends TestCase
{
    protected OfficialProductImageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OfficialProductImageService::class);
    }

    public function test_clean_studio_search_query_strips_condition_and_descriptors(): void
    {
        $this->assertEquals(
            'FUJIFILM X-T4',
            $this->service->cleanStudioSearchQuery('FUJIFILM X-T4 (Occasion) – Mirrorless Camera')
        );

        $this->assertEquals(
            'Sony Alpha 7 IV',
            $this->service->cleanStudioSearchQuery('Sony Alpha 7 IV - Boîtier Nu')
        );

        $this->assertEquals(
            'Canon EOS R5',
            $this->service->cleanStudioSearchQuery('Canon EOS R5 (Neuf) - Appareil photo hybride')
        );

        $this->assertEquals(
            'DJI Mic 2',
            $this->service->cleanStudioSearchQuery('DJI Mic 2 (2 TX + 1 RX)')
        );
    }

    public function test_is_camera_body_recognizes_camera_model_series(): void
    {
        $this->assertTrue($this->service->isCameraBody('FUJIFILM X-T4'));
        $this->assertTrue($this->service->isCameraBody('Sony Alpha 7 IV'));
        $this->assertTrue($this->service->isCameraBody('Canon EOS R5'));
        $this->assertTrue($this->service->isCameraBody('Nikon Z6 II'));
        $this->assertFalse($this->service->isCameraBody('SmallRig Cage pour Sony A7 IV'));
        $this->assertFalse($this->service->isCameraBody('Batterie Sony NP-FZ100'));
    }

    public function test_find_4k_candidates_returns_actual_products_not_cars(): void
    {
        $candidates = $this->service->find4kCandidates('FUJIFILM X-T4 (Occasion) – Mirrorless Camera', 'Fujifilm', 800, 6);

        $this->assertNotEmpty($candidates);
        $this->assertLessThanOrEqual(6, count($candidates));

        $vehicleTerms = ['porsche', 'ferrari', 'lamborghini', 'race car', 'motorsport', 'gt4', 'circuit', 'racetrack'];

        foreach ($candidates as $cand) {
            $titleLower = strtolower($cand['title']);
            foreach ($vehicleTerms as $vt) {
                $this->assertStringNotContainsString(
                    $vt,
                    $titleLower,
                    "Candidate title '{$cand['title']}' contains vehicular term '$vt'"
                );
            }
            $this->assertNotEmpty($cand['url']);
            $this->assertGreaterThanOrEqual(1200, $cand['width']);
        }
    }
}
