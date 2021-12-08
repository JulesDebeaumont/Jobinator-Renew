# Jobinator
The job seeker app where companies respond to every applicants! 🤔  
All images found on https://unsplash.com (copyright free)

By *Jules Debeaumont*

  
### 🔧 1-Installation 

- Do a `git init`
- Clone the project
- Do a `composer install`
- Do a `npm install`


  
### ⚙ 2-Configure the Database

- Log as admin on your localhost/phpmyadmin
- Make a new user for the Symfony5 access
- Copy the .env file into .env.local: `cp .env .env.local`
- Add .env.local to gitignore file
- In the .env.local:

*These settings are suggestions, put whatever you want:*  
User: `Jobinator`  
Password: `1234`

- Once done, edit the .env.local you made
- Make sure the database access looks like the following one:

`DATABASE_URL="mysql://Jobinator:1234@127.0.0.1:3306/Jobinator?serverVersion=5.7"`

*If using other Database (aka MariaDB, SQLite etc...), make sure to change the server version!*


  
### 🚀 3-Launch the server

Do a `symfony serve`  

If you don't have Symfony installed, go for this command instead: `php -S 127.0.0.1:8000 -t public`


  
### 📎 4-Misc

- Reset DB, remove uploaded files and load fixtures: `composer reset`
- To check the 404 Error Page, go to `your-localhost-here/index.php/_error/404`
- Amazing reference: https://slides-symfony-iut.netlify.app


  
### 🚧 5-To do

- Finir crud Jobs ✔
- Add all form errors ✔
- Fix Voters ✔
- Already applied constraint ✔
- Redirect from /home to / ✔
- Mailer inscription ✔ 
- Table file pour candidature ✔
- PDF avec Vich ✔
- Application form ✔
- Show candidat count on my_jobs + link if count > 0 ✔
- Voter job extend ✔ 
- Success applied page ✔
- Logique fixture apply only once ✔
- File max size + type ✔
- Delete files when deleting job/user ✔
- Show files on application/id ✔
- Delete uploaded files with composer reset script ✔
- Service fileUpload ✔
- Add attribute to fileApplication for original filename + extension ✔
- MailSender service ✔
- Mailer candidature (candidat + recruter) ✔
- Page de recherche ✔
- Paginator ✔
- Fix all <label> props for forms ✔
- Voter application show ✔
- https://symfony.com/doc/current/controller.html#managing-errors-and-404-pages
- Test register + edit profile for both users
- Test login recruter -> new job
- Tests login candidat -> apply
- Test new job -> look for it in search page
- Test create job recruter -> apply as candidat with 
- Test emails
- Cron delete job after 6 months
- Test register + mail
- Slug dans URL
- Slug filenames
- Fixture with files
- Commande showing number of job in the database
- Commande for sending a mail to all candidat for fun
- My_application/job by createdAt/UpdateAt order
- Apply as anonymous
- Table last research linked to candidat
- Event with research -> add to last research if user is logged, max 5
- Better emails style
- Change password
- Reset password mailer
- Fix Job counts for Recruter ✔
- Fix See Candidat count on jobs
- Message table ?
- EasyAdmin
- Image table for Job
- Recruter must answer!