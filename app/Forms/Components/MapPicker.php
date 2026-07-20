<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class MapPicker extends Field
{
    protected string $view = 'forms.components.map-picker';

    protected float $defaultLat = -7.5083; // titik tengah Kedungreja / Gumelar
    protected float $defaultLng = 108.7871;
    protected int $zoom = 13;

    public function defaultCenter(float $lat, float $lng, int $zoom = 13): static
    {
        $this->defaultLat = $lat;
        $this->defaultLng = $lng;
        $this->zoom = $zoom;
        return $this;
    }

    public function getDefaultLat(): float { return $this->defaultLat; }
    public function getDefaultLng(): float { return $this->defaultLng; }
    public function getZoom(): int { return $this->zoom; }
}
