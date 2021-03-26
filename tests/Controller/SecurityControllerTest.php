<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class SecurityControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfLoginPageRedirectsToHomepage()
    {
        $this->client->request('GET', '/login');
        
        $this->assertResponseRedirects("/");
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCanLogin()
    {
        $crawler = $this->client->request('GET', '/');

        $form['username'] = 'test_user';
        $form['password'] = 'test_user';

        $crawler = $this->client->submitForm('Login', $form);

        $this->assertResponseRedirects('/');

        $crawler = $this->client->request('GET', '/');

        $this->assertSelectorTextContains('html', 'Logout');
        $this->assertSelectorTextNotContains('html', 'Login');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCanLogout()
    {
        $crawler = $this->client->request('GET', '/');

        $form['username'] = 'test_user';
        $form['password'] = 'test_user';

        $this->client->submitForm('Login', $form);

        $this->client->request('GET', '/');

        $this->client->clickLink('Logout');

        $this->client->request('GET', '/');

        $this->assertSelectorTextContains('html', 'Login');
        $this->assertSelectorTextNotContains('html', 'Logout');
    }
}