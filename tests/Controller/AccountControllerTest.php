<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends WebTestCase
{
    public $client = null;

    private $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->user = static::$container->get(UserRepository::class)->findOneBy(['username' => 'account_tests']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfAccountPageIsSuccessfull(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/account');

        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html', 'Nazwa Użytkownika');
        $this->assertSelectorTextContains('html', 'Twój Email');
        $this->assertSelectorTextContains('html', 'Twoje Hasło');
        $this->assertSelectorTextContains('html', 'Moje Konto');
    }

    /**
     * @dataProvider getUrlsForUnloggedUsers
     */
    public function testAccessDeniedForUnloggedUsers(string $httpMethod, string $url): void
    {
        $this->client->request($httpMethod, $url);

        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);

    }

    /**
     * @runInSeparateProcess
     */
    public function testAccountChangeEmail(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/account');

        $password = 'account_password';
        $oldEmail = 'account_test@gmail.com';
        $newEmail = 'new_account_test@gmail.com';

        $this->client->submitForm('change_email_form_button', [
            'change_email[password]' => $password,
            'change_email[newEmail]' => $newEmail,
        ]);

        $this->assertResponseRedirects('/account', Response::HTTP_FOUND);

        $this->assertEquals(static::$container->get(UserRepository::class)->findOneBy(['username' => 'account_tests'])->getEmail(), $newEmail);

        $this->client->request('GET', '/account');

        $this->client->submitForm('change_email_form_button', [
            'change_email[password]' => $password,
            'change_email[newEmail]' => $oldEmail,
        ]);

        $this->assertResponseRedirects('/account', Response::HTTP_FOUND);
    }

    /**
     * @runInSeparateProcess
     */
    public function testAccountChangePassword(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/account');

        $oldPassword = 'account_password';
        $newPassword = 'account_test_password';

        $this->client->submitForm('change_password_form_button', [
            'change_password[oldPassword]' => $oldPassword,
            'change_password[newPassword][first]' => $newPassword,
            'change_password[newPassword][second]' => $newPassword,
        ]);

        $this->assertResponseRedirects('/account', Response::HTTP_FOUND);

        $this->client->request('GET', '/account');

        $this->client->submitForm('change_password_form_button', [
            'change_password[oldPassword]' => $newPassword,
            'change_password[newPassword][first]' => $oldPassword,
            'change_password[newPassword][second]' => $oldPassword,
        ]);

        $this->assertResponseRedirects('/account', Response::HTTP_FOUND);
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfPasswordMustBeCorrectToChangeEmail(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/account');

        $wrongPassword = 'wrong_password_xxx';
        $oldEmail = $this->user->getEmail();
        $newEmail = 'new_account_test@gmail.com';

        $this->client->submitForm('change_email_form_button', [
            'change_email[password]' => $wrongPassword,
            'change_email[newEmail]' => $newEmail,
        ]);

        $this->assertEquals(static::$container->get(UserRepository::class)->findOneBy(['username' => 'account_tests'])->getEmail(), $oldEmail);
        $this->assertSelectorTextContains('html', 'Podane hasło jest nieprawidłowe');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfOldPasswordMustBeCorrectToChangePassword(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/account');

        $wrongPassword = 'wrong_password_xxx';
        $newPassword = 'account_test_password';

        $this->client->submitForm('change_password_form_button', [
            'change_password[oldPassword]' => $wrongPassword,
            'change_password[newPassword][first]' => $newPassword,
            'change_password[newPassword][second]' => $newPassword,
        ]);

        $this->assertResponseStatusCodeSame($this->client->getResponse()->getStatusCode(), Response::HTTP_OK);
        $this->assertSelectorTextContains('html', 'Podane obecne hasło jest nieprawidłowe');
    }

    public function getUrlsForUnloggedUsers(): ?\Generator
    {
        yield ['GET' , '/account'];
    }
}