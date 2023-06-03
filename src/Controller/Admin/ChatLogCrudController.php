<?php

namespace App\Controller\Admin;

use App\Entity\ChatLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            /* ->renderSidebarMinimized() */
            ->setEntityPermission('ROLE_ADMIN')
            ->setPaginatorPageSize(20);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('question'),
            TextEditorField::new('answer'),
            /* AssociationField::new('user') */
                /*->autocomplete()
                ->setFormTypeOption('by_reference', false)
                ->setRequired(true), */
/*                 ->formatValue(function ($value, $entity) {
                    return $entity->getUser()->getUsername();
                }), */
        ];
    }
}
