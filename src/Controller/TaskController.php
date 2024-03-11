<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/task', name: 'api_task_')]
class TaskController extends AbstractController
{

  function __construct(
    private TaskRepository $taskRepository,
    private EntityManagerInterface $entityManager
  ) {
  }

  #[Route('', name: 'create', methods: ['POST'])]
  public function create(Request $request): JsonResponse
  {
    $task = new Task();

    $json = json_decode($request->getContent(), true);
    $task->setDescription($json['description']);
    $this->entityManager->persist($task);

    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'id' => $task->getId()
    ]);
  }

  #[Route('', name: 'update', methods: ['PUT'])]
  public function edit(Request $request, Task $task): JsonResponse
  {
    $json = json_decode($request->getContent(), true);
    $task->setDescription($json['description']);
    $this->entityManager->persist($task);
    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'id' => $task->getId()
    ]);
  }

  #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
  public function delete(Task $task): JsonResponse
  {
    $this->entityManager->remove($task);
    $this->entityManager->flush();

    return new JsonResponse([
      'success' => true,
      'id' => $task->getId()
    ]);
  }

  #[Route('/{id}', name: 'one', methods: ['GET'])]
  public function one(Task $task): JsonResponse
  {
    return new JsonResponse([
      "success" => true,
      "task" => [
        "id" => $task->getId(),
        "description" => $task->getDescription()
      ]
    ]);
  }

  #[Route('', name: 'list', methods: ['GET'])]
  public function list(): JsonResponse
  {
    $tasks = $this->taskRepository->findAll();

    return new JsonResponse([
      "success" => true,
      "tasks" => array_map(function ($task) {
        return [
          "id" => $task->getId(),
          "description" => $task->getDescription()
        ];
      }, $tasks)
    ]);
  }
}
