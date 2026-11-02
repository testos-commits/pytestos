<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Import the Str facade
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{

    protected static $usedAttributeTypes = [];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $attributeTypes = [
            'Skill' => [
                'acting' => ['ar' => 'التمثيل', 'en' => 'Acting'],
                'dancing' => ['ar' => 'الرقص', 'en' => 'Dancing'],
                'singing' => ['ar' => 'الغناء', 'en' => 'Singing']
            ],
            'Occupation' => [
                'engineer' => ['ar' => 'مهندس', 'en' => 'Engineer'],
                'doctor' => ['ar' => 'طبيب', 'en' => 'Doctor'],
                'teacher' => ['ar' => 'معلم', 'en' => 'Teacher']
            ],
            'Spoken Language' => [
                'arabic' => ['ar' => 'العربية', 'en' => 'Arabic'],
                'english' => ['ar' => 'الإنجليزية', 'en' => 'English'],
                'spanish' => ['ar' => 'الإسبانية', 'en' => 'Spanish']
            ],
            'Hobby' => [
                'painting' => ['ar' => 'الرسم', 'en' => 'Painting'],
                'fishing' => ['ar' => 'صيد الأسماك', 'en' => 'Fishing'],
                'cooking' => ['ar' => 'الطهي', 'en' => 'Cooking']
            ],
            'Programming Language' => [
                'java' => ['ar' => 'جافا', 'en' => 'Java'],
                'python' => ['ar' => 'بايثون', 'en' => 'Python'],
                'javascript' => ['ar' => 'جافاسكريبت', 'en' => 'JavaScript']
            ]
        ];




        // Filter out attribute types that have already been used
        $availableAttributeTypes = array_diff_key($attributeTypes, array_flip(static::$usedAttributeTypes));

        // If all attribute types have been used, reset the array
        if (empty($availableAttributeTypes)) {
            static::$usedAttributeTypes = [];
            $availableAttributeTypes = $attributeTypes;
        }

        // Randomly pick one attribute type from the available ones
        $type = $this->faker->randomElement(array_keys($availableAttributeTypes));

        // Mark this attribute type as used
        static::$usedAttributeTypes[] = $type;
        $slug = Str::slug($type);

        return [
            'slug' => $slug,
            'name' => $type,
            'name_ar' => $type . ' Arabic',
            'options' => $attributeTypes[$type],
        ];
    }
}
