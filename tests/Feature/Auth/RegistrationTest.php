<?php
use Livewire\Volt\Volt;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
test('public registration is unavailable', function () { $this->get('/register')->assertNotFound(); });
test('registration action cannot create an account directly', function () {
 Volt::test('auth.register')->call('register')->assertForbidden();
 $this->assertDatabaseCount('users', 0);
});
