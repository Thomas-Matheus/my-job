<?php

namespace SecurityApiBundle\Security;


use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var JWTEncoderInterface
     */
    private $encoder;

    /**
     * JwtAuthenticator constructor.
     * @param EntityManager $em
     * @param JWTEncoderInterface $encoder
     */
    public function __construct(EntityManager $em, JWTEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $message = empty($authException) ? $authException->getMessageKey() : 'Credenciais não encontradas.';
        return new JsonResponse(
            ['info' => $message],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function getCredentials(Request $request)
    {
        if (empty($request->headers->has('Authorization'))) {
            return;
        }

        $tokenExtrator = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

        $token = $tokenExtrator->extract($request);

        if (empty($token)) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $credenciais = $this->encoder->decode($credentials);

        if (empty($credenciais)) {
            throw new CustomUserMessageAuthenticationException('Token inválido.');
        }

        $user = $this->em->getRepository('AppBundle:Usuario')
            ->findOneBy(['email' => $credenciais['username']]);

        if (empty($user)) {
            return;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(
            ['message' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }

}