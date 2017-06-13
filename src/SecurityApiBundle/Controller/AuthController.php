<?php

namespace SecurityApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends FOSRestController
{

    /**
     * @Rest\Post("/token-authentication", name="token_authentication")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function tokenAuthenticationAction(Request $request)
    {
        try {
            $username = $request->get('username');
            $password = $request->get('password');

            if (empty($username) || empty($password)) {
                return new View('Usuário ou senha inválidos', Response::HTTP_BAD_REQUEST);
            }

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:Usuario')->findOneBy(['email' => $username]);

            if (empty($user)) {
                return new View('Usuário inválido', Response::HTTP_BAD_REQUEST);
            }

            $isPassValid = $this->get('security.password_encoder')->isPasswordValid($user, $password);

            if (false === $isPassValid) {
                return new View('Usuário inválido', Response::HTTP_FORBIDDEN);
            }

            $token = $this->get('lexik_jwt_authentication.encoder')->encode([
                'username' => $user->getEmail(),
                'role' => $user->getRoles(),
                'exp' => time() + 3600
            ]);
            return new View(['token' => $token], Response::HTTP_ACCEPTED);
        } catch (\Throwable $e) {
            /**
             * @todo seria bacana monitorar o erro no Sentry
             * @see https://sentry.io/
             */
            return new View($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
