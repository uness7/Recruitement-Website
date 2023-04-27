<?php

    namespace App\Controller;

    use App\Entity\Candidate;
    use App\Entity\Recruiter;
    use App\Entity\User;
    use App\Form\CandidateFormType;
    use App\Form\RecruiterFormType;
    use App\Form\RegistrationFormType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class RegistrationController extends AbstractController
    {
        #[Route('/register', name: 'app_register', methods: ['POST', 'GET'])]
        public function register(
            Request $request,
            UserPasswordHasherInterface $userPasswordHasher,
            EntityManagerInterface $entityManager): Response
        {
            $user = new User();
            $candidate = new Candidate();
            $recruiter = new Recruiter();

            $form = $this->createForm(RegistrationFormType::class, $user);
            $recruiterForm = $this->createForm(RecruiterFormType::class, $recruiter);
            $candidateForm = $this->createForm(CandidateFormType::class, $candidate);

            $form->handleRequest($request);
            $recruiterForm->handleRequest($request);
            $candidateForm->handleRequest($request);




            if($form->isSubmitted() && $form->isValid()) {

                $userType = $form->get('userType')->getData();
                $firstName = $form->get('firstName')->getData();
                $lastName = $form->get('lastName')->getData();
                $email = $form->get('email')->getData();
                $companyName = $recruiterForm->get('companyName')->getData();

                if ($userType !== 'candidate' && $userType !== 'recruiter') {
                    throw new \RuntimeException('Invalid user type');
                }

                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setEmail($email);
                $user->setPassword($userPasswordHasher->hashPassword($user,
                    $form->get('plainPassword')->getData()));


                if ($userType === 'candidate') {
                    $user->setRoles(['ROLE_CANDIDATE']);
                    $user->setCandidate($candidate);

                    $candidate->setFirstName($firstName);
                    $candidate->setLastName($lastName);
                    $candidate->setEmail($email);

                    $entityManager->persist($candidate);
                } else {
                    $user->setRoles(['ROLE_RECRUITER']);
                    $user->setRecruiter($recruiter);

                   $recruiter->setEmail($email);
                   if(!empty($companyName)) {
                       $recruiter->setCompanyName($companyName);
                   }

                    $entityManager->persist($recruiter);
                }
                $entityManager->persist($user);
                $entityManager->flush();
//                try {
//
//                } catch (\Throwable $e) {
//                    // Handle errors that may occur during the persistence of the user entity
//                    throw new \RuntimeException('Unable to register user', 0, $e);
//                }

                return $this->redirectToRoute('app_login');
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
                'recruiterForm' => $recruiterForm->createView(),
                'candidateForm' => $candidateForm->createView(),
            ]);
        }
    }

