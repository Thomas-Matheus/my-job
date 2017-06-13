<?php

namespace UsuarioApiBundle\Controller;

use AppBundle\Entity\Vagas;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;


class VagasController extends FOSRestController
{
    /**
     * @Rest\Get("/vagas", name="listar_vagas")
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
     *     name="pesquisar_vagas"
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

    /**
     * @Rest\Post("/vaga/", name="cadastrar_vagas")
     * @ApiDoc(
     *  statusCodes={
     *      201="Vaga criada.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Insere uma nova vaga.",
     *  requirements={
     *      {
     *          "name" = "ativo",
     *          "dataType" = "boolean",
     *          "description" = "Define se a vaga está ativa."
     *      },
     *      {
     *          "name" = "dataInicio",
     *          "dataType" = "date",
     *           "description" = "Data de inicio da vaga."
     *      },
     *      {
     *          "name" = "dataFim",
     *          "dataType" = "data",
     *           "description" = "Data de fim da vaga."
     *      },
     *      {
     *          "name" = "nome",
     *          "dataType" = "string",
     *           "description" = "Nome da vaga."
     *      },
     *      {
     *          "name" = "descricao",
     *          "dataType" = "string",
     *           "description" = "Decrição completa da vaga."
     *      }
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        try {
            $nome = $request->get('nome');
            $ativo = empty($request->get('ativo')) ? false : true;
            $dataInicio = $request->get('dataInicio');
            $dataFim = $request->get('dataFim');
            $descricao = $request->get('descricao');

            if (empty($nome) || empty($dataInicio) || empty($dataFim) || empty($descricao)) {
                return new View('Formulário não submetido ou requisição HTTP inválida.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $vaga = (new Vagas())
                ->setAtivo($ativo)
                ->setDataFim($dataFim)
                ->setDataInicio($dataInicio)
                ->setDescricao($descricao)
                ->setNome($nome);

            $em = $this->getDoctrine()->getManager();
            $em->persist($vaga);
            $em->flush();

            return new View($vaga, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Put("/vaga/{id}",
     *     requirements={ "id": "\d+" },
     *     name="atualizar_vagas"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Atualizar uma vaga específica.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID da vaga."
     *      },
     *      {
     *          "name" = "ativo",
     *          "dataType" = "boolean",
     *          "description" = "Define se a vaga está ativa."
     *      },
     *      {
     *          "name" = "dataInicio",
     *          "dataType" = "date",
     *           "description" = "Data de inicio da vaga."
     *      },
     *      {
     *          "name" = "dataFim",
     *          "dataType" = "data",
     *           "description" = "Data de fim da vaga."
     *      },
     *      {
     *          "name" = "nome",
     *          "dataType" = "string",
     *           "description" = "Nome da vaga."
     *      },
     *      {
     *          "name" = "descricao",
     *          "dataType" = "string",
     *           "description" = "Decrição completa da vaga."
     *      }
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
            /** @var Vaga $oldVaga */
            $oldVaga = $em->getRepository('AppBundle:Vagas')->find($id);

            if (empty($oldVaga)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $nome = $request->get('nome');
            $ativo = empty($request->get('ativo')) ? false : true;
            $dataInicio = $request->get('dataInicio');
            $dataFim = $request->get('dataFim');
            $descricao = $request->get('descricao');

            if (empty($nome) || empty($dataInicio) || empty($dataFim) || empty($descricao)) {
                return new View('Formulário não submetido ou requisição HTTP inválida.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $vaga = $oldVaga
                ->setAtivo($ativo)
                ->setDataFim($dataFim)
                ->setDataInicio($dataInicio)
                ->setDescricao($descricao)
                ->setNome($nome);

            $em->persist($vaga);
            $em->flush();

            return new View($vaga, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/vaga/{id}",
     *     requirements={ "id": "\d+" },
     *     name="remover_vaga"
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
            $vaga = $em->getRepository('AppBundle:Vagas')->find($id);

            if (empty($vaga)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $em->remove($vaga);
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