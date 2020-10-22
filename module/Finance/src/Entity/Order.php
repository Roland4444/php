<?php

namespace Finance\Entity;

class Order
{
    private $date;
    private $sourceInn;
    private $destInn;
    private $money;
    private $number;
    private $source;
    private $dest;
    private $comment;
    private $recipient;
    private $sender;
    private $type;

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return date("Y-m-d", strtotime($this->date));
    }

    public function setSourceInn($inn)
    {
        $this->sourceInn = $inn;
    }

    public function getSourceInn()
    {
        return $this->sourceInn;
    }

    public function setDestInn($inn)
    {
        $this->destInn = $inn;
    }

    public function getDestInn()
    {
        return trim($this->destInn);
    }

    public function setMoney($money)
    {
        $this->money = $money;
    }

    public function getMoney()
    {
        return $this->money;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setDest($dest)
    {
        $this->dest = $dest;
    }

    public function getDest()
    {
        return $this->dest;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
}
