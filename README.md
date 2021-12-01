# Jobinator
The job seeker app where companies respond to every applicants! 🤔
All images found on https://unsplash.com (copyright free)

By *Jules Debeaumont*


### 🔧 1_Installation 

- Do a `git init`
- Clone the project
- Do a `composer install`
- Do a `npm install`



### ⚙ 2_Configure the Database:

- Log as admin on your localhost/phpmyadmin
- Make a new user for the Symfony5 access
- Copy the .env file `cp .env .env.local`
- Add .env.local to gitignore file
- In the .env.local:

*These settings are suggestions, put whatever you want:*  
User: `Jobinator`  
Password: `1234`

- Once done, edit the .env.local you made
- Make sure the database access looks like the following one:

`DATABASE_URL="mysql://Jobinator:1234@127.0.0.1:3306/Jobinator?serverVersion=5.7"`

*If using other Database (aka MariaDB, SQLite etc...), make sure to change the server version!*


### 🎬 3_Launch the server:

Do a `symfony serve`

*If you encounter trouble with this command, you may try this one (mostly Windows issue): 
`php -S 127.0.0.1:8000 -t public`


### 📎 4_Misc:

- Reset DB and load fixtures `composer reset`
- To check the 404 Error Page, go to `your-localhost-here/index.php/_error/404`
- Amazing reference: https://slides-symfony-iut.netlify.app

TODO
-> Finir crud Jobs ✔
-> Add all form errors ✔
-> Fix Voters ✔
-> Already applied constraint ✔
-> Redirect from /home to / ✔
-> Mailer inscription ✔ 
-> Table file pour candidature ✔
-> PDF avec Vich ✔
-> Application form
-> File max size + type
-> Reactive forms pour department region pays
-> Mailer candidature (candidat + recruter)
-> Page de recherche
-> fix all <lavel> for props for forms
-> Test login recruter -> new job
-> Tests login candidat -> apply
-> Test register + mail
-> Slug dans URL
-> Commandes
-> Apply as anonymous
-> Better emails style
-> Change password
-> Reset password mailer
-> Fix Job counts for Recruter
-> EasyAdmin
-> Image table for Job