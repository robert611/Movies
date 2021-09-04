<?php 

namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmail
{
    /**
     * @SecurityAssert\UserPassword(
     *  message = "Podane hasło jest nieprawidłowe"
     * )
     */
    public $password;

    /**
     * @Assert\Email(
     *     message = "Podany email '{{ value }}' nie jest prawidłowym adresem email"
     * )
     */
    public $newEmail;
}