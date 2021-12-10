<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobTest extends WebTestCase
{
    /**
     * Log as Candidat
     */
    public function authAsCandidat(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('random@yahoo.fr');
        $client->loginUser($testUser);

        return $client;
    }


    /**
     * Log as Recruter
     */
    public function authAsRecruter(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('another@hotmail.fr');
        $client->loginUser($testUser);

        return $client;
    }

    public function testLogAsRecruterAndCreateAJob(): void
    {
        $client = $this->authAsRecruter();
        $container = static::getContainer();

        $typeRepository = $container->get('doctrine')->getRepository(Type::class);
        $type = $typeRepository->findOneBy(['name' => 'CDI']);

        $categoryRepository = $container->get('doctrine')->getRepository(Category::class);
        $category = $categoryRepository->findOneBy(['name' => 'Autre']);

        $client->request('GET', '/job/new');

        $client->submitForm('Save', [
            'job[name]' => 'TestJob',
            'job[company]' => 'TestCompany',
            'job[type]' => $type->getId(),
            'job[category]' => $category->getId(),
            'job[description]' => 'Test description'
        ]);

        $this->assertResponseRedirects('/my-jobs');
        $client->followRedirect();

        $this->assertSelectorTextContains('.card-job-title', '1 job(s) in total.');

        $client->request('GET', '/search');
        $client->submitForm('Search', [
            'job-where' => '',
            'job-what' => 'TestJob'
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.card-job-title', '1 result(s) found.');
        $this->assertSelectorTextContains('.title-section', 'TestJob');
    }
}
