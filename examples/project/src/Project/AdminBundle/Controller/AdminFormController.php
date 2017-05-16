<?php

namespace Project\AdminBundle\Controller;

use Project\AdminBundle\Entity\Admin;
use Project\AdminBundle\Form\Type\AdminCreateType;
use Project\AdminBundle\Form\Type\MultipleAdminType;
use Project\AdminBundle\Lib\AdminListModel;
use Project\AdminBundle\Lib\AdminModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminFormController
 *
 * @package Project\AdminBundle\Controller
 */
class AdminFormController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postMultipleAction(Request $request): JsonResponse
    {
        try {
            $model = new AdminListModel();
            $form = $this->createForm(MultipleAdminType::class, $model, ['validation_groups' => ['create']]);
            if (!$form->handleRequest($request)->isValid()) {
                throw $this->createNotFoundException($form->getErrors(true, true));
            }
            $admins = $this->get('project_admin.admin_manager')->bulkCreate($model);

            return $this->json($admins, Response::HTTP_CREATED, [], ['list']);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request): JsonResponse
    {
        try {
            $adminModel = new AdminModel(null);
            $form = $this->createForm(AdminCreateType::class, $adminModel, ['validation_groups' => ['create']]);
            if (!$form->handleRequest($request)->isValid()) {
                throw $this->createNotFoundException($form->getErrors(true, true));
            }
            $admin = $this->get('project_admin.admin_manager')->create($adminModel);

            return $this->json($admin, Response::HTTP_CREATED, [], ['get']);
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
            $adminModel = $this->get('project_admin.admin_model_builder')->createByAdmin($admin);
            $form = $this->createForm(AdminCreateType::class, $adminModel, ['validation_groups' => ['edit']]);
            if (!$form->handleRequest($request)->isValid()) {
                throw $this->createNotFoundException($form->getErrors(true, true));
            }
            $this->get('project_admin.admin_manager')->update($admin, $adminModel);

            return $this->json($admin, Response::HTTP_OK, [], ['get']);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}