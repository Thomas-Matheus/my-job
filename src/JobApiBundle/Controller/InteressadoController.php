<?php

namespace JobApiBundle\Controller;

use AppBundle\Entity\Candidato;
use AppBundle\Entity\Vagas;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;

class InteressadoController extends FOSRestController
{
    /**
     * @Rest\Get("/interessado/{id}vaga/{vaga}",
     *     requirements={ "id": "\d+" , "vaga": "\d+"},
     *     name="pesquisar_interessado"
     * )
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
     *          "description"="ID do candidato."
     *      },
     *     {
     *          "name"="vaga",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID da vaga."
     *      }
     *  }
     * )
     */
    public function getAction($id, $vaga)
    {
        try {
            if (!is_numeric($id) || empty($id) || !is_numeric($vaga) || empty($vaga)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $interessado = $em->getRepository('AppBundle:Vagas')->findBy(['candidato' => $id]);

            if (empty($interessado)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($interessado, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/interessado/{id}/vaga/{vaga}",
     *     requirements={ "id": "\d+" , "vaga": "\d+"},
     *     name="adicionar_interessado"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      201="Request aceito.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Adiciona um candidato a uma vaga.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do candidato."
     *      },
     *      {
     *          "name"="vaga",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID da vaga."
     *      },
     *  }
     * )
     */
    public function postAction($id, $vaga)
    {
        try {
            if (!is_numeric($id) || empty($id) || !is_numeric($vaga) || empty($vaga)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $candidato = $em->getRepository('AppBundle:Candidato')->find($id);
            $vagas = $em->getRepository('AppBundle:Vaga')->find($vaga);


            $interessado = (new Vagas())
                ->addCandidato($candidato);

            $candidato = (new Candidato())
                ->addVaga($vagas);

            $em->persist($interessado);
            $em->persist($candidato);
            $em->flush();

            $htmlEmail = $this->renderView('JobApiBundle:Default:index.html.twig', [
                'usuario' => $candidato->getUsuario(),
                'codigo' => base64_encode(mt_rand())
            ]);
            $this->get('api.job.mail')->send($interessado->getUsuario()->getEmail(), $htmlEmail);

            return new View([$interessado, $candidato], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/interessado/{id}",
     *     requirements={ "id": "\d+"},
     *     name="remover_interessado"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Remove uma vaga especifica.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do vaga."
     *      },
     *  }
     * )
     */
    public function deleteAction($id)
    {
        try {
            if (!is_numeric($id) || empty($id)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $interessado = $em->getRepository('AppBundle:Candidato')->findOneBy([
                'vagas' => $id,
            ]);

            if (empty($interessado)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $em->remove($interessado);
            $em->flush();

            return new View('', Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
