<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use App\Models\Audition;
use App\Models\Attribute;
use App\Models\Profile;
use App\Models\Project;
use Database\Seeders\ForYouTestSeeder;
use App\Services\AuditionService;
use Illuminate\Testing\Assert;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Log;

class ForYouAuditionsTest extends TestCase
{
    use  RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public $user;
    public $attributes;
    public $profile;
    public $audition;

    public function test_user_with_no_meta_has_empty_for_you(): void
    {
        $user = $this->setUser('1');

        $response = $this->withHeaders(['Accept' => 'application/json'])->get('api/auditions');

        $response->assertJsonPath('forYou', []);
    }

    public function test_user_with_wrong_meta_gets_empty_for_you(): void
    {
        $user = $this->setUser('5');

        $result = $this->withHeaders(['Accept' => 'application/json'])->get('api/auditions');

        $result->assertJsonPath('forYou', []);
    }

    public function test_user_with_one_meta_gets_for_you(): void
    {
        $user = $this->setUser('2');


        $response = $this->withHeaders(['Accept' => 'application/json'])->get('api/auditions');

        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->has('forYou', function (AssertableJson $json) use ($user) {
                $forYouItems = $json->toArray();
                $count = count($forYouItems);
                foreach ($forYouItems as $item) {
                    if ($this->assertForYouItemMatchesProfileMeta($item['meta'], $user->profile->meta)) {
                        $count--;
                    }
                }
                if ($count !== 0) {
                    $this->fail('Not all forYou items match profile meta');
                }

                $this->assertTrue(true);
            });
        });
    }

    public function setUser($number)
    {
        $user = User::where('email', $number . '@' . $number . '.com')
            ->with('profile')
            ->first();

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    protected function assertForYouItemMatchesProfileMeta(array $forYouMeta, array $profileMeta)
    {
        foreach ($forYouMeta as $key => $values) {
            if (isset($profileMeta[$key])) {
                foreach ($values as $value) {
                    if (in_array($value, $profileMeta[$key])) {
                        return true;
                    }
                }
            }
        }

        // $this->fail('No matching meta found in profile for forYou item');
    }
}
