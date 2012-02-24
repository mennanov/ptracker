<?php

namespace Ptracker\AuthBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    // is login form ok?
    public function testLoginForm() {
        $client = static::createClient();
        // looking for inputs
        $crawler = $client->request('GET', '/login');
        $assert = $crawler->filter('form')->filter('#username')->count() > 0 && $crawler->filter('form')->filter('#password')->count() > 0;
        $this->assertTrue($assert);
    }

    // login form test (incorrect/empty credentials)
    public function testLoginFormCredentialsIncorrect() {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        // incorrect
        $form['_username'] = 'lucas';
        $form['_password'] = 'ping-pong';

        $crawler = $client->submit($form);
        // looking for error message
        $assert = $crawler->filter('html:contains("Error")')->count() > 0;
        // empty 
        $form = $crawler->selectButton('submit')->form();
        // incorrect
        $form['_username'] = '';
        $form['_password'] = '';

        $crawler = $client->submit($form);
        $assert = $crawler->filter('html:contains("Error")')->count() > 0 && $assert;
        $this->assertTrue($assert);
    }

    // login form validation test (correct credentials)
    public function testLoginFormCredentialsCorrect() {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        // incorrect
        $form['_username'] = 'mennanov';
        $form['_password'] = '2232';

        $crawler = $client->submit($form);
        // looking for content in /tasks page
        $assert = $crawler->filter('html:contains("Tasks list")')->count() > 0;
        $this->assertTrue($assert);
    }

    // is registration form ok?
    public function testRegistrationForm() {
        $client = static::createClient();
        // looking for inputs
        $crawler = $client->request('GET', '/register');
        $assert = $crawler->filter('form')->filter('#form_username')->count() > 0
                && $crawler->filter('form')->filter('#form_password')->count() > 0
                && $crawler->filter('form')->filter('#form_name')->count() > 0
                && $crawler->filter('form')->filter('#form_email')->count() > 0;
        $this->assertTrue($assert);
    }
    
    // login form test (incorrect/empty credentials)
    public function testRegistationFormCredentialsIncorrect() {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('submit')->form();
        // incorrect
        $form['form[username]'] = 'вася'; // incorrect username
        $form['form[name]'] = 'Вася';
        $form['form[email]'] = '??33-1-2*&^%vasya@mail.ru'; // incorrect email
        $form['form[password]'] = ''; // empty password

        $crawler = $client->submit($form);

        $assert = $crawler->filter('#success')->count() == 0;
        $this->assertTrue($assert);
    }
    
    // registration form test - create new user (correct credentials - works only at once)
    public function testRegistationFormCredentialsCorrect() {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('submit')->form();
        // incorrect
        $form['form[username]'] = 'petr'; // incorrect username
        $form['form[name]'] = 'Петр';
        $form['form[email]'] = 'petro@mail.ru'; // incorrect email
        $form['form[password]'] = '1111423'; // empty password

        $crawler = $client->submit($form);

        $assert = $crawler->filter('#success')->count() > 0;
        $this->assertTrue($assert);
    }
    
    // user activation (incorrect salt or already activated user)
    public function testUserActivationIncorrect() {
        $client = static::createClient();
        // looking for inputs
        $crawler = $client->request('GET', "/activate/igor/didskf2342esw2lkj");
        $assert = $crawler->filter('html:contains("Account activation error")')->count() > 0;
        $this->assertTrue($assert);
    }
    
    // user activation (correct data - works only at once!)
    public function testUserActivationCorrect() {
        $client = static::createClient();
        $client->followRedirects(true);
        // looking for inputs
        $crawler = $client->request('GET', "/activate/igor/7tmx14ut544kkw8os0cwkgsk00ww4cc");
        $assert = $crawler->filter('html:contains("Your account was just activated")')->count() > 0;
        $this->assertTrue($assert);
    }

}
