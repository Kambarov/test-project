This is the test project for note management system. Everyone can create a note but only owner can edit and delete the note. When the note created every user receives a mail about new note in the system. 

To run the project, please follow these steps:

* composer install

copy the .env.example and save as .env and .env.testing

set the credentials for sql and mail

* php artisan key:generate
* php artisan migrate
* php artisan queue:listen (to run the queue)

In order to run the tests, run following command:

* php artisan test --env=testing

As the project is only for REST API, here is the documentation link

https://documenter.getpostman.com/view/10763903/2s8YRnmX5d
