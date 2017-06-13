<?php

namespace JobApiBundle\Controller;

use AppBundle\Entity\Vagas;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;

class VagasController extends FOSRestController
{
    /**
     * @Rest\Get("/vagas", name="job_listar_vagas")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Lista todas as vagas.",
     * )
     */
    public function cgetAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $vagas = $em->getRepository('AppBundle:Vagas')->findAll();

            if (empty($vagas)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($vagas, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/vaga/{id}",
     *     requirements={ "id": "\d+" },
     *     name="job_pesquisar_vagas"
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Pesquisa uma vaga especifica.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID da vaga."
     *      }
     *  }
     * )
     */
    public function getAction($id)
    {
        try {
            if (!is_numeric($id) || empty($id)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $vaga = $em->getRepository('AppBundle:Vagas')->find($id);

            if (empty($vaga)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($vaga, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
