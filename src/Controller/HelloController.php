<?php
namespace App\Controller;


use App\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use App\Form\PersonType;
use App\Form\HelloType;
use App\Repository\PersonRepository;
use App\Service\MyService;
use PHPUnit\Util\Xml\Validator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{id}", name="hello")
     */
    public function index(Request $request, MyService $service, int $id = 11)
    {
        $person = $service->getPerson($id);
        $msg = $person == null ? 'no person.' : 'name: ' . $person;
        return $this->render('hello/index.html.twig',[
            'title' => 'Hello',
            'message' => $msg
        ]);
    }


    /**
     * @Route("/find", name="find")
     */
    public function find(Request $request)
    {
        $formobj = new FindForm();
        $form = $this->createFormBuilder($formobj)
            ->add('find', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Click'))
            ->getForm();
        
        $repository = $this->getDoctrine()->getRepository(Person::class);

        $manager = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $findstr = $form->getData()->getFind();
            $query = $manager->createQuery(
                "SELECT p FROM App\Entity\Person p WHERE p.name = '{$findstr}'"
            );
            $result = $query->getResult();
        } else {
            $result = $repository->findAllwithSort();
        }
        return $this->render('hello/find.html.twig',[
            'title' => 'hello',
            'form' => $form->createView(),
            'data' => $result,
        ]);
        
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, ValidatorInterface $validator)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, 
                array(
                    'required' => true,
                    'constraints' => [
                        new Assert\Length(array(
                            'min' => 3, 'max' => 10, 
                            'minMessage' => '３文字以上必要です。',
                            'maxMessage' => '10文字以内にして下さい。'))
                    ]
                )
            )
            ->add('save', SubmitType::class, array('label' => 'Click'))
            ->getForm();


        if ($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            if ($form->isValid()){
                $msg = 'Hello, ' . $form->get('name')->getData() . '!';
            } else {
                $msg = 'ERROR!';
            }
        } else {
            $msg = 'Send Form';
        }  
        return $this->render('hello/create.html.twig', [
            'title' => 'Hello',
            'message' => $msg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update(Request $request, Person $person){
        $form = $this->createFormBuilder($person)
                ->add('name', TextType::class)
                ->add('mail', TextType::class)
                ->add('age', IntegerType::class)
                ->add('save', SubmitType::class, array('label' => 'Click'))
                ->getForm();
        
        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirect('/hello');
        } else {
            return $this->render('hello/create.html.twig',[
                'title' => 'Hello',
                'message' => 'Update Entity id=' . $person->getId(),
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Request $request, Person $person){
        $form = $this->createFormBuilder($person)
                ->add('name', TextType::class)
                ->add('mail', TextType::class)
                ->add('age', IntegerType::class)
                ->add('save', SubmitType::class, array('label' => 'Click'))
                ->getForm();
        
        if($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($person);
            $manager->flush();
            return $this->redirect('/hello');
        } else {
            return $this->render('hello/create.html.twig',[
                'title' => 'Hello',
                'message' => 'Delete Entity id=' . $person->getId(),
                'form' => $form->createView(),
            ]);
        }
    }
}

class FindForm
{
    private $find;


    public function getFind()
    {
        return $this->find;
    }
    public function setFind($find)
    {
        $this->find = $find;
    }
}