<?php

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UsuarioRepository extends EntityRepository implements UserLoaderInterface
{

    /**
     * @param string $username
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $user) {
            $username = sprintf(
                'O usuário "%s" não foi encontrado.',
                $username
            );

            throw new UsernameNotFoundException($username);
        }

        return $user;
    }
}