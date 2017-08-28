<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    /**
     * @param Request $request
     * @Route("/", name="index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Task::class);
        $finishedTasks = $repo->findBy(["status" => "Finished"]);
        $openTasks = $repo->findBy(["status" => "Open"]);
        $inProgressTasks = $repo->findBy(["status" => "In Progress"]);
        return $this->render("task/index.html.twig",
            [
                "openTasks" => $openTasks,
                "inProgressTasks" => $inProgressTasks,
                "finishedTasks" => $finishedTasks,
            ]);
    }

    /**
     * @param Request $request
     * @Route("/create", name="create")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute("index");
        }
        return$this->render("task/create.html.twig",
            [
                "form" => $form->createView(),
                "task" => $task
            ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
       $repo = $this->getDoctrine()->getRepository(Task::class);
       $task = $repo->find($id);
       $form = $this->createForm(TaskType::class, $task);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager = $this->getDoctrine()->getManager();
           $entityManager->merge($task);
           $entityManager->flush();
           return $this->redirectToRoute("index");
       }
       return $this->render("task/edit.html.twig",
           [
               "form" => $form->createView(),
               "task" => $task
           ]);
    }
}
