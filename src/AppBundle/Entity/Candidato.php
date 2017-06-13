<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Candidato
 *
 * @ORM\Table(name="candidato", indexes={@ORM\Index(name="fk_Candidato_Usuario1_idx", columns={"usuario_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CandidatoRepository")
 * @ORM\Entity
 */
class Candidato
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
     * @Assert\Length(
     *     min="8",
     *     minMessage="O telefone do candidato deve conter 9 caracteres."
     *     max="15",
     *     maxMessage="O telefone do candidato deve conter 15 caracteres."
     * )
     * @Assert\NotBlank(message="O telefone do candidato não pode ser vazio.")
     *
     * @ORM\Column(name="telefone", type="string", length=45, nullable=false)
     */
    private $telefone;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min="3",
     *     minMessage="As experiencia do candidato deve conter 9 caracteres."
     *     max="500",
     *     maxMessage="As experiencia do candidato deve conter 15 caracteres."
     * )
     * @Assert\NotBlank(message="As experiencias do candidato não pode ser vazio.")
     *
     * @ORM\Column(name="experiencias", type="string", length=500, nullable=false)
     */
    private $experiencias;

    /**
     * @var \Usuario
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Vagas", inversedBy="candidato")
     * @ORM\JoinTable(name="interessados",
     *   joinColumns={
     *     @ORM\JoinColumn(name="candidato_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="vagas_id", referencedColumnName="id")
     *   }
     * )
     */
    private $vagas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vagas = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Candidato
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set telefone
     *
     * @param string $telefone
     *
     * @return Candidato
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;

        return $this;
    }

    /**
     * Get telefone
     *
     * @return string
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Set experiencias
     *
     * @param string $experiencias
     *
     * @return Candidato
     */
    public function setExperiencias($experiencias)
    {
        $this->experiencias = $experiencias;

        return $this;
    }

    /**
     * Get experiencias
     *
     * @return string
     */
    public function getExperiencias()
    {
        return $this->experiencias;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return Candidato
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add vaga
     *
     * @param \AppBundle\Entity\Vagas $vaga
     *
     * @return Candidato
     */
    public function addVaga(\AppBundle\Entity\Vagas $vaga)
    {
        $this->vagas[] = $vaga;

        return $this;
    }

    /**
     * Remove vaga
     *
     * @param \AppBundle\Entity\Vagas $vaga
     */
    public function removeVaga(\AppBundle\Entity\Vagas $vaga)
    {
        $this->vagas->removeElement($vaga);
    }

    /**
     * Get vagas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVagas()
    {
        return $this->vagas;
    }
}
