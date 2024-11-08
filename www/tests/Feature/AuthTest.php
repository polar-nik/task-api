<?php namespace Tests\Feature;

use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private ?User $user = null;

    private array $successData = [
        'name' => 'User1',
        'email' => 'user1@email.com',
        'password' => 'user-password',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create($this->successData);
    }

    public function test_signUp(): void
    {
        $response = $this->postJson(route('auth.sign-up'), [
            'name' => 'User2',
            'email' => 'user2@email.com',
            'password' => 'user2-password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'token']);
    }

    public static function invalidSignUpValidationProvider(): array
    {
        return [
            'not-filled-name' => [
                [
                    'email' => 'Some@email.com',
                    'password' => 'pass',
                ],
                'name',
            ],
            'short-password' => [
                [
                    'name' => 'User1',
                    'email' => 'Some@email.com',
                    'password' => 'pass',
                ],
                'password',
            ],
            'wrong-email' => [
                [
                    'name' => 'User1',
                    'email' => 'wrong-email.com',
                    'password' => 'pass',
                ],
                'email',
            ],
        ];
    }

    #[DataProvider('invalidSignUpValidationProvider')]
    public function test_failedSignUp(array $data, string $failedField): void
    {
        $response = $this->postJson(route('auth.sign-up'), $data);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonValidationErrors($failedField);
    }

    public function test_signIn(): void
    {
        $response = $this->postJson(route('auth.sign-in'), $this->successData);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'token']);
    }

    public static function invalidSignInValidationProvider(): Generator
    {
        yield 'wrong-password' => [
            [
                'email' => 'user1@email.com',
                'password' => 'passwort',
            ],
            'password',
        ];

        yield 'wrong-user' => [
            [
                'email' => 'user@email.com',
                'password' => 'password',
            ],
            'email',
        ];

        yield 'non-existent-user' => [
            [
                'email' => 'wrong-email.com',
                'password' => 'pass',
            ],
            'email',
        ];
    }

    #[DataProvider('invalidSignInValidationProvider')]
    public function test_failedSignIn(array $data, string $failedField): void
    {
        $response = $this->postJson(route('auth.sign-in'), $data);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $response->assertJsonValidationErrors($failedField);
    }
}
