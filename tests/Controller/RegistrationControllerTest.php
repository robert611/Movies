<?php 

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class RegistrationControllerTest extends WebTestCase
{
    public $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfRegistrationPageIsSuccessfull()
    {
        $this->client->request('GET', '/register');
        
        $this->assertEquals(200, $this->client->getResponse()->isSuccessful());
        $this->assertSelectorTextContains('html h1', 'Rejestracja');
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfUserCanRegister()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Zarejestruj się')->form();

        $form['registration_form[email]'] = 'test_registration_email@test.pl';
        $form['registration_form[password][first]'] = 'password';
        $form['registration_form[password][second]'] = 'password';
        $form['registration_form[username]'] = 'test_registration_user';

        $crawler = $this->client->submit($form);

        $this->client->request('GET', '/');
        $this->assertSelectorTextNotContains('html', 'Exception');
        $this->assertSelectorTextContains('html', 'Moje Konto');

        $users = static::$container->get('doctrine')->getRepository(User::class)->findAll();
        $user = $users[count($users) - 1];

        $entityManager = static::$container->get('doctrine.orm.entity_manager');

        $entityManager->remove($user);
        $entityManager->flush();
        
        $this->assertEquals($form->get('registration_form[email]')->getValue(), $user->getEmail());
        $this->assertEquals($form->get('registration_form[username]')->getValue(), $user->getUsername());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIfLogedInUserCannotRegister()
    {
        $this->client->loginUser(static::$container->get(UserRepository::class)->findOneBy([]));
        
        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseRedirects('/');
    }
}