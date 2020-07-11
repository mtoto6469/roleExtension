<?php


namespace mtoto\role\models;

use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name='isAuthor';
    public function execute($user, $item, $params)
    {
        // TODO: Implement execute() method.
        return isset($params['post'])? $params['post']->author_id==$user:false;
    }
}