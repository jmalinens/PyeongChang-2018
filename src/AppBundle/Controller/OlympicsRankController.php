<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\OlympicsMedals;
use Symfony\Component\HttpFoundation\Request;

class OlympicsRankController extends Controller
{
    /**
     * @Route("/rank/", name="olympics_rank")
     * @Route("/rank/{type}/", name="olympics_rank_type")
     */
    public function rankAction(Request $request, $type = '')
    {
        switch ($type) {
            case 'value':
                $results = $this->getRankListValue();
                $heading = 'medal value';
                break;

            default:
                $results = $this->getRankListTotal('DESC');
                $heading = 'number of medals';
                break;
        }

        return $this->render('olympics/rank.html.twig', [
            'results' => $results,
            'heading' => $heading,
            'total_medals' => $this->getTotalMedals(),
            'back_url' => $this->generateUrl('homepage')
        ]);
    }

    /**
     * Get rank list by medals value
     *
     * @param string $order
     * @return array
     */
    public function getRankListValue($order = 'ASC') {
        $return = array();
        $em = $this->getDoctrine()->getManager();

        $order = ($order === 'ASC' ? $order : 'DESC');

        if ($results = $em->createQuery("SELECT p, (p.gold * 3 + p.silver * 2 + p.bronze * 1) total
            FROM AppBundle:OlympicsMedals p
            ORDER BY total $order"
        )
            ->getArrayResult()) {
            $return = $results;
        }

        return $return;
    }

    /**
     * Get rank list by total number of medals
     *
     * @param string $order
     * @return array
     */
    public function getRankListTotal($order = 'ASC') {
        $return = array();
        $em = $this->getDoctrine()->getManager();

        $order = ($order === 'ASC' ? $order : 'DESC');

        if ($results = $em->createQuery("SELECT p, (p.gold + p.silver + p.bronze) total
            FROM AppBundle:OlympicsMedals p
            ORDER BY total $order"
        )
            ->getArrayResult()) {
            $return = $results;
        }

        return $return;
    }

    /**
     * Get total medals
     *
     * @return int
     */
    public function getTotalMedals() {
        $return = 0;
        $em = $this->getDoctrine()->getManager();

        // Total medals results
        if ($results = $em->createQuery("SELECT (SUM(p.gold) + SUM(p.silver) + SUM(p.bronze)) 
            FROM AppBundle:OlympicsMedals p")
            ->setMaxResults(1)
            ->getSingleScalarResult()) {
            $return = $results;
        }

        return $return;
    }
}

