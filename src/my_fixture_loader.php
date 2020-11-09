<?php

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NGD\Api\DocumentaryBaseBundle\Entity\Role;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class LoadRoles
 */
class my_fixture_loader implements FixtureInterface
{
    /**
     * Load
     *
     * @param ObjectManager $manager manager
     *
     * @return null
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        // Get output
        $output = new ConsoleOutput();
        $output->setFormatter(new OutputFormatter(true));

        // Echo start message
        $now = new \DateTime();
        $output->writeln(
            '<comment>Start importing roles : '.$now->format('d-m-Y G:i:s').'</comment>'
        );

        // Get file
        $csvFile = dirname(__FILE__).'/data/fixtures.csv';

        if (!file_exists($csvFile)) {
            throw new \Exception('Fixtures file does not exist');
        }

        // open file
        $fixtures = fopen($csvFile, 'r');

        // Skip first line
        fgetcsv($fixtures);

        // Define some vars
        // Count line and remove first line
        $size = count(file($csvFile)) - 1;

        while (!feof($fixtures)) {
            $line = fgetcsv($fixtures, null, ";");

            if (empty($line)) {
                continue;
            }

            if (count($line) != 5) {
                throw new \Exception('Bad line !');
            }

            $newRole = new Role();
            $newRole->setName($line[0]);
            $newRole->setTechnicalName($line[1]);

            if (!empty($line[2])) {
                $newRole->setDescription($line[2]);
            }

            $newRole->setIsRestrictive($line[3] == 'true' ? true : false);

            if (!empty($line[4])) {
                $newRole->setDocumentTypeNames(explode('|', $line[4]));
            }

            // Persisting the current user
            $manager->persist($newRole);
        }

        // Flushing and clear data on queue
        $manager->flush();
        $manager->clear();

        $now = new \DateTime();
        $output->writeln('');
        $output->writeln(
            '<comment>End importing roles : '.$now->format('d-m-Y G:i:s').'</comment>'
        );
    }
}
