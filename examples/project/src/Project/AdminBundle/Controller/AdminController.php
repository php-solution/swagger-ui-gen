<?php

namespace Project\AdminBundle\Controller;

use Project\AdminBundle\Entity\Admin;
use Project\AdminBundle\Lib\PaginatedCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 *
 * @package Project\AdminBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @param int $offset
     *
     * @return JsonResponse
     */
    public function listAction(int $offset = 0): JsonResponse
    {
        $repository = $this->getDoctrine()->getRepository(Admin::class);
        $totalCount = $repository->getCount();
        $admins = $repository->getList($offset);
        $data = new PaginatedCollection($totalCount, $admins, $offset);

        return $this->json($data, Response::HTTP_OK, [], ['groups' => ['list']]);
    }

    /**
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function getAction(Admin $admin): JsonResponse
    {
        return $this->json($admin, Response::HTTP_OK, [], ['groups' => ['get']]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request): JsonResponse
    {
        try {
            $adminModel = $this->get('project_admin.admin_model_builder')->createByData($request->request->all());
            if (count($constraint = $this->get('validator')->validate($adminModel, null, ['create'])) > 0) {
                throw $this->createNotFoundException($constraint);
            }
            $admin = $this->get('project_admin.admin_manager')->create($adminModel);

            return $this->json($admin, Response::HTTP_CREATED, [], ['groups' => ['get']]);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @param Admin   $admin
     *
     * @return JsonResponse
     */
    public function putAction(Request $request, Admin $admin): JsonResponse
    {
        try {
            $adminModel = $this->get('project_admin.admin_model_builder')->createByAdmin($admin, $request->request->all());
            if (count($constraint = $this->get('validator')->validate($adminModel, null, ['edit'])) > 0) {
                throw $this->createNotFoundException('Not valid request data');
            }
            $this->get('project_admin.admin_manager')->update($admin, $adminModel);

            return $this->json($admin, Response::HTTP_OK, [], ['groups' => ['get']]);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Admin $admin
     *
     * @return JsonResponse
     */
    public function deleteAction(Admin $admin): JsonResponse
    {
        $this->get('project_admin.admin_manager')->delete($admin);

        return $this->json('');
    }
}