<?php

namespace UsuarioApiBundle\Controller;

use AppBundle\Entity\Curriculo;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;

class CurriculoController extends FOSRestController
{

    /**
     * @Rest\Get("/curriculos", name="listar_curriculo")
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Lista todas os curriculos.",
     * )
     */
    public function cgetAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $curriculos = $em->getRepository('AppBundle:Curriculo')->findAll();

            if (empty($curriculos)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($curriculos, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/curriculo/{id}",
     *     requirements={ "id": "\d+" },
     *     name="pesquisar_curriculo"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Pesquisa uma curriculo.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do curriculo."
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
            $curriculo = $em->getRepository('AppBundle:Curriculo')->find($id);

            if (empty($curriculo)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($curriculo, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/curriculo/", name="cadastrar_curriculo")
     * @ApiDoc(
     *  statusCodes={
     *      201="Vaga criada.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Insere uma novo.",
     *  requirements={
     *      {
     *          "name" = "candidato",
     *          "dataType" = "string",
     *          "description" = "ID do candidato."
     *      },
     *      {
     *          "name" = "arquivo",
     *          "dataType" = "file",
     *          "description" = "Arquivo a enviado."
     *      },
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        try {
            $file = $request->files->get('arquivo');
            $candidato = $request->get('candidato');

            if (empty($file)) {
                return new View('Arquivo não encontrado.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $em = $this->getDoctrine()->getManager();
            $cand = $em->getRepository('AppBundle:Candidato')->find($candidato);

            if (empty($cand)) {
                return new View('Candidato não encontrado.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $fileUploaded = $this->get('api.usuario.file_manager')->upload($file);
            $path = $this->getParameter('file_uploaded_directory');

            $curriculo = (new Curriculo())
                ->setPath($path)
                ->setArquivo($fileUploaded)
                ->setCandidato($cand)
            ;

            $em->persist($curriculo);
            $em->flush();

            return new View($curriculo, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Put("/curriculo/{id}",
     *     requirements={ "id": "\d+" },
     *     name="atualizar_curriculo"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Atualizar uma curriculo.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID da vaga."
     *      },
     *      {
     *          "name" = "arquivo",
     *          "dataType" = "file",
     *          "description" = "Arquivo a enviado."
     *      },
     *  }
     * )
     */
    public function editAction($id, Request $request)
    {
        try {
            $file = $request->files->get('arquivo');
            if (!is_numeric($id) || empty($id) || empty($file)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            /** @var Curriculo $curriculo */
            $curriculo = $em->getRepository('AppBundle:Curriculo')->find($id);

            if (empty($curriculo)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $fileUploaded = $this->get('api.usuario.file_manager')->updateFile($curriculo->getArquivo(), $file);

            $curriculo->setArquivo($fileUploaded);
            $em->persist($curriculo);
            $em->flush();

            return new View($curriculo, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/curriculo/{id}",
     *     requirements={ "id": "\d+" },
     *     name="remover_curriculo"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Remove um curriculo.",
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
            /** @var Curriculo $curriculo */
            $curriculo = $em->getRepository('AppBundle:Curriculo')->find($id);

            if (empty($curriculo)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $this->get('api.usuario.file_manager')->unlinkFile($curriculo->getArquivo());
            $em->remove($curriculo);
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
