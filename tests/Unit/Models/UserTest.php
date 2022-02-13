<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

// PHPUnit\Framework\TestCase is not used because the test use native function of Laravel

class UserTest extends TestCase
{
    public function test_has_many_notes()
    {
        $user = new User();
        $this->assertInstanceOf(Collection::class, $user->quotes);
    }
}
