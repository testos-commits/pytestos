<?php

namespace App\Traits;

trait HasFilamentTranslatableFields
{

    
    /**
     * @param $fieldName
     * @param $inputType
     * @param bool $notRequired
     * @param null $callback can be used to add more options to the input, passed as fn($input) => $input->default('123') for example
     * @return array
     */

    public function makeTranslatableField($fieldName, $inputType, $notRequired = false, $callback = null)
    {
        $inputs = [];

        foreach (config('app.supported_locales') as $locale) {

            $input = $inputType::make($fieldName . '_' . $locale);

            if ($callback) {
                $callback($input);
            }

            if (!$notRequired && $locale == config('app.fallback_locale')) {
                $input->required();
            }
            $inputs[] = $input;
        }
        return $inputs;
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->setMutatedData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->setMutatedData($data);
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {

        return $this->getMutatedData($data);
    }

    protected function getMutatedData(array $data): array
    {
        if (!isset($this->translatableFields)) {
            throw new \Exception('You must define the makeTranslatableFields method in your resource');
        }

        foreach ($this->translatableFields as $key => $field) {
            $baseField = $field;

            if (is_array($field)) {
                $baseField = $key;
            }

            if (isset($data[$baseField])) {
                foreach (config('app.supported_locales') as $locale) {
                    if (isset($data[$baseField][$locale])) {
                        $data[$baseField . '_' . $locale] = $data[$baseField][$locale];
                    }
                }
                unset($data[$baseField]);
            }
        }


        return $data;
    }
    protected function setMutatedData(array $data): array
    {
        if (!isset($this->translatableFields)) {
            throw new \Exception('You must define the makeTranslatableFields method in your resource');
        }

        $dataKeys = array_keys($data);

        foreach (config('app.supported_locales') as $locale) {

            foreach ($this->translatableFields as $k => $field) {

                if (is_array($field)) { // takes into consideration that the field might be a repeatable field
                    if (in_array($k, $dataKeys)) {
                        foreach ($field as $f) {
                            foreach ($data[$k] as $key => $value) {
                                if (in_array($f . '_' . $locale, array_keys($value))) {
                                    if ($value[$f . '_' . $locale] !== null) {
                                        $data[$k][$key][$locale] = $value[$f . '_' . $locale];
                                    }
                                }
                                unset($data[$k][$key][$f . '_' . $locale]);
                            }
                            unset($data[$f . '_' . $locale]);
                        }
                    }
                } else {
                    if (in_array($field . '_' . $locale, $dataKeys)) {
                        if ($data[$field . '_' . $locale] !== null) {
                            $data[$field][$locale] = $data[$field . '_' . $locale];
                        }
                    }
                    unset($data[$field . '_' . $locale]);
                }
            }
        }

        return $data;
    }
}
