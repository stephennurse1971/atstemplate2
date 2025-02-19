<?php


namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportUserService
{
    public function importUsers(string $fileName)
    {
        $directories = [
            $this->container->getParameter('users_import_directory'),
            $this->container->getParameter('project_set_up_import_directory')
        ];
        $fullpath = null;
        foreach ($directories as $directory) {
            $potentialPath = $directory . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($potentialPath)) {
                $fullpath = $potentialPath;
                break;
            }
        }
        if (!$fullpath) {
            throw new \Exception("File not found in either directory: $fileName");
        }

        $alldataFromCsv = [];
        $row = 0;

        if (($handle = fopen($fullpath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                if ($row == 0) {
                    $row++;
                    continue;
                }
                $num = count($data);
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    $all_data_from_csv[$row][] = $data[$c];
                }
            }
            fclose($handle);
        }
        foreach ($all_data_from_csv as $oneLineFromCsv) {
            $entity = trim($oneLineFromCsv[0]);
            $salutation = trim($oneLineFromCsv[1]);
            $firstName = trim($oneLineFromCsv[2]);
            $lastName = trim($oneLineFromCsv[3]);
            $emailVerified = trim(strtolower($oneLineFromCsv[4]));
            $defaultLanguage = trim(strtolower($oneLineFromCsv[5]));
            $linkedIn = trim(strtolower($oneLineFromCsv[6]));
            $jobTitle = trim(strtolower($oneLineFromCsv[7]));
            $company = trim(strtolower($oneLineFromCsv[8]));
            $birthday = trim(strtolower($oneLineFromCsv[9]));
            $webpage = trim(strtolower($oneLineFromCsv[10]));
            $businessStreet = trim(strtolower($oneLineFromCsv[11]));
            $businessCity = trim(strtolower($oneLineFromCsv[12]));
            $businessPostCode = trim(strtolower($oneLineFromCsv[13]));
            $businessCountry = trim(strtolower($oneLineFromCsv[14]));
            $homeStreet = trim(strtolower($oneLineFromCsv[15]));
            $homeCity = trim(strtolower($oneLineFromCsv[16]));
            $homePostCode = trim(strtolower($oneLineFromCsv[17]));
            $homeCountry = trim(strtolower($oneLineFromCsv[18]));
            $mobile1 = trim(strtolower($oneLineFromCsv[19]));
            $mobile2 = trim(strtolower($oneLineFromCsv[20]));
            $businessPhone = trim(strtolower($oneLineFromCsv[21]));
            $homePhone = trim(strtolower($oneLineFromCsv[22]));
            $email = trim(strtolower($oneLineFromCsv[23]));
            $email2 = trim(strtolower($oneLineFromCsv[24]));
            $email3 = trim(strtolower($oneLineFromCsv[25]));
            $notes = trim(strtolower($oneLineFromCsv[26]));

            if (!$email ) {
                $email = $firstName . $lastName . "NoEmail@no_email.com";
            }

            $existing_user = $this->userRepository->findOneBy(['email' => $email]);

            if ($existing_user and $entity="Users") {
                $existing_user->setFirstName($firstName);
                $existing_user->setLastName($lastName);
            } else {
                $new_user = new User();
                $new_user->setEmail($email)
                    ->setSalutation($salutation)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setEmailVerified($emailVerified)
//                    ->setDefaultLanguage($defaultLanguage)
                    ->setLinkedIn($linkedIn)
                    ->setLinkedIn($jobTitle)
                    ->setLinkedIn($company)
                    ->setLinkedIn($birthday)
                    ->setLinkedIn($webpage)
                    ->setBusinessStreet($businessStreet)
                    ->setBusinessCity($businessCity)
                    ->setBusinessPostalCode($businessPostCode)
                    ->setBusinessCountry($businessCountry)
                    ->setHomeStreet($homeStreet)
                    ->setHomeCity($homeCity)
                    ->setHomePostalCode($homePostCode)
                    ->setHomeCountry($homeCountry)
                    ->setMobile($mobile1)
                    ->setMobile2($mobile2)
                    ->setBusinessPhone($businessPhone)
                    ->setHomePhone($homePhone)
                    ->setEmail($email)
                    ->setEmail2($email2)
                    ->setEmail3($email3)
                    ->setNotes($notes)


                    ->setRoles(['ROLE_USER'])
                    ->setPassword('password')
                    ->setEmailVerified(true);
                $this->manager->persist($new_user);
                $this->manager->flush();
            }
        }

        $this->manager->flush();
        return null;

    }

    public
    function __construct(ContainerInterface $container, UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->userRepository = $userRepository;
    }
}
