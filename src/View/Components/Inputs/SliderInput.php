<?php

namespace WireUi\View\Components\Inputs;

use Exception;
use Illuminate\Support\{Collection, Str, Stringable};
use Illuminate\View\ComponentAttributeBag;
use WireUi\View\Components\FormComponent;

class SliderInput extends FormComponent
{
    protected Collection $names;
    protected Collection $values;

    protected string $size = 'sm';

    public function __construct(
        public bool $md = false,
        public bool $lg = false,
        public bool $range = false,
        public bool $errorless = false,
        public bool $showStops = false,
        public bool $hideTooltip = false,
        public ?string $label = null,
        public ?string $hint = null,
        public ?string $cornerHint = null,
        Collection|array|null $names = null,
        Collection|array|null $values = null,
    ) {
        $this->size = $this->md ? 'md' : $this->size;
        $this->size = $this->lg ? 'lg' : $this->size;

        $this->names = collect($names)->values();
        $this->values = collect($values)->values();
    }

    protected function getView(): string
    {
        return $this->range
            ? 'wireui::components.inputs.slider.range'
            : 'wireui::components.inputs.slider.index';
    }

    private function validateConfig(): void
    {
        if ($this->names->isNotEmpty() && $this->names->count() !== 2) {
            throw new Exception('The names array must contain exactly two items.');
        }

        if ($this->values->isNotEmpty() && $this->values->count() !== 2) {
            throw new Exception('The values array must contain exactly two items.');
        }
    }

    public function formatDataSliderRange(ComponentAttributeBag $attributes, string $type)
    {
        $this->validateConfig();

        $copy = clone $attributes;

        $model = $copy->wire('model');

        if (is_string($model->directive) && is_bool($model->value) && $this->names->isEmpty()) {
            throw new Exception('When using a boolean model, you must provide a values array or assign their value.');
        }

        if (is_bool($model->value) && is_bool($copy->get('name')) && $this->names->isEmpty()) {
            throw new Exception('The {values} attribute is required when using boolean values.');
        }

        if ($this->names->isNotEmpty()) {
            $modelName = $valueName = $type === 'min' ? "{$this->names->get(0)}" : "{$this->names->get(1)}";
        } else {
            $modelName = "{$model->value}_{$type}";
            $valueName = "{$copy->get('name')}_{$type}";
        }

        if (is_string($model->directive)) {
            $copy->offsetSet($model->directive, $modelName);
        }

        if (is_string($copy->get('name'))) {
            $copy->offsetSet('name', $valueName);
        }

        if(!is_string($model->directive) && $this->values->isNotEmpty()) {
            $copy->offsetSet('value', $type === 'min' ? $this->values->get(0) : $this->values->get(1));
        }

        return $copy;
    }

    public function formatDataSlider(ComponentAttributeBag $attributes): array
    {
        return [
            'type' => 'number',
            'min'  => $attributes->has('min') ? $attributes->get('min') : 0,
            'max'  => $attributes->has('max') ? $attributes->get('max') : 100,
            'step' => $attributes->has('step') ? $attributes->get('step') : 1,
        ];
    }

    public function getSliderClasses(?bool $disabled = null): string
    {
        return Str::of('relative w-full align-middle bg-secondary-200 rounded dark:bg-white')
            ->when(!$disabled, fn (Stringable $stringable) => $stringable->append(' cursor-pointer'))
            ->when($this->size === 'sm', fn (Stringable $stringable) => $stringable->append(' my-4 h-1.5'))
            ->when($this->size === 'md', fn (Stringable $stringable) => $stringable->append(' my-4.5 h-2'))
            ->when($this->size === 'lg', fn (Stringable $stringable) => $stringable->append(' my-5 h-2.5'));
    }

    public function getBarClasses(?bool $disabled = null, bool $hasError = false): string
    {
        return Str::of('absolute rounded-l')
            ->when($disabled, fn (Stringable $stringable) => $stringable->append(' bg-secondary-500 opacity-60'))
            ->when(!$disabled && $hasError, fn (Stringable $stringable) => $stringable->append(' bg-negative-600'))
            ->when(!$disabled && !$hasError, fn (Stringable $stringable) => $stringable->append(' bg-primary-600'))
            ->when($this->size === 'sm', fn (Stringable $stringable) => $stringable->append(' h-1.5'))
            ->when($this->size === 'md', fn (Stringable $stringable) => $stringable->append(' h-2'))
            ->when($this->size === 'lg', fn (Stringable $stringable) => $stringable->append(' h-2.5'));
    }

    public function getStopClasses(): string
    {
        return Str::of('absolute -translate-x-1/2 bg-white rounded-full dark:bg-secondary-400')
            ->when($this->size === 'sm', fn (Stringable $stringable) => $stringable->append(' w-1.5 h-1.5'))
            ->when($this->size === 'md', fn (Stringable $stringable) => $stringable->append(' w-2 h-2'))
            ->when($this->size === 'lg', fn (Stringable $stringable) => $stringable->append(' w-2.5 h-2.5'));
    }

    public function getButtonGridClasses(): string
    {
        return <<<EOT
            absolute leading-normal text-center -translate-x-1/2
            bg-transparent select-none w-9 h-9 -top-4
        EOT;
    }

    public function getButtonClasses(?bool $disabled = null): string
    {
        $enabledClasses = <<<EOT
            border-2 bg-white hover:bg-white dark:hover:bg-white
            cursor-grab hover:scale-120
        EOT;

        $disabledClasses = <<<EOT
            border-2 bg-white hover:bg-white dark:hover:bg-white
            ring-transparent cursor-not-allowed
        EOT;

        return $disabled ? $disabledClasses : $enabledClasses;
    }

    public function buttonError(?bool $disabled = null, bool $hasError = false): string
    {
        return $disabled ? 'secondary' : ($hasError ? 'negative' : 'primary');
    }

    public function buttonSizes(): string
    {
        return match ($this->size) {
            'sm' => '2xs',
            'md' => 'xs',
            'lg' => 'sm',
        };
    }
}