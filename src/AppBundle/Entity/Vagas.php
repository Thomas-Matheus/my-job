<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Candidato;
use AppBundle\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vagas
 *
 * @ORM\Table(name="vagas", indexes={@ORM\Index(name="fk_Vagas_Usuario1_idx", columns={"usuario_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VagaRepository")
 * @ORM\Entity
 */
class Vagas
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
     *     min="3",
     *     minMessage="A vaga não pode ter um nome menor que 3 caractares.",
     *     max="50",
     *     maxMessage="A vaga não pode ter um nome maior que 50 caracteres"
     * )
     * @Assert\NotBlank(message="O nome da vaga não pode ser vazio.")
     *
     * @ORM\Column(name="nome", type="string", length=50, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="A descrição não pode ser vazio.")
     *
     * @ORM\Column(name="descricao", type="text", length=65535, nullable=false)
     */
    private $descricao;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime(message="Data inválida")
     * @Assert\NotBlank(message="O data não pode ser vazio.")
     *
     * @ORM\Column(name="data_inicio", type="datetime", nullable=false)
     */
    private $dataInicio;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime(message="Data inválida")
     * @Assert\NotBlank(message="O data não pode ser vazio.")
     *
     * @ORM\Column(name="data_termino", type="datetime", nullable=false)
     */
    private $dataTermino;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ativo", type="boolean", nullable=false)
     */
    private $ativo = '1';

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
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Candidato", mappedBy="vagas")
     */
    private $candidato;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->candidato = new ArrayCollection();
    }


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Vagas
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
     * Set nome
     *
     * @param string $nome
     *
     * @return Vagas
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
     * Set descricao
     *
     * @param string $descricao
     *
     * @return Vagas
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * Get descricao
     *
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set dataInicio
     *
     * @param \DateTime $dataInicio
     *
     * @return Vagas
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    /**
     * Get dataInicio
     *
     * @return \DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * Set dataTermino
     *
     * @param \DateTime $dataTermino
     *
     * @return Vagas
     */
    public function setDataTermino($dataTermino)
    {
        $this->dataTermino = $dataTermino;

        return $this;
    }

    /**
     * Get dataTermino
     *
     * @return \DateTime
     */
    public function getDataTermino()
    {
        return $this->dataTermino;
    }

    /**
     * Set ativo
     *
     * @param boolean $ativo
     *
     * @return Vagas
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;

        return $this;
    }

    /**
     * Get ativo
     *
     * @return boolean
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     *
     * @return Vagas
     */
    public function setUsuario(Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add candidato
     *
     * @param Candidato $candidato
     *
     * @return Vagas
     */
    public function addCandidato(Candidato $candidato)
    {
        $this->candidato[] = $candidato;

        return $this;
    }

    /**
     * Remove candidato
     *
     * @param Candidato $candidato
     */
    public function removeCandidato(Candidato $candidato)
    {
        $this->candidato->removeElement($candidato);
    }

    /**
     * Get candidato
     *
     * @return Collection
     */
    public function getCandidato()
    {
        return $this->candidato;
    }
}
