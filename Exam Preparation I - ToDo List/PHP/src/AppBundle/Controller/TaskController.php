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
        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $taskRepo->findAll();

        return $this->render('task/index.html.twig', ['tasks' => $tasks]);
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
            if ($task->getTitle()===null || $task->getComments()===null) {
                return $this->redirectToRoute('create');
            }
            $em = $this->getDoctrine()->getManager();

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('task/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id, Request $request)
    {
        $taskToBeDeleted = $this->getDoctrine()->getRepository(Task::class)->find($id);
        // syshtoto kato:
        // $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        // $tasks = $taskRepo->find($id);

        if ($taskToBeDeleted === null) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(TaskType::class, $taskToBeDeleted);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($taskToBeDeleted);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('task/delete.html.twig', ['task' => $taskToBeDeleted, 'form' => $form->createView()]);
    }
}
