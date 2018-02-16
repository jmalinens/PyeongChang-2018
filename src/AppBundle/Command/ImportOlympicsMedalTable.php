<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use AppBundle\Entity\OlympicsMedals;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportOlympicsMedalTable extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import-medal-table')
            ->setDescription('Import 2018 Winter Olympics medal table from Wikipedia.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($html = $this->getContent('https://en.wikipedia.org/wiki/2018_Winter_Olympics#Medal_table')) {

            // Parse Wikipedia page
            $crawler = new Crawler($html);
            $crawler = $crawler->filter('#mw-content-text h3, #mw-content-text table.wikitable');

            $table = $table_html = false;

            // Iterate over filter results
            foreach ($crawler as $node) {
                // Find "Medal table" heading, next are our table
                if (!$table && $node->nodeName == 'h3' && strpos($node->nodeValue, 'Medal table') !== false) {
                    $table = true;
                } else if ($table && $node->nodeName === 'table') {
                    // Get "Medal table" html
                    $table_html = $node->ownerDocument->saveHTML($node);
                    break;
                }
            }

            // Parse "Medal table" html
            $crawler = new Crawler($table_html);
            $rows = array();

            $tr_elements = $crawler->filter('table tr');

            // Iterate over filter results
            foreach ($tr_elements as $content) {
                $tds = array();

                // Create crawler instance for result
                $crawler = new Crawler($content);

                // Iterate again
                $noc = false;
                foreach ($crawler->filter('td') as $i => $node) {
                    // Find National Olympic Committee column
                    if (!$noc && $node->hasAttribute('align')) $noc = true;
                    if (!$noc) continue;

                    // Extract the value
                    $tds[] = trim(html_entity_decode($node->nodeValue), " \t\n\r\0\x0B\xC2\xA0");

                }

                if (!empty($tds)) $rows[] = $tds;
            }

            // Table column key => DB column name
            $mapping = array(0 => 'title', 1 => 'gold', 2 => 'silver', 3 => 'bronze');

            $em = $this->getContainer()->get('doctrine.orm.entity_manager');

            foreach ($rows as $row) {
                // Search National Olympic Committee by title
                $medals = $em->getRepository(OlympicsMedals::class)->findOneByTitle($row[0]);

                // Create new if not exist
                if (!$medals) {
                    $medals = new OlympicsMedals();
                }

                // Iterate trough columns and set values
                foreach ($row as $k => $v) {
                    if (!isset($mapping[$k])) continue;
                    $function = 'set'.ucfirst($mapping[$k]);
                    $medals->$function($v);
                }

                $em->persist($medals);
            }

            $em->flush();
        }
    }

    private function getContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}