<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

class UserTest extends WebTestCase
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


    public function testRegisterAsCandidat(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertSelectorTextContains('body', 'As candidat');

        $client->request('GET', '/register/candidat');
        $client->submitForm('Register', [
            'registration_candidat_form[email]' => 'testCandidat@yahoo.fr',
            'registration_candidat_form[password][first]' => 'aRegularPassword',
            'registration_candidat_form[password][second]' => 'aRegularPassword'
        ]);
        $this->assertResponseRedirects('/profil');
        $client->followRedirect();
        $this->assertSelectorTextContains('.title-section', 'My profile');
    }


    public function testRegisterAsRecruter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertSelectorTextContains('body', 'As recruter');

        $client->request('GET', '/register/recruter');
        $client->submitForm('Register', [
            'registration_recruter_form[email]' => 'testRecruter@yahoo.fr',
            'registration_recruter_form[password][first]' => 'aRegularPassword',
            'registration_recruter_form[password][second]' => 'aRegularPassword',
            'registration_recruter_form[company]' => 'TestCompany'
        ]);
        $this->assertResponseRedirects('/profil');
        $client->followRedirect();
        $this->assertSelectorTextContains('.title-section', 'My profile');
    }


    public function testProfilAsAnonymous(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil');

        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        $client->request('GET', '/profil/edit');

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
    }


    public function testEditProfilCandidat(): void
    {
        $client = $this->authAsCandidat();
        $client->request('GET', '/profil');

        $this->assertSelectorTextNotContains('.profil-info', 'Test');
        $this->assertSelectorTextContains('.btn', 'Edit my profile');
        $client->clickLink('Edit my profile');

        $this->assertResponseIsSuccessful();
        $client->submitForm('Save', [
            'candidat[name]' => 'Test'
        ]);

        $this->assertResponseRedirects('/profil');
        $client->followRedirect();

        $this->assertSelectorTextContains('.profil-info', 'Test');
    }


    public function testEditProfilRecruter(): void
    {
        $client = $this->authAsRecruter();
        $client->request('GET', '/profil');

        $this->assertSelectorTextNotContains('.profil-info', 'Test');
        $this->assertSelectorTextContains('.btn', 'Edit my profile');
        $client->clickLink('Edit my profile');

        $this->assertResponseIsSuccessful();
        $client->submitForm('Save', [
            'recruter[company]' => 'Test'
        ]);

        $this->assertResponseRedirects('/profil');
        $client->followRedirect();

        $this->assertSelectorTextContains('.profil-info', 'Test');
    }


    public function testEailWhenRegisterAsCandidat(): void {
        $client = static::createClient();
        $client->request('GET', '/register/candidat');
        $client->submitForm('Register', [
            'registration_candidat_form[email]' => 'testCandidatEmail@yahoo.fr',
            'registration_candidat_form[password][first]' => 'aRegularPassword',
            'registration_candidat_form[password][second]' => 'aRegularPassword'
        ]);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($email, 'To', 'testCandidatEmail@yahoo.fr');
        $this->assertEmailHeaderSame($email, 'Subject', 'Thanks for signing up!');
        $this->assertEmailHeaderSame($email, 'Sender', 'jobinator-renew@gmail.com');
    }


    public function testEailWhenRegisterAsRecruter(): void {
        $client = static::createClient();
        $client->request('GET', '/register/recruter');
        $client->submitForm('Register', [
            'registration_recruter_form[email]' => 'testRecruterEmail@yahoo.fr',
            'registration_recruter_form[password][first]' => 'aRegularPassword',
            'registration_recruter_form[password][second]' => 'aRegularPassword',
            'registration_recruter_form[company]' => 'TestCompany'
        ]);

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage(0);

        $this->assertEmailHeaderSame($email, 'To', 'testRecruterEmail@yahoo.fr');
        $this->assertEmailHeaderSame($email, 'Subject', 'Thanks for signing up!');
        $this->assertEmailHeaderSame($email, 'Sender', 'jobinator-renew@gmail.com');
    }
}
