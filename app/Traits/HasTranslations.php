<?php

namespace App\Traits;

trait HasTranslations
{

    public function initializeHasTranslations()
    {   
        if (isset($this->translatable)) {
            $translatableCasts = [];

            foreach ($this->translatable as $translatable) {
                $translatableCasts[$translatable] = 'array';
            }
            $this->casts = array_merge($this->casts, $translatableCasts);
        }
    }
    public function __get($key)
    {
        
        if (request()->is('api/*')) {

            if (in_array($key, $this->translatable)) {
                return $this->localTranslate($key);
            }
        }

        if (request()->is('admin/*')) {
            if (in_array($key, $this->translatable)) {
                return $this->localTranslate($key, config('app.fallback_locale'));
            }
        }

        return parent::__get($key);
    }

    public function localTranslate($key, $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $property = parent::__get($key);

        if (isset($property[$locale])) {
            return $property[$locale];
        }

        if (isset($property[config('app.fallback_locale')])) {
            return $property[config('app.fallback_locale')];
        }

        return null;
    }
}
