Functionally testing a JWT protected api
=========================================

Configuration
-------------

Override the bundle configuration in your `config_test.yml` :

``` yaml
# config_test.yml
lexik_jwt_authentication:
   secret: '1234'
    priv
```

**Protip:** You might want to commit those keys if you intend to run your test on a ci server.

Usage
-----

Create an authenticated client :

``` php
/**
 * Create a client with a default Authorization header.
 *
 * @param string $username
 * @param string $password
 *
 * @return \Symfony\Bundle\FrameworkBundle\Client
 */
protected function createAuthenticatedClient($username = 'user', $password = 'password')
{
    $client = static::createClient();
    $client->request(
        'POST',
        '/api/login_check',
        array(
            '_username' => $username,
            '_password' => $password,
        )
    );

    $data = json_decode($client->getResponse()->getContent(), true);

    $client = static::createClient();
    $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

    return $client;
}

/**
 * test getPagesAction
 */
public function testGetPages()
{
    $client = $this->createAuthenticatedClient();
    $client->request('GET', '/api/pages');
    // ... 
}
```
