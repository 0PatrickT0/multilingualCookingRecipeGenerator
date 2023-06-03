<?php

namespace App\Controller\Admin;

use App\Entity\ChatLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChatLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChatLog::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('question'),
            TextEditorField::new('answer'),
            /* AssociationField::new('user'), */
        ];
    }
}
