<?php

use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use function Pest\Laravel\actingAs;
use Tests\CreatesApplication;

uses(TestCase::class, CreatesApplication::class, RefreshDatabase::class, WithFaker::class)->in('Feature', 'Unit');

function login(?User $user = null): void
{
    actingAs($user ?? User::factory()->create(), 'sanctum');
}
