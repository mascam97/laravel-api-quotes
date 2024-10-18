<?php

namespace Tests\Factories;

use Carbon\Carbon;
use Domain\Users\Enums\SexEnum;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

class RegisterRequestDataFactory
{
    use WithFaker;

    protected string $name = 'user name';

    protected string $email = 'user@mail.com';

    protected string $password = '53cUr3.Pa55word';

    public static function new(): self
    {
        return new self();
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function withUser(User $user): self
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = $user->password;

        return $this;
    }

    public function create(array $extra = []): array
    {
        return $extra + [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
