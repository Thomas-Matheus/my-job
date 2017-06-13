<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRepository")
 * @ORM\Entity
 */
class Usuario implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Nome do usuário não pode ser vazio.")
     * @Assert\Length(
     *     min="3",
     *     minMessage="O nome do usuário não pode ser menor do que 3.",
     *     max="45",
     *     maxMessage="O nome do usuário não pode ser maior do que 45."
     * )
     *
     * @ORM\Column(name="nome", type="string", length=45, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="O email não pode ser vazio.")
     * @Assert\Email(
     *     message = "O email '{{ value }}' não é válido.",
     *     checkMX = true
     * )
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=6,
     *     minMessage="A senha deve possuir no minimo 6 caracteres."
     * )
     * @SecurityAssert\UserPassword(
     *     message = "Senha inválida."
     * )
     *
     * @ORM\Column(name="password", type="string", length=60, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=45, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min="11",
     *     minMessage="O cpf do candidato deve conter 11 caracteres.",
     *     max="11",
     *     maxMessage="O cpf do candidato deve conter 11 caracteres."
     * )
     * @Assert\NotBlank(message="O cpf do candidato não pode ser vazio.")
     *
     * @ORM\Column(name="cpf", type="string", length=11, nullable=false)
     */
    private $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=11, nullable=false)
     */
    private $roles;

    /**
     * Usuario constructor.
     */
    public function __construct()
    {
        $this->salt = md5(uniqid(mt_rand()), false);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nome
     *
     * @param string $nome
     *
     * @return Usuario
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set cpf
     *
     * @param string $cpf
     *
     * @return Usuario
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get cpf
     *
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = empty($roles) ? 'ROLE_USER' : $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {

    }

}
