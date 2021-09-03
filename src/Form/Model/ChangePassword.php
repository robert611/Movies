<?php 

namespace App\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *  message = "Podane obecne hasło jest nieprawidłowe"
     * )
     */
    public $oldPassword;

    /**
     * @Assert\Length(
     *  min = 8,
     *  max = 64,
     *  minMessage = "Nowe hasło musi składać się z conajmniej {{ limit }} znaków",
     *  maxMessage = "Nowe hasło nie może składać się z więcej niż {{ limit }} znaków",
     * )
     */
    public $newPassword;
}