<?php

namespace App;

use App\Entity\Event;
use Symfony\Component\HttpFoundation\InputBag;

class Memory
{
    private $userevent = [];
    //memoriser le paquet parametres d'une requete

    /**
     * @param string $email
     * @param InputBag $array
     */
    public function write($email, $request)
    {
        $this->userevent[$email] = $request;
    }

    /**
     * @param $email
     * @return InputBag|null
     */
    public function read($email): ?InputBag
    {
        if (isset($this->userevent[$email])) {
            return $this->userevent[$email];
        }
        return null;
    }

    /**
     * @param $email
     * @return void
     */
    public function clear($email)
    {
        if (isset($this->userevent[$email])) {
            unset($this->userevent[$email]);
        }
    }


    /**
     * @param $email
     * @return Event
     */
    public function createAnEvent($email)
    {
        $event = new Event();
        if (isset($this->userevent[$email])){
            $event->setName($this->userevent['mem_name']);
            $event->setDateStart($this->userevent['mem_dateStart']);
            $event->setDateFinish($this->userevent['mem_dateFinish']);
            $event->setDateLimit($this->userevent['mem_dateLimit']);
            $event->setPeopleMax($this->userevent['mem_peopleMax']);
            $event->setDescription($this->userevent['mem_description']);
        }
        return $event;
    }
}