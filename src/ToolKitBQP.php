<?php

namespace App;

use App\Entity\Event;
use App\Repository\EventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class ToolKitBQP
{
    /**
     * transforme les entités (objet ou string) au format UTF 8
     * @param $d
     * @return mixed|string
     */
    function turnToUTF8($d): mixed
    {
        if (is_array($d))
            foreach ($d as $k => $v)
                $d[$k] = $this->turnToUTF8($v);

        else if (is_object($d))
            foreach ($d as $k => $v)
                $d->$k = $this->turnToUTF8($v);

        else
            return utf8_encode($d);

        return $d;
    }

    /**
     * Archiver un evenement
     * @param Event $event evenement à archiver
     * @param string $mode mode d'archivage qui sera inscrit dans le fichier ('AUTO', 'USER', 'ADMIN', ...)
     * @return void
     */
    public function archive(Event $event, string $mode,EntityManagerInterface $em): void
    {
        //transformation en json
        $json = json_encode($event);
        $today = new DateTime('now');
        $today = json_encode($today->format('d-m-Y H:i:s'));

        //écriture du json dans le fichier archive
        $archiveFile = fopen(realpath("../public/archives.txt"), 'r+');
        fwrite($archiveFile, '\n' . $mode . ' ' . $today) . ' ';
        fwrite($archiveFile, $json);
        fclose($archiveFile);

        $em->remove($event);
        $em->flush();
    }

    /**
     * @param EntityManagerInterface $em
     * @param EventRepository $eventRepo
     * @return void
     */
    public function autoArchive(
        EntityManagerInterface $em,
        EventRepository        $eventRepo
    ): void
    {
        foreach ($eventRepo->findAll() as $event) {
            $today = new DateTime('now');
            $today->modify('+1 month');
            if ($event->getDateFinish() > $today) {
                self::archive($event,'AUTO',$em);
            }
        }
    }


}