<?php

namespace App\Controller\Admin;

use App\Entity\Recruiter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RecruiterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recruiter::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
