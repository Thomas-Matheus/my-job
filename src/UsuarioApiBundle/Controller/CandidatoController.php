<?php

namespace UsuarioApiBundle\Controller;

use AppBundle\Entity\Candidato;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;

class CandidatoController extends FOSRestController
{
    /**
     * @Rest\Get("/candidatos", name="listar_candidatos")
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Lista todas os candidatos.",
     * )
     */
    public function cgetAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $candidatos = $em->getRepository('AppBundle:Candidato')->findAll();

            if (empty($candidatos)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($candidatos, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/candidato/{id}",
     *     requirements={ "id": "\d+" },
     *     name="pesquisar_candidato"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Pesquisa uma candidato.",
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
            $candidato = $em->getRepository('AppBundle:Candidato')->find($id);

            if (empty($candidato)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($candidato, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/candidato/", name="cadastrar_candidato")
     * @ApiDoc(
     *  statusCodes={
     *      201="Vaga criada.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Insere uma novo candidato.",
     *  requirements={
     *      {
     *          "name" = "experiencia",
     *          "dataType" = "string",
     *          "description" = "Descrição das experiências do candidato."
     *      },
     *      {
     *          "name" = "telefone",
     *          "dataType" = "string",
     *          "description" = "Telefone de contato do candidato."
     *      },
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        try {
            $experiencia = $request->get('experiencia');
            $telefone = $request->get('telefone');
            $curriculo = $request->files('curriculo');

            if (empty($experiencia) || empty($curriculo) || empty($telefone)) {
                return new View('Formulário não submetido ou requisição HTTP inválida.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $candidato = (new Candidato())
                ->setExperiencias($experiencia)
                ->setTelefone($telefone)
                ->setUsuario($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($candidato);
            $em->flush();

            return new View($candidato, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Put("/candidato/{id}",
     *     requirements={ "id": "\d+" },
     *     name="atualizar_candidato"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Atualizar uma candidato.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do candidato."
     *      },
     *      {
     *          "name" = "experiencia",
     *          "dataType" = "string",
     *          "description" = "Descrição das experiências do candidato."
     *      },
     *      {
     *          "name" = "telefone",
     *          "dataType" = "string",
     *          "description" = "Telefone de contato do candidato."
     *      },
     *  }
     * )
     */
    public function editAction($id, Request $request)
    {
        try {
            if (!is_numeric($id) || empty($id)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            /** @var Candidato $oldCandidato */
            $oldCandidato = $em->getRepository('AppBundle:Candidato')->find($id);

            if (empty($oldCandidato)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $experiencia = $request->get('experiencia');
            $telefone = $request->get('telefone');

            if (empty($experiencia) || empty($curriculo) || empty($telefone)) {
                return new View('Formulário não submetido ou requisição HTTP inválida.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $candidato = $oldCandidato
                ->setExperiencias($experiencia)
                ->setTelefone($telefone);

            $em->persist($candidato);
            $em->flush();

            return new View($candidato, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/candidato/{id}",
     *     requirements={ "id": "\d+" },
     *     name="remover_candidato"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Remove uma candidato.",
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
    public function deleteAction($id)
    {
        try {
            if (!is_numeric($id) || empty($id)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $candidato = $em->getRepository('AppBundle:Candidato')->find($id);

            if (empty($candidato)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $curriculo = $em->getRepository('AppBundle:Curriculo')->findOneBy(['candidato' => $candidato->getId()]);

            $em->remove($curriculo);
            $em->remove($candidato);
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
