<?php

namespace WireUi\Components\TextField\tests\Unit;

use WireUi\Components\TextField\Textarea;
use WireUi\Components\Wrapper\WireUi\{Color, Rounded};
use WireUi\WireUi\Shadow;

beforeEach(function () {
    $this->component = (new Textarea())->withName('textarea');
});

test('it should have array properties', function () {
    $packs = $this->invokeProperty($this->component, 'packs');

    expect($packs)->toBe(['shadow']);

    $props = $this->invokeProperty($this->component, 'props');

    expect($props)->toBe([
        'cols'       => 'auto',
        'rows'       => 4,
        'shadowless' => false,
    ]);
});

test('it should have properties in component', function () {
    $this->runWireUiComponent($this->component);

    expect($this->component)->toHaveProperties([
        'cols',
        'rows',
        'color',
        'shadow',
        'rounded',
        'squared',
        'shadowless',
        'colorClasses',
        'shadowClasses',
        'roundedClasses',
    ]);

    expect($this->component->rows)->toBe(4);
    expect($this->component->cols)->toBe('auto');
    expect($this->component->shadowless)->toBeFalse();
});

test('it should set random color in component', function () {
    $pack = $this->getRandomPack(Color::class);

    $this->setAttributes($this->component, [
        'color' => $color = data_get($pack, 'key'),
    ]);

    $this->runWireUiComponent($this->component);

    $class = data_get($pack, 'class');

    expect($this->component->color)->toBe($color);
    expect($this->component->colorClasses)->toBe($class);

    expect('<x-textarea :$color />')
        ->render(compact('color'))
        ->toContain(data_get($class, 'input'));
});

test('it should set random shadow in component', function () {
    $pack = $this->getRandomPack(Shadow::class);

    $this->setAttributes($this->component, [
        'shadow' => $shadow = data_get($pack, 'key'),
    ]);

    $this->runWireUiComponent($this->component);

    $class = data_get($pack, 'class');

    expect($this->component->shadow)->toBe($shadow);
    expect($this->component->shadowless)->toBeFalse();
    expect($this->component->shadowClasses)->toBe($class);

    expect('<x-textarea :$shadow />')->render(compact('shadow'))->toContain($class);
});

test('it should set random rounded in component', function () {
    $pack = $this->getRandomPack(Rounded::class);

    $this->setAttributes($this->component, [
        'rounded' => $rounded = data_get($pack, 'key'),
    ]);

    $this->runWireUiComponent($this->component);

    $class = data_get($pack, 'class');

    expect($this->component->squared)->toBeFalse();
    expect($this->component->rounded)->toBe($rounded);
    expect($this->component->roundedClasses)->toBe($class);

    expect('<x-textarea :$rounded />')
        ->render(compact('rounded'))
        ->toContain(data_get($class, 'input'));
});