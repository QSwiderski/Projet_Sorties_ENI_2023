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
        if ($dateStart = $request->get('mem_dateStart')) {
            $event->setDateStart(date_create_from_format('d/m/Y H:i:s', $dateStart));
        }
        if ($dateFinish = $request->get('mem_dateFinish')) {
            $event->setDateFinish(date_create_from_format('d/m/Y H:i:s', $dateFinish));
        }
        if ($dateLimit = $request->get('mem_dateLimit')) {
            $event->setDateLimit(date_create_from_format('d/m/Y H:i:s', $dateLimit));
        }
        $event->setPeopleMax(intval($request->get('mem_peopleMax')));
        $event->setDescription($request->get('mem_description'));
        self::$userevent[$email] = $event;
        //        dd(self::$userevent);
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