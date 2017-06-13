<?php

namespace SecurityApiBundle\Controller;

use AppBundle\Entity\Usuario;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistroController extends FOSRestController
{

    /**
     * @Rest\Post("/cadastrar",
     *     name="cadastrar_usuario"
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @ApiDoc(
     *  statusCodes={
     *      201="Usuário criado.",
     *      400="Há campos vazio ou email já cadastrado.",
     *      500="Erro interno."
     *  },
     *  resource=true,
     *  description="Cadastrar usuário.",
     *  requirements={
     *      {
     *          "name" = "nome",
     *          "dataType" = "string",
     *          "description" = "Nome do usuário."
     *      },
     *      {
     *          "name" = "email",
     *          "dataType" = "string",
     *           "description" = "Email do usuário."
     *      },
     *      {
     *          "name" = "cpf",
     *          "dataType" = "string",
     *           "description" = "CPF do usuário."
     *      },
     *      {
     *          "name" = "password",
     *          "dataType" = "string",
     *           "description" = "Senha do usuário."
     *      },
     *  }
     * )
     */
    public function cadastroAction(Request $request)
    {
        try {
            $nome = $request->get('nome');
            $email = $request->get('email');
            $cpf = $request->get('cpf');
            $pass = $request->get('password');

            if (empty($nome) || empty($email) || empty($pass)) {
                return new View('Campos obrigatóios vazios.', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $searchUser = $em->getRepository('AppBundle:Usuario')->findOneBy(['email' => $email]);
            if (!empty($searchUser)) {
                return new View('O email informado já existe.', Response::HTTP_BAD_REQUEST);
            }

            $usuario = (new Usuario())
                ->setNome($nome)
                ->setEmail($email)
                ->setCpf($cpf)
                ->setRoles('ROLE_USER');

            $encodedPass = $this->get('security.password_encoder')->encodePassword($usuario, $pass);
            $usuario->setPassword($encodedPass);

            $em->persist($usuario);
            $em->flush();
            return new View(['nome' => $usuario->getNome(), 'email' => $usuario->getEmail()],
                Response::HTTP_CREATED
            );
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}