<?php

use Cloudinary\Cloudinary;

beforeEach(function () {
    $this->app->instance(
        Cloudinary::class,
        new Cloudinary('cloudinary://key:secret@demo')
    );
});

it('renders widget component button with onclick and slot content', function () {
    $html = view('cloudinary::components.widget', [
        'slot' => 'Upload',
        'attributes' => '',
    ])->render();

    expect($html)->toContain('onclick="openWidget()"');
    expect($html)->toContain('Upload');
    expect($html)->toContain('cloudinary.com');
});
