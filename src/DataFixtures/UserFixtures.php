<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();

        foreach ($users as $key => $data) {
            $user = new User();

            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->encoder->encodePassword($user, $data['password']));
            $user->setEmail($data['email']);

            $manager->persist($user);

            $this->addReference('user.' . $key, $user);
        }

        $manager->flush();
    }

    public function getUsers(): array
    {
        return [
            ['username' => 'casual_user', 'email' => 'casual_user@gmail.com', 'roles' => ['ROLE_USER'], 'password' => 'password'],
            ['username' => 'admin_user', 'email' => 'admin_user@gmail.com', 'roles' => ['ROLE_USER', 'ROLE_ADMIN'], 'password' => 'password']
        ];
    }
}
