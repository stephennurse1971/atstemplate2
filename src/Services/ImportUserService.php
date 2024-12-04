<?php


namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportUserService
{
    public function importUser(string $fileName)
    {
        $firstName = '';
        $lastName = '';
        $email = '';
        $mobile = '';

        $filepath = $this->container->getParameter('user_import_directory');
        $fullpath = $filepath . $fileName;
        $all_data_from_csv = [];
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
            $firstName = trim($oneLineFromCsv[0]);
            $lastName = trim($oneLineFromCsv[1]);
            $email = trim(strtolower($oneLineFromCsv[2]));
//            $mobile = trim(strtolower($oneLineFromCsv[3]));

            if (!$email) {
                $email = $firstName . $lastName . "NoEmail@no_email.com";
            }

            $old_user = $this->userRepository->findOneBy(['email' => $email]);
            if ($old_user) {
                $old_user->setFirstName($firstName);
                $old_user->setLastName($lastName);
            } else {
                $new_user = new User();
                $new_user->setEmail($email)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
//                    ->setMobile($mobile)
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
