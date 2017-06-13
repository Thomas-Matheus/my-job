<?php

namespace UsuarioApiBundle\Controller;

use AppBundle\Entity\Usuario;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;

class UsuarioController extends FOSRestController
{
    /**
     * @Rest\Get("/usuarios", name="listar_usuario")
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Lista todas os usuarios.",
     * )
     */
    public function cgetAction()
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $usuario = $em->getRepository('AppBundle:Usuario')->findAll();

            if (empty($usuario)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($usuario, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Get("/usuario/{id}",
     *     requirements={ "id": "\d+" },
     *     name="pesquisar_usuario"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Pesquisa um usuario.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do usuario."
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
            $usuario = $em->getRepository('AppBundle:Usuario')->find($id);

            if (empty($usuario)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            return new View($usuario, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/usuario/", name="cadastrar_usuario")
     * @ApiDoc(
     *  statusCodes={
     *      201="Vaga criada.",
     *      400="Dados inválidos.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Insere um usuario.",
     *  requirements={
     *      {
     *          "name" = "nome",
     *          "dataType" = "string",
     *          "description" = "Nome do usuário."
     *      },
     *      {
     *          "name" = "pass",
     *          "dataType" = "string",
     *          "description" = "Senha do usuário."
     *      },
     *      {
     *          "name" = "email",
     *          "dataType" = "string",
     *          "description" = "Email do usuário."
     *      },
     *      {
     *          "name" = "cpf",
     *          "dataType" = "string",
     *          "description" = "CPF do usuário."
     *      },
     *      {
     *          "name" = "role",
     *          "dataType" = "string",
     *          "description" = "Permissões do usuário"
     *      },
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        try {
            $nome = $request->get('nome');
            $pass = $request->get('pass');
            $email = $request->get('email');
            $cpf = $request->get('cpf');
            $role = $request->get('role');

            if (empty($nome)
                || empty($pass)
                || empty($email)
                || empty($cpf)
                || empty($role)
            ) {
                return new View('Arquivo não encontrado.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $usuario = (new Usuario())
                ->setNome($nome)
                ->setEmail($email)
                ->setCpf($cpf)
                ->setRoles([$role]);

            $encodedPass = $this->get('security.password_encoder')->encodePassword($usuario, $pass);
            $usuario->setPassword($encodedPass);

            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            return new View($usuario, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Put("/usuario/{id}",
     *     requirements={ "id": "\d+" },
     *     name="atualizar_usuario"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Atualizar um usuario.",
     *  requirements={
     *      {
     *          "name" = "nome",
     *          "dataType" = "string",
     *          "description" = "Nome do usuário."
     *      },
     *      {
     *          "name" = "pass",
     *          "dataType" = "string",
     *          "description" = "Senha do usuário."
     *      },
     *      {
     *          "name" = "email",
     *          "dataType" = "string",
     *          "description" = "Email do usuário."
     *      },
     *      {
     *          "name" = "cpf",
     *          "dataType" = "string",
     *          "description" = "CPF do usuário."
     *      },
     *      {
     *          "name" = "role",
     *          "dataType" = "string",
     *          "description" = "Permissões do usuário"
     *      },
     *  }
     * )
     */
    public function editAction($id, Request $request)
    {
        try {
            $nome = $request->get('nome');
            $pass = $request->get('pass');
            $email = $request->get('email');
            $cpf = $request->get('cpf');
            $role = $request->get('role');

            if (!is_numeric($id) || empty($id) || empty($file)) {
                return new View('ID inválido ou vazio.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            /** @var Usuario $usuario */
            $usuario = $em->getRepository('AppBundle:Usuario')->find($id);

            if (empty($usuario)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $usuario = $usuario
                ->setNome($nome)
                ->setEmail($email)
                ->setCpf($cpf)
                ->setRoles([$role]);

            $encodedPass = $this->get('security.password_encoder')->encodePassword($usuario, $pass);
            $usuario->setPassword($encodedPass);

            $em->persist($usuario);
            $em->flush();

            return new View($usuario, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/usuario/{id}",
     *     requirements={ "id": "\d+" },
     *     name="remover_usuario"
     * )
     * @ApiDoc(
     *  statusCodes={
     *      202="Request aceito.",
     *      204="Nenhum resultado foi encontrado.",
     *      400="ID inválido ou vazio.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Remove um usuário e o candidato caso tenha.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="ID do usuário."
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
            $usuario = $em->getRepository('AppBundle:Usuario')->find($id);

            if (empty($usuario)) {
                return new View('Nenhum resultado encontrado.', Response::HTTP_NO_CONTENT);
            }

            $candidatos = $em->getRepository('AppBundle:Candidato')->findBy(['usuario' => $usuario->getId()]);

            if (!empty($candidato)) {
                foreach ($candidatos as $candidato) {
                    $em->remove($candidato);
                }
            }

            $em->remove($usuario);
            $em->flush();

            return new View(null, Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
