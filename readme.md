# Setup project

Ensure that you have `docker`  and `docker-compose`

1. Build docker images `docker-compose build`
2. Fetch dependencies `docker-compose run --rm web composer install`.
3. Create DB `docker-compose run --rm web php bin/console doctrine:schema:create`.
4. Set Messenger `docker-compose run --rm web php bin/console messenger:setup-transports`.
5. Run containers & services `docker-compose up -d`.
6. Set local host: `app-manager.local`
7. Try: http://app-manager.local/file/

# Run tests
`docker-compose run --rm web php bin/phpunit`

#Tips & tricks

It's more handy to run command inside docker container, to go in run:

`docker-compose exec web bash`
