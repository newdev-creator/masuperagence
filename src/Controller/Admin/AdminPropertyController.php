<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index(): Response
    {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    /**
     * @Route("/admin/property/new", name="admin.property.new")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($property);
            $em->flush();
            $this->addFlash('success', 'bien créé avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit", methods={"GET", "POST"})
     * @param Property $property
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Property $property, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Property $property
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return Response
     * @Route("/admin/property/delete/{id}", name="admin.property.delete", methods={"POST"})
     */
    public function delete(Property $property, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $em->remove($property);
            $em->flush();
            $this->addFlash('success', 'bien supprimé avec succès');
        }

        return $this->redirectToRoute('admin.property.index');
    }
}