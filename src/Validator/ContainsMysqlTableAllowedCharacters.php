<?php  

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsMysqlTableAllowedCharacters extends Constraint
{
    public $message = 'The string "{{ string }}" contains an illegal character; it can only contain characters that maybe used in mariadb table name';
    public $mode = 'strict';
}