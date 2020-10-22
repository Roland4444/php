<?php
/**
 * Created by PhpStorm.
 * User: Миша
 * Date: 13.05.14
 * Time: 22:19
 */

namespace Reports\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * @ORM\Entity(repositoryClass="\Reports\Repository\ExportRepository")
 * @ORM\Table(name="remote_sklad")
 */
class RemoteSklad implements InputFilterAwareInterface
{
    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;//    bigint

    /**
     * @ORM\Column(name="report_id",type="integer")
     */
    protected $report_id;// bigint

    /**
     * @ORM\Column(name="transport",type="string")
     */
    protected $transport;// varchar

    /**
     * @ORM\Column(name="transnumb",type="string")
     */
    protected $transnumb;// varchar

    /**
     * @ORM\Column(name="recplate",type="string")
     */
    protected $recplate;//  varchar

    /**
     * @ORM\Column(name="naklnumb",type="integer")
     */
    protected $naklnumb;//  int

    /**
     * @ORM\Column(name="date",type="date")
     */
    protected $date;//  date

    /**
     * @ORM\Column(name="massa",type="string")
     */
    protected $massa;// varchar

    /**
     * @ORM\Column(name="gruz",type="string")
     */
    protected $gruz;//  varchar

    /**
     * @ORM\Column(name="sklad",type="string")
     */
    protected $sklad;// varchar

    /**
     * @ORM\Column(name="sor",type="string")
     */
    protected $sor;//   varchar

    /**
     * @ORM\Column(name="brute",type="integer")
     */
    protected $brute;// int

    /**
     * @ORM\Column(name="primesi",type="string")
     */
    protected $primesi;//   varchar

    /**
     * @ORM\Column(name="time",type="string")
     */
    protected $time;//  varchar

    /**
     * @ORM\Column(name="cena",type="string")
     */
    protected $cena;//  varchar

    /**
     * @ORM\Column(name="img",type="blob")
     */
    protected $img;//   mediumblob

    /**
     * @ORM\Column(name="img2",type="blob")
     */
    protected $img2;//  mediumblob

    /**
     * @ORM\Column(name="massaFact",type="integer")
     */
    protected $massaFact;// int

    /**
     * @ORM\Column(name="destination",type="string")
     */
    protected $destination;//   varchar

    /**
     * @ORM\Column(name="comment",type="string")
     */
    protected $comment;//   varchar

    /**
     * @ORM\Column(name="finished",type="boolean")
     */
    protected $finished;//  tinyint

    /**
     * @ORM\Column(name="transfer",type="integer")
     */
    protected $transfer;

    /**
     * @ORM\Column(name="path",type="string")
     */
    protected $path;

    public function getNetto()
    {
        $sor = (100 - $this->getSor()) / 100;
        return round($this->getMassa() / $sor);
    }

    public function getTare()
    {
        return $this->getBrute() - $this->getNetto();
    }
    /**
     * @param mixed $brute
     * @return mixed
     */
    public function setBrute($brute)
    {
        $this->brute = $brute;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrute()
    {
        return $this->brute;
    }

    /**
     * @param mixed $cena
     */
    public function setCena($cena)
    {
        $this->cena = $cena;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCena()
    {
        return $this->cena;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        if (! $this->date) {
            return date('Y-m-d');
        }
        return $this->date;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param mixed $gruz
     */
    public function setGruz($gruz)
    {
        $this->gruz = $gruz;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGruz()
    {
        return $this->gruz;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img2
     */
    public function setImg2($img2)
    {
        $this->img2 = $img2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImg2()
    {
        return $this->img2;
    }

    /**
     * @param mixed $massa
     */
    public function setMassa($massa)
    {
        $this->massa = $massa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMassa()
    {
        return $this->massa;
    }

    /**
     * @param mixed $massaFact
     */
    public function setMassaFact($massaFact)
    {
        $this->massaFact = $massaFact;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMassaFact()
    {
        return $this->massaFact;
    }

    /**
     * @param mixed $naklnumb
     */
    public function setNaklnumb($naklnumb)
    {
        $this->naklnumb = $naklnumb;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNaklnumb()
    {
        return $this->naklnumb;
    }

    /**
     * @param mixed $primesi
     */
    public function setPrimesi($primesi)
    {
        $this->primesi = $primesi;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimesi()
    {
        return $this->primesi;
    }

    /**
     * @param mixed $report_id
     */
    public function setReportId($report_id)
    {
        $this->report_id = $report_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReportId()
    {
        return $this->report_id;
    }

    /**
     * @param mixed $sklad
     */
    public function setSklad($sklad)
    {
        $this->sklad = $sklad;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSklad()
    {
        return $this->sklad;
    }

    /**
     * @param mixed $sor
     */
    public function setSor($sor)
    {
        $this->sor = $sor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSor()
    {
        return $this->sor;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $transfer
     */
    public function setTransfer($transfer)
    {
        $this->transfer = $transfer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * @param mixed $transnumb
     */
    public function setTransnumb($transnumb)
    {
        $this->transnumb = $transnumb;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransnumb()
    {
        return str_replace(' ', '', mb_strtoupper($this->transnumb, "UTF-8"));
    }

    /**
     * @param mixed $transnumb
     */
    public function setRecPlate($plate)
    {
        $this->recplate = $plate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecPlate()
    {
        return $this->recplate;
    }

    /**
     * @param mixed $transport
     * @return mixed
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getInputFilter()
    {
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
    }
}
