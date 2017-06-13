<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Candidato;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Curriculo
 *
 * @ORM\Table(name="curriculo", indexes={@ORM\Index(name="fk_Curriculo_Candidato1_idx", columns={"candidato_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CurriculoRepository")
 * @ORM\Entity
 */
class Curriculo
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
     * @ORM\Column(name="path", type="string", length=200, nullable=false)
     */
    private $path;

    /**
     * @var string
     *
     * @Assert\File(
     *     maxSize="2M",
     *     maxSizeMessage = "O tamanho máximo permitido para upload é de 2MB.",
     *     mimeTypes={"application/pdf"},
     *     mimeTypesMessage = "Formato de arquivo inválido."
     * )
     *
     * @ORM\Column(name="arquivo", type="string", length=32, nullable=false)
     */
    private $arquivo;

    /**
     * @var \Candidato
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Candidato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="candidato_id", referencedColumnName="id")
     * })
     */
    private $candidato;



    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Curriculo
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
     * Set path
     *
     * @param string $path
     *
     * @return Curriculo
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set arquivo
     *
     * @param string $arquivo
     *
     * @return Curriculo
     */
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;

        return $this;
    }

    /**
     * Get arquivo
     *
     * @return string
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * Set candidato
     *
     * @param Candidato $candidato
     *
     * @return Curriculo
     */
    public function setCandidato(Candidato $candidato)
    {
        $this->candidato = $candidato;

        return $this;
    }

    /**
     * Get candidato
     *
     * @return Candidato
     */
    public function getCandidato()
    {
        return $this->candidato;
    }
}
