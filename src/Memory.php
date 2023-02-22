<?php

namespace App;

use App\Entity\Event;
use Symfony\Component\HttpFoundation\InputBag;

/*
 * service de mémorisation d'un objet App\Event
 * depuis un objet HTTPFoundation/request fourni par controller
 */

class Memory
{
    private static $userevent = [];
    //memoriser le paquet parametres d'une requete

    /**
     * @param string $email
     * @param InputBag $array
     */
    public function write($email, $requestToSave)
    {
        $request = clone $requestToSave;
        $event = new Event();
        $event->setName($request->get('mem_name'));
        //transformation de la chaine date en datetime si non null
        if ($dateStart = $request->get('mem_dateStart')) {
            $dateStart = new \DateTime(date('Y-m-d h:i:s', strtotime($dateStart)));
            $event->setDateStart($dateStart);
        }
        if ($dateFinish = $request->get('mem_dateFinish')) {
            $dateFinish = new \DateTime(date('Y-m-d h:i:s', strtotime($dateFinish)));
            $event->setDateFinish($dateFinish);
        }
        if ($dateLimit = $request->get('mem_dateLimit')) {
            $dateLimit = new \DateTime(date('Y-m-d h:i:s', strtotime($dateLimit)));
            $event->setDateLimit($dateLimit);
        }
        $event->setPeopleMax(intval($request->get('mem_peopleMax')));
        $event->setDescription($request->get('mem_description'));
        self::$userevent[$email] = $event;
//                dd(self::$userevent);
    }


    /**
     * @param $email
     * @return void
     */
    public
    function clear($email)
    {
        if (isset(self::$userevent[$email])) {
            unset(self::$userevent[$email]);
        }
    }

    /**
     * @param $email
     * @return Event
     */
    public
    function createAnEvent($email)
    {
//        dd(self::$userevent);
        if (isset(self::$userevent[$email])) {
            return self::$userevent[$email];
        }
        return new Event();

    }
}