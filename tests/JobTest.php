<?php

namespace App\Tests;

use App\Entity\Application;
use App\Entity\Job;
use App\Repository\ApplicationRepository;
use App\Repository\CategoryRepository;
use App\Repository\JobRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class JobTest extends WebTestCase
{
    use MailerAssertionsTrait;

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
     * Log as Another Recruter
     */
    public function authAsAnotherRecruter(): \Symfony\Bundle\FrameworkBundle\KernelBrowser
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('anotherRecruter@hotmail.fr');
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


    /**
     * Create a job
     */
    public function createJob(string $name): Job
    {
        $container = static::getContainer();

        $jobManager = static::getContainer()->get('doctrine')->getManagerForClass(Job::class);

        $typeRepository = $container->get(TypeRepository::class);
        $type = $typeRepository->findOneBy(['name' => 'CDI']);

        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findOneBy(['name' => 'Autre']);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $recruter = $userRepository->findOneByEmail('another@hotmail.fr');

        $job = new Job;
        $job->setName($name);
        $job->setCompany('Test company');
        $job->setDescription('The test description');
        $job->setIsRemote(false);
        $job->setCategory($category);
        $job->setType($type);
        $job->setRecruter($recruter);

        $jobManager->persist($job);
        $jobManager->flush();

        return $job;
    }


    public function testLogAsRecruterAndCreateAJob(): void
    {
        $client = $this->authAsRecruter();
        $container = static::getContainer();

        $typeRepository = $container->get(TypeRepository::class);
        $type = $typeRepository->findOneBy(['name' => 'CDI']);

        $categoryRepository = $container->get(CategoryRepository::class);
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

        $jobRepository = static::getContainer()->get(JobRepository::class);
        $job = $jobRepository->findOneBy(['name' => 'TestJob']);

        $this->assertNotNull($job);
        $this->assertInstanceOf(Job::class, $job);
    }


    public function testLogAsCandidatAndApply(): void
    {
        $client = $this->authAsCandidat();
        $job = $this->createJob('Test Job');

        $client->request('GET', "/job/{$job->getSlug()}");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains('.btn', 'Already applied');

        $crawler = $client->clickLink('Apply');
        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Apply');
        $form = $buttonCrawlerNode->form();
        $form['application[description]'] = "Hello, I'm just a test!";

        $filePath = 'public' . DIRECTORY_SEPARATOR .
            'uploads' . DIRECTORY_SEPARATOR .
            'tests' . DIRECTORY_SEPARATOR .
            'TestCase.pdf';

        if (file_exists($filePath)) {
            $form['application[files][0]']->upload($filePath);
        }

        $client->submit($form);

        $this->assertResponseRedirects("/job/{$job->getSlug()}/application/success");
        $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'You successfully applied to ' . $job->getName());

        $applicationRepository = static::getContainer()->get(ApplicationRepository::class);
        $application = $applicationRepository->findOneBy(['description' => "Hello, I'm just a test!"]);

        $this->assertNotNull($application);
        $this->assertInstanceOf(Application::class, $application);
    }


    public function testShowApplicationAsRecruterWithFile(): void
    {
        $client = $this->authAsRecruter();
        $client->request('GET', '/my-jobs');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'See candidats (1)');

        $client->clickLink('See candidats (1)');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.card-job-title', '1 candidat(s)');

        $client->clickLink('Show');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('.card-job-info', "Hello, I'm just a test!");
        $this->assertSelectorTextNotContains('.card-job-info', "The candidat applied without any file.");

        $client->clickLink('TestCase (pdf)');
        $this->assertResponseIsSuccessful();
    }


    public function testLogAsCandidatAndApplyToAnAlreadyAppliedJob(): void
    {
        $client = $this->authAsCandidat();
        $job = $this->createJob('Test Job Another One');

        $client->request('GET', "/job/{$job->getSlug()}/application/new");
        $client->submitForm('Apply', [
            'application[description]' => "Hello, I'm just a test !"
        ]);
        $this->assertResponseRedirects("/job/{$job->getSlug()}/application/success");
        $client->followRedirect();

        $client->request('GET', "/job/{$job->getSlug()}");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.btn', 'Already applied');

        $client->request('GET', "/job/{$job->getSlug()}/application/new");
        $this->assertResponseRedirects('/');
    }


    public function testDeniedAccessCreateJobAsCandidat(): void
    {
        $this->expectException(AccessDeniedException::class);

        $client = $this->authAsCandidat();
        $client->catchExceptions(false);
        $client->request('GET', '/job/new');
    }


    public function testDeniedAccessMyJobRouteAsCandidat(): void
    {
        $client = $this->authAsCandidat();
        $client->request('GET', '/my-jobs');
        $this->assertResponseRedirects('/');
    }


    public function testDeniedAccessMyApplicationRouteAsRecruter(): void
    {
        $client = $this->authAsRecruter();
        $client->request('GET', '/my-applications');
        $this->assertResponseRedirects('/');
    }


    public function testDeniedAccessEditJobAsCandidat(): void
    {
        $this->expectException(AccessDeniedException::class);

        $client = $this->authAsCandidat();
        $job = $this->createJob('Test Job For Denied Access Edit Job as Candidat');
        $client->catchExceptions(false);
        $client->request('GET', "/job/{$job->getSlug()}/edit");
    }


    public function testDeniedAccessApplyAsRecruter(): void
    {
        $client = $this->authAsRecruter();
        $job = $this->createJob('Test Job For Denied Access as Recruter');
        $client->request('GET', "/job/{$job->getSlug()}/application/new");
        $this->assertResponseRedirects('/');
    }


    public function testGettingEmailsWhenApplying(): void
    {
        $client = $this->authAsCandidat();
        $job = $this->createJob('Another Job');

        $client->request('GET', "/job/{$job->getSlug()}/application/new");
        $this->assertResponseIsSuccessful();

        $client->submitForm('Apply', [
            'application[description]' => "Hello, I'm just a test!"
        ]);

        $this->assertEmailCount(2);

        $email = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($email, 'To', 'another@hotmail.fr');
        $this->assertEmailHeaderSame($email, 'Subject', 'Someone applied to your job!');
        $this->assertEmailHeaderSame($email, 'Sender', 'jobinator-renew@gmail.com');

        $email = $this->getMailerMessage(1);

        $this->assertEmailHeaderSame($email, 'To', 'random@yahoo.fr');
        $this->assertEmailHeaderSame($email, 'Subject', 'You applied to a job!');
        $this->assertEmailHeaderSame($email, 'Sender', 'jobinator-renew@gmail.com');
    }


    public function testEditOtherRecrutersJobAsRecruter(): void
    {
        $this->expectException(AccessDeniedException::class);

        $client = $this->authAsAnotherRecruter();
        // Here the job owner is not the current user
        $job = $this->createJob('Another job');

        $client->catchExceptions(false);
        $client->request('GET', "/job/{$job->getSlug()}/edit");
    }

    // Not working, can't finf the 'application[files][1]' field for some reason
    // https://symfony.com/doc/current/testing.html#submitting-forms
    /*
    public function testMoreThan3FilesForApplicationForm(): void {
        $client = $this->authAsCandidat();
        $job = $this->createJob('A new job');
        
        $crawler = $client->request('GET', "/job/{$job->getSlug()}/application/new");
        $this->assertResponseIsSuccessful();

        $this->expectException(FileException::class);

        $buttonCrawlerNode = $crawler->selectButton('Apply');
        $form = $buttonCrawlerNode->form();
        $form['application[description]'] = "Hello, I test if I can put more than 3 files in this form";

        $filePath = 'public' . DIRECTORY_SEPARATOR .
            'uploads' . DIRECTORY_SEPARATOR .
            'tests' . DIRECTORY_SEPARATOR .
            'TestCase.pdf';

        if (file_exists($filePath)) {
            $form['application[files][0]']->upload($filePath);
            $form['application[files][1]']->upload($filePath);
            $form['application[files][2]']->upload($filePath);
            $form['application[files][3]']->upload($filePath);
        }

        $client->submit($form);
        $client->catchExceptions(false);
    }
    */


    public function testCreateJobWithImage(): void
    {
        $client = $this->authAsRecruter();
        $container = static::getContainer();

        $typeRepository = $container->get(TypeRepository::class);
        $type = $typeRepository->findOneBy(['name' => 'CDI']);

        $categoryRepository = $container->get(CategoryRepository::class);
        $category = $categoryRepository->findOneBy(['name' => 'Autre']);

        $crawler =   $client->request('GET', '/job/new');
        $buttonCrawlerNode = $crawler->selectButton('Save');
        $form = $buttonCrawlerNode->form();
        $form['job[name]'] = "Test job with image";
        $form['job[company]'] = "Test company";
        $form['job[type]'] = $type->getId();
        $form['job[category]'] = $category->getId();
        $form['job[description]'] = 'Test description';

        $filePath = 'public' . DIRECTORY_SEPARATOR .
            'uploads' . DIRECTORY_SEPARATOR .
            'tests' . DIRECTORY_SEPARATOR .
            'ImageTestCase.png';

        if (file_exists($filePath)) {
            $form['job[companyImage]']->upload($filePath);
        }

        $client->submit($form);
        $this->assertResponseRedirects('/my-jobs');

        $client->request('GET', '/job/test_job_with_image_test_company');
        $this->assertSelectorExists('img');
    }
}
