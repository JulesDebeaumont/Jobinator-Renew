<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
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
}
